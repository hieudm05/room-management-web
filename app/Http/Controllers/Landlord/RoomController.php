<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Mail\RoomJoinSuccessNotification;
use App\Mail\RoomUpdatedNotification;
use App\Mail\TenantMovedNotification;
use App\Models\Landlord\Facility;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomEditRequest;
use App\Models\Landlord\RoomPhoto;
use App\Models\Landlord\Service;
use App\Models\RentalAgreement;
use App\Models\RoomUser;
use App\Models\User;
use App\Models\UserInfo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\HTML;

use Smalot\PdfParser\Parser;




class RoomController extends Controller
{
    public function index(Request $request)
    {
        // 1️⃣ Truy vấn cơ bản với eager load
        $query = Room::with([
            'facilities',
            'property',
            'photos',
            'services',
            'staffs',
            'rentalAgreements',
        ])->orderBy('created_at', 'desc');

        // 2️⃣ Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('property', fn($q2) => $q2->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('facilities', fn($q3) => $q3->where('name', 'LIKE', "%{$search}%"))
                    ->orWhere('rental_price', 'LIKE', "%{$search}%")
                    ->orWhere('area', 'LIKE', "%{$search}%");
            });
        }

        // 3️⃣ Lọc theo khu trọ
        if ($propertyId = $request->input('property_id')) {
            $query->where('property_id', $propertyId);
        }

        // 4️⃣ Lọc theo giá cố định từ dropdown
        if ($range = $request->input('price_range')) {
            if (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                $query->whereBetween('rental_price', [(int)$min, (int)$max]);
            } elseif (is_numeric($range)) {
                $query->where('rental_price', '>', (int)$range);
            }
        }

        // 5️⃣ Lọc theo giá nhập tay
        if ($request->filled('price_min')) {
            $query->where('rental_price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('rental_price', '<=', $request->input('price_max'));
        }

        // 6️⃣ Lấy danh sách phòng phân trang
        $rooms = $query->paginate(8);

        // 7️⃣ Gán flag kiểm tra hợp đồng Active/Signed
        $rooms->load('rentalAgreements'); // tránh n+1 query
        $rooms->each(fn($room) => $room->currentAgreementValidFlag = $room->currentAgreementValid ? true : false);

        // 8️⃣ Lấy tất cả khu trọ để filter
        $allProperties = Property::all();

        // 9️⃣ Lấy danh sách phòng trống để chuyển phòng
        $availableRooms = Room::where('is_contract_locked', false)
            ->whereDoesntHave('rentalAgreements', function ($q) {
                $q->whereIn('status', [RentalAgreement::STATUS_ACTIVE, RentalAgreement::STATUS_SIGNED]);
            })
            ->get();

        // 10️⃣ Trả về view
        return view('landlord.rooms.index', compact('rooms', 'allProperties', 'availableRooms'));
    }


    public function create()
    {
        $properties = Property::all();
        $facilities = Facility::where('name', '!=', 'Thang máy')->get();
        $services = Service::where('is_hidden', false)->get();

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
            'deposit_price' => 'nullable|numeric|min:0',
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

        $requestData = $request->only(['property_id', 'room_number', 'area', 'rental_price', 'status', 'occupants', 'deposit_price']);
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

        // 👉 Lấy người thuê mới nhất từ hợp đồng (nếu có)
        $agreement = $room->rentalAgreements()->latest()->first();
        $tenant = $agreement?->renter;

        $this->generateContractPDF($room, $landlord);
        $this->generateContractWord($room, $landlord, $tenant);

        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Tạo phòng thành công.');
    }

    public function show(Room $room)
    {
        $room->load([
            'property',
            'facilities',
            'photos',
            'services',
            'roomUsers.user',
            'currentAgreement.renter.info',
        ]);

        // Gán renter từ hợp đồng hiện tại
        $room->renter = $room->currentAgreement?->renter;

        // Gán cờ kiểm tra hợp đồng hiện tại có hiệu lực hay không
        $status = $room->currentAgreement->status ?? null;
        $room->currentAgreementValid = in_array($status, ['Active', 'Signed']);

        return view('landlord.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $facilities = Facility::where('name', '!=', 'Thang máy')->get();
        $services = Service::where('is_hidden', false)->get();
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
            'deposit_price' => 'nullable|numeric|min:0',
        ]);

        // Nếu là nhân viên thì gửi yêu cầu
        if (auth()->user()->role === 'Staff') {
            $original = $room->only(['area', 'rental_price', 'status', 'occupants', 'deposit_price']);
            $requested = $request->only(['area', 'rental_price', 'status', 'occupants', 'deposit_price']);

            $diff = array_diff_assoc($requested, $original);
            if (empty($diff)) {
                return back()->with('info', 'Bạn chưa thay đổi thông tin nào.');
            }

            RoomEditRequest::create([
                'room_id' => $room->room_id,
                'staff_id' => auth()->id(),
                'original_data' => json_encode($original),
                'requested_data' => json_encode($requested),
                'status' => 'pending',
            ]);

            $room->increment('edit_count');

            return redirect()->route('staff.index')->with('success', 'Yêu cầu sửa đã được gửi, chờ chủ trọ duyệt.');
        }

        // 🔁 Lưu lại dữ liệu gốc trước khi cập nhật
        $originalValues = $room->getOriginal();

        // ✅ Cập nhật dữ liệu
        $room->update($request->only(['area', 'rental_price', 'status', 'occupants', 'deposit_price']));

        // Đếm số lần thay đổi giá
        if ($originalValues['rental_price'] != $room->rental_price) {
            $room->increment('price_edit_count');
        }
        if ($originalValues['deposit_price'] != $room->deposit_price) {
            $room->increment('deposit_edit_count');
        }

        $room->facilities()->sync($request->facilities ?? []);

        // Xoá ảnh
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

        // Thêm ảnh mới
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo->isValid()) {
                    $path = $photo->store('uploads/rooms', 'public');
                    RoomPhoto::create([
                        'room_id' => $room->room_id,
                        'image_url' => '/storage/' . $path,
                    ]);
                }
            }
        }

        // Dịch vụ
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

        // Load lại quan hệ
        $room->load('property', 'facilities', 'services');
        $room->refresh();

        $landlord = $this->getCurrentUserAsLandlord();
        $agreement = $room->rentalAgreements()->latest()->first();
        $tenant = $agreement?->renter;

        $this->generateContractPDF($room, $landlord);
        $this->generateContractWord($room, $landlord, $tenant);

        // 📨 So sánh thay đổi và gửi mail
        $changes = [];
        foreach (['area', 'rental_price', 'deposit_price', 'status', 'occupants'] as $field) {
            if ($originalValues[$field] != $room->$field) {
                $changes[$field] = [
                    'old' => $originalValues[$field],
                    'new' => $room->$field,
                ];
            }
        }

        // Gửi mail nếu có thay đổi và có người thuê
        if (!empty($changes) && $tenant && filter_var($tenant->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($tenant->email)->queue(new RoomUpdatedNotification($room, $changes));
            } catch (\Exception $e) {
                Log::error('Không gửi được mail cập nhật phòng: ' . $e->getMessage());
            }
        }

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
            $room->load('property'); // Load để lấy property kèm theo

            $rules = $room->property->rules ?? '';

            $deposit_price = $room->deposit_price;

            $pdf = Pdf::loadView('landlord.rooms.pdf.Contracter', compact('room', 'landlord', 'rules', 'deposit_price'));

            Storage::disk('public')->put("contracts/contract_room_{$room->room_id}.pdf", $pdf->output());

            $room->update(['contract_pdf_file' => "contracts/contract_room_{$room->room_id}.pdf"]);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo PDF: ' . $e->getMessage());
        }
    }


    private function generateContractWord(Room $room, $landlord, $tenant)
    {
        $templatePath = resource_path('contracts/contract_template.docx');
        if (!file_exists($templatePath)) {
            Log::error('Mẫu Word không tồn tại.');
            return;
        }

        $room->load('facilities', 'services');
        $templateProcessor = new TemplateProcessor($templatePath);

        // Các thông tin cơ bản
        $templateProcessor->setValue('CHU_TRO', $landlord->name);
        $templateProcessor->setValue('SDT_CHU_TRO', $landlord->phone_number);
        $templateProcessor->setValue('CCCD_CHU_TRO', $landlord->identity_number);
        $templateProcessor->setValue('SO_PHONG', $room->room_number);
        $templateProcessor->setValue('DIEN_TICH', $room->area);
        $templateProcessor->setValue('GIA_THUE', number_format($room->rental_price));
        $templateProcessor->setValue('GIA_COC', number_format($room->deposit_price));
        $templateProcessor->setValue('TEN_NGUOI_THUE', $tenant->name ?? '......................................');
        $templateProcessor->setValue('SDT_NGUOI_THUE', $tenant->phone ?? '......................................');
        $templateProcessor->setValue('CCCD_NGUOI_THUE', $tenant->cccd ?? '......................................');
        $templateProcessor->setValue('EMAIL_NGUOI_THUE', $tenant->email ?? '......................................');
        // ✅ Cập nhật thông tin người thuê vào bảng user_infos
        if ($tenant) {
            $userInfo = \App\Models\UserInfo::firstOrNew(['user_id' => $tenant->id]);

            $userInfo->full_name = $tenant->name ?? $userInfo->full_name;
            $userInfo->cccd = $tenant->cccd ?? $userInfo->cccd;
            $userInfo->phone = $tenant->phone ?? $userInfo->phone;
            $userInfo->email = $tenant->email ?? $userInfo->email;
            $userInfo->room_id = $room->room_id;

            $userInfo->save();
        }

        $templateProcessor->setValue('SO_LUONG_NGUOI_O', $tenant->people_renter ?? '......................................');
        $templateProcessor->setValue('NGAY_BAT_DAU', '........../........../............');
        $templateProcessor->setValue('NGAY_KET_THUC', '........../........../............');
        $templateProcessor->setValue('SO_LUONG_NGUOI_TOI_DA', $room->occupants ?? '......................................');

        // ✅ TIỆN NGHI DƯỚI DẠNG CHECKBOX
        $roomFacilities = $room->facilities->pluck('name')->toArray();

        $facilityPlaceholders = [
            'Máy lạnh' => 'FACILITY_MAYLANH',
            'Tủ lạnh' => 'FACILITY_TULANH',
            'Wifi' => 'FACILITY_WIFI',
            'Tivi' => 'FACILITY_TIVI',
            'Giường' => 'FACILITY_GIUONG',
            'Bàn học' => 'FACILITY_BANHOC',
            'Bàn làm việc' => 'FACILITY_BANLAMVIEC',
        ];

        foreach ($facilityPlaceholders as $facilityName => $placeholder) {
            $templateProcessor->setValue($placeholder, in_array($facilityName, $roomFacilities) ? '' : ' ');
        }


        // ✅ NỘI QUY
        $rules = strip_tags($room->property->rules ?? 'Không có nội quy được thiết lập.');
        $templateProcessor->setValue('NOI_QUY', $rules);

        // ✅ GIÁ THANG MÁY (nếu có)
        $thangMay = $room->services->firstWhere('name', 'Thang máy');
        if ($thangMay) {
            $giaThangMay = $thangMay->pivot->is_free
                ? 'Miễn phí'
                : number_format($thangMay->pivot->price) . ' VNĐ/' . ($thangMay->pivot->unit === 'per_person' ? 'người' : 'phòng');
        } else {
            $giaThangMay = 'Không sử dụng';
        }
        $templateProcessor->setValue('GIA_THANG_MAY', $giaThangMay);

        // ✅ DỊCH VỤ
        $dichVu = '';
        foreach ($room->services as $service) {
            $unitLabel = match ($service->service_id) {
                1 => 'số',
                2 => $service->pivot->unit === 'per_m3' ? 'm³' : 'người',
                3 => $service->pivot->unit === 'per_room' ? 'phòng' : 'người',
                7 => $service->pivot->unit === 'per_room' ? 'phòng' : 'người',
                default => 'phòng',
            };
            $dichVu .= '- ' . $service->name . ': ' . ($service->pivot->is_free
                ? 'Miễn phí'
                : number_format($service->pivot->price) . ' VNĐ/' . $unitLabel) . "\n";
        }
        $templateProcessor->setValue('DICH_VU', trim($dichVu));

        // ✅ Lưu file
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
        $rentalAgreements = $room->rentalAgreements;

        // ❗ Chỉ chặn xoá nếu có hợp đồng đã khóa và có người thuê thật
        $hasRealRental = $rentalAgreements->contains(function ($agreement) {
            return $agreement->status === 'Active' && !is_null($agreement->renter_id);
        });

        if ($hasRealRental) {
            return back()->withErrors(['delete' => 'Không thể xóa phòng đã có khách thuê xác nhận hợp đồng.']);
        }

        // ✅ Xoá hợp đồng mẫu (kể cả Active nhưng chưa có renter)
        $room->rentalAgreements()->delete();

        // ✅ Xoá các dữ liệu liên quan
        $room->services()->detach();
        $room->facilities()->detach();

        foreach ($room->photos as $photo) {
            $path = str_replace('/storage/', '', $photo->image_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $photo->delete();
        }

        $room->delete();

        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Phòng đã được xoá thành công!');
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
            'contract_word_file' => 'required|mimes:doc,docx,pdf|max:2048',
        ]);

        $file = $request->file('contract_word_file');
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->storeAs('temp', uniqid() . '.' . $extension, 'public');

        $text = '';

        // 📄 Nếu là file Word
        if (in_array($extension, ['doc', 'docx'])) {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path('app/public/' . $tempPath));
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
        }
        // 📄 Nếu là file PDF
        elseif ($extension === 'pdf') {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile(storage_path('app/public/' . $tempPath));
                $text = $pdf->getText();
            } catch (\Exception $e) {
                return back()->withErrors(['contract_word_file' => 'Không thể đọc file PDF: ' . $e->getMessage()]);
            }
        }

        // ✨ Tách thông tin người thuê từ nội dung văn bản
        if (preg_match('/Họ tên:\s*(.*?)\n([^\n]*)/i', $text, $nameMatch)) {
            $tenantName = trim($nameMatch[1] . ' ' . $nameMatch[2]);
        } elseif (preg_match('/Họ tên:\s*(.*)/i', $text, $nameMatch)) {
            $tenantName = trim($nameMatch[1]);
        } else {
            $tenantName = '';
        }

        preg_match('/Email:\s*([^\s]+)/i', $text, $emailMatch);
        preg_match('/Số lượng người ở\s*:\s*([0-9]+)/i', $text, $peopleMatch);
        preg_match('/Số lượng người ở tối đa\s*:\s*([0-9]+)/i', $text, $maxPeopleMatch);

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


    // public function confirmContract(Request $request, Room $room)
    // {
    //     if ($room->is_contract_locked) {
    //         return back()->withErrors(['contract_locked' => 'Phòng này đã bị khóa hợp đồng. Không thể xác nhận hợp đồng.']);
    //     }

    //     $user = Auth::user();
    //     $tenantName  = $request->input('tenant_name');
    //     $tenantEmail = $request->input('tenant_email');
    //     $tenantPhone = $request->input('tenant_phone');   // ✅ huy thêm
    //     $tenantCccd  = $request->input('tenant_cccd');    // ✅ huy thêm
    //     $tempPath = $request->input('temp_path');

    //     // 1. Di chuyển file
    //     $newPath = 'contracts/word/' . basename($tempPath);
    //     Storage::disk('public')->move($tempPath, $newPath);

    //     // 2. Tạo mới hợp đồng
    //     $agreement = new RentalAgreement();
    //     $agreement->room_id = $room->room_id;
    //     $agreement->renter_id = $user->id;
    //     $agreement->status = 'Pending';
    //     $agreement->start_date = now();
    //     $agreement->end_date = now()->addMonths(12);
    //     $agreement->contract_file = $newPath;

    //     // ✅ thêm: lưu thông tin bên B vào hợp đồng
    //     $agreement->full_name = $tenantName;
    //     $agreement->email     = $tenantEmail;
    //     $agreement->phone     = $tenantPhone;
    //     $agreement->cccd      = $tenantCccd;

    //     $agreement->save();

    //     // ✅ Sau khi tạo hợp đồng, lưu thông tin vào user_infos
    //     $userInfo = \App\Models\UserInfo::firstOrNew(['user_id' => $user->id]);
    //     $userInfo->full_name = $tenantName;
    //     $userInfo->email     = $tenantEmail;
    //     $userInfo->phone     = $tenantPhone;   // ✅ huy thêm
    //     $userInfo->cccd      = $tenantCccd;    // ✅ huy thêm
    //     $userInfo->room_id   = $room->room_id;
    //     $userInfo->save();

    //     // 3. Cập nhật lại thông tin phòng
    //     $room->id_rental_agreements = $agreement->rental_id;
    //     $room->people_renter = $request->input('number_of_people', 0);
    //     $room->occupants = $request->input('max_number_of_people', 0);
    //     $room->save();

    //     return redirect()->route('show2', $room)->with('success', 'Hợp đồng mới đã được tạo và phòng đã được cập nhật!');
    // }

    public function confirmContract(Request $request, Room $room)
    {
        if ($room->is_contract_locked) {
            return back()->withErrors(['contract_locked' => 'Phòng này đã bị khóa hợp đồng. Không thể xác nhận hợp đồng.']);
        }

        $user = Auth::user();
        $tempPath = $request->input('temp_path');

        // 1. Di chuyển file sang thư mục lưu trữ chính thức
        $newPath = 'contracts/word/' . basename($tempPath);
        Storage::disk('public')->move($tempPath, $newPath);

        // 2. Parse PDF để lấy thông tin Bên B (người thuê)
        $tenantName = $tenantEmail = $tenantPhone = $tenantCccd = '';
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(storage_path('app/public/' . $newPath));
            $text = mb_convert_encoding($pdf->getText(), 'UTF-8', 'auto');

            if (preg_match('/BÊN THUÊ PHÒNG TRỌ.*?:\s*(.*?)(?:Căn cứ pháp lý|BÊN CHO THUÊ)/siu', $text, $match)) {
                $infoBlock = $match[1];

                preg_match('/- Ông\/Bà:\s*(.+)/u', $infoBlock, $nameMatch);
                preg_match('/- CMND\/CCCD số:\s*([0-9]+)/u', $infoBlock, $cccdMatch);
                preg_match('/- SĐT:\s*([0-9]+)/u', $infoBlock, $phoneMatch);
                preg_match('/- Email:\s*([^\s]+)/iu', $infoBlock, $emailMatch);

                $tenantName  = $nameMatch[1] ?? '';
                $tenantCccd  = $cccdMatch[1] ?? '';
                $tenantPhone = $phoneMatch[1] ?? '';
                $tenantEmail = $emailMatch[1] ?? '';
            }
        } catch (\Exception $e) {
            // Nếu parse lỗi thì để trống, tránh crash
        }

        // 3. Tạo mới hợp đồng
        $agreement = new RentalAgreement();
        $agreement->room_id = $room->room_id;
        $agreement->renter_id = $user->id; // ai tạo thì gắn tạm, sau khi parse xong có thể cập nhật lại
        $agreement->status = 'Pending';
        $agreement->start_date = now();
        $agreement->end_date = now()->addMonths(12);
        $agreement->contract_file = $newPath;

        // ✅ lưu thông tin người thuê lấy từ PDF
        $agreement->full_name = $tenantName;
        $agreement->email     = $tenantEmail;
        $agreement->phone     = $tenantPhone;
        $agreement->cccd      = $tenantCccd;

        $agreement->save();

        // 4. Lưu thêm vào user_infos
        $userInfo = \App\Models\UserInfo::firstOrNew(['user_id' => $user->id]);
        $userInfo->full_name = $tenantName;
        $userInfo->email     = $tenantEmail;
        $userInfo->phone     = $tenantPhone;
        $userInfo->cccd      = $tenantCccd;
        $userInfo->room_id   = $room->room_id;
        $userInfo->save();

        // 5. Cập nhật lại thông tin phòng
        $room->id_rental_agreements = $agreement->rental_id;
        $room->people_renter = $request->input('number_of_people', 0);
        $room->occupants = $request->input('max_number_of_people', 0);
        $room->save();

        return redirect()->route('show2', $room)->with('success', '✅ Hợp đồng mới đã được tạo từ file và phòng đã được cập nhật!');
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
        // ✅ Validate input
        $request->validate([
            'rental_id'   => 'required|exists:rental_agreements,rental_id',
            'tenant_name' => 'required|string|max:255',
            'tenant_email' => 'required|email',
            'tenant_phone' => 'required|string|max:20',
            'tenant_cccd' => 'required|string|max:20',
            'occupants'   => 'nullable|integer|min:0',
            'people_renter' => 'nullable|integer|min:0',
        ]);

        // 1. Lấy thông tin từ request
        $rentalId     = $request->input('rental_id');
        $tenantName   = $request->input('tenant_name');
        $tenantEmail  = $request->input('tenant_email');
        $tenantPhone  = $request->input('tenant_phone');
        $tenantCccd   = $request->input('tenant_cccd');
        $occupants    = $request->input('occupants', 0);
        $people_renter = $request->input('people_renter', 0);

        // 2. Tìm hợp đồng và cập nhật trạng thái
        $rental = RentalAgreement::findOrFail($rentalId);
        $rental->status    = 'Active';
        $rental->is_active = 1;
        $rental->save();

        // 3. Cập nhật phòng thành 'Rented'
        $room = Room::findOrFail($rental->room_id);
        $room->status = 'Rented';
        $room->save();

        if ($people_renter > $occupants) {
            return back()->withErrors([
                'people_renter' => 'Số lượng người ở không được lớn hơn số lượng người tối đa của phòng.'
            ]);
        }

        // 4. Kiểm tra email đã tồn tại chưa
        $existingUser = User::where('email', $tenantEmail)->first();
        if (!$existingUser) {
            // Tạo mật khẩu ngẫu nhiên
            $password       = Str::random(8);
            $hashedPassword = Hash::make($password);

            // Tạo user
            $user = new User();
            $user->name     = $tenantName;
            $user->email    = $tenantEmail;
            $user->password = $hashedPassword;
            $user->role     = 'renter';
            $user->save();

            $rental->renter_id = $user->id;

            // Gửi email thông báo tài khoản
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
        } else {
            $rental->renter_id = $existingUser->id;
        }

        // ✅ Luôn luôn lưu thông tin Bên B vào hợp đồng
        $rental->full_name = $tenantName;
        $rental->email     = $tenantEmail;
        $rental->phone     = $tenantPhone;
        $rental->cccd      = $tenantCccd;
        $rental->save();

        return back()->with('success', 'Hợp đồng đã xác nhận và tài khoản người thuê đã được xử lý.');
    }


    public function ConfirmAllUser(Request $request)
    {
        $userId = $request->input('user_id');
        $rentalId = $request->input('rental_id');


        // 4. Kiểm tra email đã tồn tại chưa
        $user = User::findOrFail($userId);
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

    public function lockRoom(Request $request, Room $room)
    {
        $request->validate([
            'lock_reason' => 'required|string|max:500',
        ]);

        // Tìm hợp đồng đang hoạt động
        $activeAgreement = $room->rentalAgreements()
            ->whereIn('status', ['Active', 'Signed'])
            ->where('is_active', 1)
            ->latest('start_date')
            ->first();

        $tenant = null;

        if ($activeAgreement) {
            // Nếu có hợp đồng thì kết thúc hợp đồng
            $activeAgreement->update([
                'is_active' => 0,
                'status'    => RentalAgreement::STATUS_TERMINATED,
            ]);

            $tenant = $activeAgreement->renter;
        }

        // 1. Khóa phòng (kể cả không có hợp đồng vẫn khóa được)
        $room->update([
            'is_contract_locked' => true,
            'lock_reason'        => $request->lock_reason,
        ]);

        // 2. Nếu có tenant thì gửi mail
        if ($tenant) {
            // Gợi ý phòng mới trong cùng property
            $samePropertyRooms = Room::with(['property', 'facilities', 'services', 'photos'])
                ->where('property_id', $room->property_id)
                ->where('room_id', '!=', $room->room_id)
                ->where('is_contract_locked', false)
                ->whereDoesntHave('rentalAgreements', function ($q) {
                    $q->whereIn('status', ['Active', 'Signed']);
                })
                ->inRandomOrder()
                ->take(3)
                ->get();

            // Nếu chưa đủ thì lấy thêm phòng khác property
            if ($samePropertyRooms->count() < 3) {
                $moreRooms = Room::with(['property', 'facilities', 'services', 'photos'])
                    ->where('property_id', '!=', $room->property_id)
                    ->where('is_contract_locked', false)
                    ->whereDoesntHave('rentalAgreements', function ($q) {
                        $q->whereIn('status', ['Active', 'Signed']);
                    })
                    ->inRandomOrder()
                    ->take(3 - $samePropertyRooms->count())
                    ->get();

                $suggestedRooms = $samePropertyRooms->merge($moreRooms);
            } else {
                $suggestedRooms = $samePropertyRooms;
            }

            // Gửi mail tenant
            Mail::to($tenant->email)->send(
                new \App\Mail\RoomLockedNotification(
                    $room,
                    $request->lock_reason,
                    $suggestedRooms,
                    $activeAgreement->rental_id ?? null
                )
            );

            // Gửi mail landlord
            Mail::to($room->property->landlord->email)->send(
                new \App\Mail\TenantMovedNotification([
                    'full_name' => $tenant->name,
                    'email'     => $tenant->email,
                    'phone'     => $activeAgreement->phone,
                    'cccd'      => $activeAgreement->cccd,
                ], $room)
            );
        }

        return back()->with('success', 'Phòng đã được khóa thành công.');
    }



    public function unlockRoom(Room $room)
    {
        // Cập nhật trạng thái mở khóa
        $room->update([
            'is_contract_locked' => false,
            'lock_reason' => null, // clear lý do cũ
        ]);

        return back()->with('success', 'Phòng đã được mở khóa, có thể cho thuê lại.');
    }

    public function joinRoom(Room $newRoom, $tenantId)
    {
        // 1. Lấy hợp đồng vừa bị khóa của tenant
        $oldAgreement = RentalAgreement::where('renter_id', $tenantId)
            ->where('status', RentalAgreement::STATUS_TERMINATED)
            ->latest('updated_at')
            ->first();

        if (!$oldAgreement) {
            return back()->withErrors('Không tìm thấy hợp đồng cũ đã bị khóa để tham gia phòng mới.');
        }

        // 2. Tạo hợp đồng mới ở phòng mới
        $newAgreement = $newRoom->rentalAgreements()->create([
            'renter_id'    => $oldAgreement->renter_id,
            'landlord_id'  => $newRoom->property->landlord_id,
            'start_date'   => now(),
            'end_date'     => now()->addMonths(12),
            'status'       => RentalAgreement::STATUS_ACTIVE,
            'rental_price' => $newRoom->rental_price,
            'deposit'      => $newRoom->deposit_price,
            'is_active'    => 1,
            'created_by'   => auth()->id() ?? $oldAgreement->renter_id,
            'full_name'    => $oldAgreement->full_name,
            'email'        => $oldAgreement->email,
            'phone'        => $oldAgreement->phone,
            'cccd'         => $oldAgreement->cccd,
        ]);

        // 3. Cập nhật phòng mới
        $newRoom->update([
            'id_rental_agreements' => $newAgreement->rental_id,
            'people_renter'        => 1,
            'is_contract_locked'   => false,
        ]);

        // 4. Gửi mail tenant
        if ($oldAgreement->email) {
            Mail::to($oldAgreement->email)
                ->send(new \App\Mail\RoomJoinSuccessNotification($newRoom, $oldAgreement->full_name, $oldAgreement->phone, $oldAgreement->cccd));
        }

        // 5. Gửi mail landlord
        Mail::to($newRoom->property->landlord->email)
            ->send(new \App\Mail\TenantMovedNotification([
                'full_name' => $oldAgreement->full_name,
                'email'     => $oldAgreement->email,
                'phone'     => $oldAgreement->phone,
                'cccd'      => $oldAgreement->cccd,
            ], $newRoom));

        return back()->with('success', 'Tenant đã tham gia phòng mới thành công, thông tin cá nhân giữ nguyên, giá phòng cập nhật theo phòng mới!');
    }

    public function move(Request $request, Room $room)
    {
        // dd($room->room_id, $room->toArray());

        $request->validate([
            'new_room_id' => 'required|exists:rooms,room_id',
        ]);

        // Lấy phòng mới
        $newRoom = Room::with('property')->findOrFail($request->new_room_id);
        // Kiểm tra phòng mới có tenant hay không
        if ($newRoom->currentAgreementValid) {
            return back()->with('error', 'Phòng mới đang có tenant, không thể chuyển.');
        }

        // Lấy hợp đồng gần nhất của phòng cũ => chuyển sang phòng mới(chỉ chuyển hợp đồng đang hoạt động)
        $oldAgreement = $room->rentalAgreements()
            ->whereIn('status', [RentalAgreement::STATUS_ACTIVE, RentalAgreement::STATUS_SIGNED])
            ->where('is_active', 1)
            ->latest('start_date')
            ->first();

        if (!$oldAgreement) {
            return back()->with('error', 'Phòng này hiện không có hợp đồng đang hoạt động, không thể chuyển.');
        }


        if (!$oldAgreement) {
            return back()->with('error', 'Phòng cũ không có hợp đồng nào.');
        }

        // Tạo hợp đồng mới cho phòng mới
        $newAgreement = $newRoom->rentalAgreements()->create([
            'renter_id'    => $oldAgreement->renter_id,
            'landlord_id'  => $newRoom->property->landlord_id,
            'start_date'   => now(),
            'end_date'     => now()->addMonths(12),
            'status'       => RentalAgreement::STATUS_ACTIVE,
            'rental_price' => $newRoom->rental_price,
            'deposit'      => $newRoom->deposit_price,
            'is_active'    => 1,
            'created_by'   => auth()->id() ?? $oldAgreement->renter_id,

            // Copy thông tin Bên B từ hợp đồng cũ
            'full_name'    => $oldAgreement->full_name,
            'email'        => $oldAgreement->email,
            'phone'        => $oldAgreement->phone,
            'cccd'         => $oldAgreement->cccd,
        ]);

        // dd($newAgreement->toArray());
        // Cập nhật phòng mới
        $newRoom->update([
            'id_rental_agreements' => $newAgreement->rental_id,
            'people_renter'        => 1,
            'is_contract_locked'   => false,
        ]);

        // Cập nhật hợp đồng cũ thành đã kết thúc
        $oldAgreement->update([
            'status'    => RentalAgreement::STATUS_TERMINATED,
            'is_active' => 0,
        ]);

        // 🚀 Gửi mail cho tenant (người thuê)
        Mail::to($oldAgreement->email)->send(
            new RoomJoinSuccessNotification($newRoom, $newAgreement) // bạn có thể custom mail này
        );

        return back()->with('success', 'Tenant đã chuyển sang phòng mới thành công!');
    }

    public function getRoomsByProperty($property_id)
    {
        $rooms = Room::where('property_id', $property_id)->get(['room_id']);
        return response()->json(['rooms' => $rooms]);
    }

    public function getRoomsByProperty($property_id)
    {
        $rooms = Room::where('property_id', $property_id)->get(['room_id']);
        return response()->json(['rooms' => $rooms]);
    }
}
