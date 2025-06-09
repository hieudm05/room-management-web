<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Facility;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomPhoto;
use App\Models\Landlord\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\TemplateProcessor;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['facilities', 'property', 'photos', 'services'])
            ->withCount('facilities')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('landlord.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $properties = Property::all();
        $facilities = Facility::all();
        $services = Service::all();

        return view('landlord.rooms.create', compact('facilities', 'properties', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,property_id',
            'room_number' => 'required|string|max:50',
            'area' => 'required|numeric|min:1',
            'rental_price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Hidden,Suspended,Confirmed',
            'facilities' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'occupants' => 'required|integer|min:0',
        ]);

        $services = $request->input('services', []);
        $errors = [];

        if (isset($services[3]['enabled']) && empty($services[3]['price'])) {
            $errors['services.3.price'] = 'Bạn phải nhập giá Wifi.';
        }
        if (isset($services[2]['enabled']) && empty($services[2]['price'])) {
            $errors['services.2.price'] = 'Bạn phải nhập giá Nước.';
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        if (Room::where('property_id', $request->property_id)->where('room_number', $request->room_number)->exists()) {
            return back()->withErrors(['room_number' => 'Phòng này đã tồn tại trong khu.'])->withInput();
        }

        $requestData = $request->only(['property_id', 'room_number', 'area', 'rental_price', 'status', 'occupants']);
        $requestData['created_by'] = Auth::id();
        $room = Room::create($requestData);

        $room->facilities()->sync($request->facilities ?? []);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('uploads/rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->room_id,
                    'image_url' => '/storage/' . $path,
                ]);
            }
        }

        if (!empty($services)) {
            $serviceData = [];
            foreach ($services as $serviceId => $data) {
                if (isset($data['enabled'])) {
                    $serviceData[$serviceId] = [
                        'is_free' => empty($data['price']),
                        'price' => $data['price'] ?? null,
                        'unit' => $data['unit'] ?? null,
                    ];
                }
            }
            $room->services()->sync($serviceData);
        }

        $room->load('property', 'facilities', 'services');
        $landlord = $this->getCurrentUserAsLandlord();

        $this->generateContractPDF($room, $landlord);
        $this->generateContractWord($room, $landlord);

        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Tạo phòng thành công.');
    }

    public function show(Room $room)
    {
        $room->load('property', 'facilities', 'photos', 'services');
        return view('landlord.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $facilities = Facility::all();
        $services = Service::all();
        $roomFacilities = $room->facilities->pluck('facility_id')->toArray();
        $roomServices = $room->services->mapWithKeys(function ($service) {
            return [
                $service->service_id => [
                    'is_free' => $service->pivot->is_free,
                    'price' => $service->pivot->price,
                    'unit' => $service->pivot->unit,
                ]
            ];
        })->toArray();

        return view('landlord.rooms.edit', compact('room', 'facilities', 'roomFacilities', 'services', 'roomServices'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'area' => 'required|numeric|min:1',
            'rental_price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Hidden,Suspended,Confirmed',
            'facilities' => 'array',
            'photos.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'delete_photos' => 'array',
            'delete_photos.*' => 'integer|exists:room_photos,photo_id',
            'occupants' => 'required|integer|min:0',
        ]);

        $room->update($request->only(['area', 'rental_price', 'status', 'occupants']));
        $room->facilities()->sync($request->facilities ?? []);

        if ($request->has('delete_photos')) {
            $photosToDelete = RoomPhoto::whereIn('photo_id', $request->delete_photos)->get();
            foreach ($photosToDelete as $photo) {
                $path = str_replace('/storage/', '', $photo->image_url);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
                $photo->delete();
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('uploads/rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->room_id,
                    'image_url' => '/storage/' . $path,
                ]);
            }
        }

        $services = $request->input('services', []);
        if (!empty($services)) {
            $serviceData = [];
            foreach ($services as $serviceId => $data) {
                if (isset($data['enabled'])) {
                    $serviceData[$serviceId] = [
                        'is_free' => empty($data['price']),
                        'price' => $data['price'] ?? null,
                        'unit' => $data['unit'] ?? null,
                    ];
                }
            }
            $room->services()->sync($serviceData);
        } else {
            $room->services()->detach();
        }

        $room->load('property', 'facilities', 'services');
        $landlord = $this->getCurrentUserAsLandlord();

        $this->generateContractPDF($room, $landlord);
        $this->generateContractWord($room, $landlord);

        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Cập nhật phòng thành công!');
    }

    public function downloadContractWord(Room $room)
    {
        $path = storage_path('app/public/' . $room->contract_word_file);
        if (!file_exists($path)) {
            return back()->withErrors(['contract' => 'Không tìm thấy file hợp đồng Word.']);
        }
        return Response::download($path, basename($path));
    }

    public function downloadContract(Room $room)
    {
        $path = storage_path('app/public/' . $room->contract_pdf_file);
        if (!file_exists($path)) {
            return back()->withErrors(['contract' => 'Không tìm thấy file hợp đồng PDF.']);
        }
        return Response::download($path, 'hop_dong_' . $room->room_number . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function streamContract(Room $room)
    {
        $path = storage_path('app/public/' . $room->contract_pdf_file);
        if (!file_exists($path)) {
            return back()->withErrors(['contract' => 'Không tìm thấy file hợp đồng PDF.']);
        }
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function generateContractPDF(Room $room, $landlord)
    {
        try {
            $pdf = Pdf::loadView('landlord.rooms.pdf.Contracter', compact('room', 'landlord'));
            Storage::disk('public')->put("contracts/contract_room_{$room->room_id}.pdf", $pdf->output());
            $room->update(['contract_pdf_file' => "contracts/contract_room_{$room->room_id}.pdf"]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo PDF: ' . $e->getMessage());
        }
    }

    private function generateContractWord(Room $room, $landlord, $tenant = null)
    {
        $templatePath = resource_path('contracts/contract_template.docx');
        if (!file_exists($templatePath)) {
            Log::error('Mẫu Word không tồn tại.');
            return;
        }

        $room->load('facilities', 'services');
        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValue('CHU_TRO', $landlord->name);
        $templateProcessor->setValue('SDT_CHU_TRO', $landlord->phone_number);
        $templateProcessor->setValue('CCCD_CHU_TRO', $landlord->identity_number);
        $templateProcessor->setValue('SO_PHONG', $room->room_number);
        $templateProcessor->setValue('DIEN_TICH', $room->area);
        $templateProcessor->setValue('GIA_THUE', number_format($room->rental_price));
        $templateProcessor->setValue('TEN_NGUOI_THUE', $tenant->name ?? '......................................');
        $templateProcessor->setValue('SDT_NGUOI_THUE', $tenant->phone ?? '......................................');
        $templateProcessor->setValue('CCCD_NGUOI_THUE', $tenant->cccd ?? '......................................');
        $templateProcessor->setValue('TIEN_NGHI', implode(', ', $room->facilities->pluck('name')->toArray()));

        $dichVu = '';
        foreach ($room->services as $service) {
            $unitLabel = match($service->service_id) {
                1 => 'số',
                2 => $service->pivot->unit === 'per_m3' ? 'm³' : 'người',
                3 => $service->pivot->unit === 'per_room' ? 'phòng' : 'người',
                default => 'phòng',
            };
            $dichVu .= '- ' . $service->name . ': ' . ($service->pivot->is_free
                ? 'Miễn phí'
                : number_format($service->pivot->price) . ' VNĐ/' . $unitLabel) . "\n";
        }
        $templateProcessor->setValue('DICH_VU', trim($dichVu));

        $filename = "contract_room_{$room->room_id}.docx";
        $templateProcessor->saveAs(storage_path("app/public/contracts/{$filename}"));
        $room->update(['contract_word_file' => "contracts/{$filename}"]);
    }

    public function hide(Room $room)
    {
        $room->update(['status' => 'Hidden']);
        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Bạn đã ẩn phòng thành công!');
    }

    public function destroy(Room $room)
    {
        if ($room->rentalAgreements()->exists()) {
            return back()->withErrors(['delete' => 'Không thể xóa phòng có hợp đồng thuê.']);
        }
        $room->facilities()->detach();
        $room->delete();
        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Phòng đã được xóa thành công!');
    }

    private function getCurrentUserAsLandlord()
    {
        $user = Auth::user();
        return (object)[
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'identity_number' => $user->identity_number,
        ];
    }
}
