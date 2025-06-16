<?php

namespace App\Http\Controllers\Landlord;

use PhpOffice\PhpWord\IOFactory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Landlord\Facility;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomPhoto;
use App\Models\Landlord\Service;
use App\Models\RentalAgreement;
use App\Models\RoomUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\Writer\HTML;
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
        $templateProcessor->setValue('EMAIL_NGUOI_THUE', $tenant->email ?? '......................................');
        $templateProcessor->setValue('SO_LUONG_NGUOI_O', $tenant->people_renter ?? '......................................');
        $templateProcessor->setValue('SO_LUONG_NGUOI_TOI_DA', $room->occupants ?? '......................................');
        $templateProcessor->setValue('TIEN_NGHI', implode(', ', $room->facilities->pluck('name')->toArray()));

        $dichVu = '';
        foreach ($room->services as $service) {
            $unitLabel = match ($service->service_id) {
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
        return (object) [
            'name' => $user->name,
            'phone_number' => $user->phone_number,
            'identity_number' => $user->identity_number,
        ];
    }
    public function show2(Room $room)
    {
        $room->load('property', 'facilities', 'photos', 'services');
        return view('home.show2', compact('room'));
    }
    public function previewContract(Request $request, Room $room)
    { 

        $request->validate([
            'contract_word_file' => 'required|mimes:doc,docx|max:2048',
        ]);

        $file = $request->file('contract_word_file');
        $tempPath = $file->storeAs('temp', uniqid() . '.' . $file->getClientOriginalExtension(), 'public');

        $phpWord = IOFactory::load(storage_path('app/public/' . $tempPath));
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
        // dd($text);
        // ✨ Tách thông tin người thuê
        preg_match('/Họ tên:\s*(.*)/i', $text, $nameMatch);
    preg_match('/Email:\s*([^\s]+)/i', $text, $emailMatch);
    preg_match('/Số lượng người ở\s*:\s*([0-9]+)/i', $text, $peopleMatch);
    preg_match('/Số lượng người ở tối đa\s*:\s*([0-9]+)/i',$text, $maxPeopleMatch);

    $tenantName = trim($nameMatch[1] ?? '');
    $tenantEmail = trim($emailMatch[1] ?? '');
    $numberOfPeople = trim($peopleMatch[1] ?? '');
    $maxNumberOfPeople = trim($maxPeopleMatch[1] ?? '');

    return view('home.preview_contract', [
        'room' => $room,
        'word_content' => $text,
        'temp_path' => $tempPath,
        'tenant_name' => $tenantName,
        'tenant_email' => $tenantEmail,
        'number_of_people' => $numberOfPeople,
        'max_number_of_people' => $maxNumberOfPeople,
        ]);
    }

    public function confirmContract(Request $request, Room $room)
    {
        $user = Auth::user();
        $tempPath = $request->input('temp_path');

        // 1. Di chuyển file
        $newPath = 'contracts/word/' . basename($tempPath);
        Storage::disk('public')->move($tempPath, $newPath);

        // 2. Tạo mới hợp đồng
        $agreement = new RentalAgreement();
        $agreement->room_id = $room->room_id;
        $agreement->renter_id = $user->id;
        $agreement->status = 'Pending';
        $agreement->start_date = now();
        $agreement->end_date = now()->addMonths(12);
        $agreement->contract_file = $newPath;
        $agreement->save(); // Lúc này $agreement->id đã có
        // 3. Cập nhật lại thông tin phòng
        $room->id_rental_agreements = $agreement->rental_id;
        $room->people_renter = $request->input('number_of_people', 0);
        $room->occupants = $request->input('max_number_of_people', 0);
        $room->save();

        return redirect()->route('show2', $room)->with('success', 'Hợp đồng mới đã được tạo và phòng đã được cập nhật!');
    }

   public function formShowContract(Request $request)
{

    $roomId = $request->input('room_id');
    $rental_id = $request->input('rental_agreement_id');
    $rentalAgreement = RentalAgreement::find($rental_id);
    $roomUsers = RoomUser::where('rental_id', $rental_id)
                     ->where('room_id', $roomId)
                     ->get(); 
   
    if (!$rentalAgreement) {
        return view('landlord.contract.index', [
            'roomUsers' => $roomUsers,
            'rentalAgreement' => null,
            'wordText' => '',
            'tenant_name' => '',
            'tenant_email' => '',
            'rental_id' => $rental_id,
            'room' => null
        ]);
    }

    $contractPath = $rentalAgreement->contract_file;
    $fullPath = storage_path('app/public/' . $contractPath);

    if (!$contractPath || !file_exists($fullPath)) {
        return view('landlord.contract.index', [
            'roomUsers' => $roomUsers,
            'rentalAgreement' => $rentalAgreement,
            'wordText' => '',
            'tenant_name' => '',
            'tenant_email' => '',
            'rental_id' => $rental_id,
            'room' => $rentalAgreement->room ?? null
        ]);
    }

    // Đọc file Word
    $text = '';
    try {
        $phpWord = IOFactory::load($fullPath);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
    } catch (\Exception $e) {
        $text = 'Không thể đọc file Word: ' . $e->getMessage();
    }

    // Trích thông tin
    preg_match('/Họ tên:\s*(.*)/i', $text, $nameMatch);
    preg_match('/Email:\s*([^\s]+)/i', $text, $emailMatch);

    return view('landlord.contract.index', [
        'roomUsers' => $roomUsers,
        'rentalAgreement' => $rentalAgreement,
        'wordText' => $text,
        'tenant_name' => trim($nameMatch[1] ?? ''),
        'tenant_email' => trim($emailMatch[1] ?? ''),
        'rental_id' => $rental_id,
        'room' => $rentalAgreement->room ?? null
    ]);
}


    public function confirmStatusRentalAgreement(Request $request)
    {
     
        // 1. Lấy thông tin từ request
        $rentalId = $request->input('rental_id');
        $tenantName = $request->input('tenant_name');
        $tenantEmail = $request->input('tenant_email');
        $occupants = $request->input('occupants', 0);
        $people_renter = $request->input('people_renter', 0);

        // 2. Tìm hợp đồng và cập nhật trạng thái
        $rental = RentalAgreement::findOrFail($rentalId);
        $rental->status = 'Active'; // hoặc 'Active' tùy theo bạn định nghĩa
        $rental->save();

        // 3. Cập nhật phòng tương ứng thành 'Rented'
        $room = Room::findOrFail($rental->room_id);
        $room->status = 'Rented';
        $room->save();
         if ($people_renter > $occupants) {
            return back()->withErrors(['people_renter' => 'Số lượng người ở không được lớn hơn số lượng người tối đa của phòng.']);
        }
        // 4. Kiểm tra email đã tồn tại chưa
        $existingUser = User::where('email', $tenantEmail)->first();
        if (!$existingUser) {
            // Tạo mật khẩu ngẫu nhiên
            $password = Str::random(8);
            $hashedPassword = Hash::make($password);
            

            // Tạo user
            $user = new User();
            $user->name = $tenantName;
            $user->email = $tenantEmail;
            $user->password = $hashedPassword;
            $user->role = 'renter'; // nếu bạn có cột role
            $user->save();
            // Cập nhật thông tin người thuê trong hợp đồng
            $user_id = $user->id;
            $rental->renter_id = $user_id;
            $rental->save();

            // Gửi email thông báo
            Mail::raw("
            Chào $tenantName,

            Tài khoản của bạn đã được tạo:
            Email: $tenantEmail
            Mật khẩu: $password

            Vui lòng đăng nhập và thay đổi mật khẩu sau lần đăng nhập đầu tiên.

            Trân trọng,
            Hệ thống quản lý phòng trọ
        ", function ($message) use ($tenantEmail) {
                $message->to($tenantEmail)
                    ->subject('Tài khoản thuê phòng đã được tạo');
            });
        }

        return back()->with('success', 'Hợp đồng đã xác nhận và tài khoản người thuê đã được xử lý.');
    }
     public function ConfirmAllUser(Request $request){
      $userId = $request->input('user_id');
      $rentalId = $request->input('rental_id');
          
      
        // 4. Kiểm tra email đã tồn tại chưa
        $user= User::findOrFail($userId);
        $tenantName = $user->name;
        $tenantEmail = $user->email;
        $existingUser = User::where('email', $tenantEmail)->first();
        if (!$existingUser) {
            // Tạo mật khẩu ngẫu nhiên
            $password = Str::random(8);
            $hashedPassword = Hash::make($password);
            

            // Tạo user
            $user = new User();
            $user->name = $tenantName;
            $user->email = $tenantEmail;
            $user->password = $hashedPassword;
            $user->role = 'renter'; // nếu bạn có cột role
            $user->save();
         
        

            // Gửi email thông báo
            Mail::raw("
            Chào $tenantName,

            Tài khoản của bạn đã được tạo:
            Email: $tenantEmail
            Mật khẩu: $password

            Vui lòng đăng nhập và thay đổi mật khẩu sau lần đăng nhập đầu tiên.

            Trân trọng,
            Hệ thống quản lý phòng trọ
        ", function ($message) use ($tenantEmail) {
                $message->to($tenantEmail)
                    ->subject('Tài khoản thuê phòng đã được tạo');
            });
        }

        return back()->with('success', 'Hợp đồng đã xác nhận và tài khoản người thuê đã được xử lý.');
     }
}
