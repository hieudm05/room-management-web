<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Property;
use App\Models\Landlord\PropertyImage;
use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomBillService;
use App\Models\Landlord\RentalAgreement;
use App\Exports\PropertyBillsExport;
use Maatwebsite\Excel\Facades\Excel;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng đang đăng nhập
        $user = Auth::user(); // Chủ trọ


        // Lấy danh sách property của landlord đó, sắp xếp mới nhất
        $listProperties = Property::where('landlord_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view("landlord.propertyManagement.list", compact("listProperties"));
    }
    // Lấy danh sách ID của người dùng có role là 'Landlord'

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("landlord.propertyManagement.create");
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['auth' => 'Vui lòng đăng nhập để thực hiện thao tác này.']);
        }

        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
            'detailed_address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'required|string', // Yêu cầu rules không rỗng
            'image_urls.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5024',
            'images_property.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5024',
        ]);

        // Làm sạch nội dung HTML của rules
        $validatedData['rules'] = Purifier::clean($validatedData['rules']);

        // Lấy tên địa phương từ API
        $provinceName = Http::get("https://provinces.open-api.vn/api/p/{$request->province}")['name'] ?? '';
        $districtName = Http::get("https://provinces.open-api.vn/api/d/{$request->district}")['name'] ?? '';
        // $wardName = Http::get("https://provinces.open-api.vn/api/x/{$request->ward}")['name'] ?? '';
        $district = Http::get("https://provinces.open-api.vn/api/d/{$request->district}?depth=2")->json();
        $wardName = '';

        foreach ($district['wards'] ?? [] as $ward) {
            if ($ward['code'] == $request->ward) {
                $wardName = $ward['name'];
                break;
            }
        }
        $fullAddress = "{$request->detailed_address}, {$wardName}, {$districtName}, {$provinceName}, Việt Nam";
        // Lấy tọa độ
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');

        // dd($request->file('image_urls'));

        // Handle main image
        $imageFiles = $request->file('image_urls');

        if (!$imageFiles || count($imageFiles) === 0) {
            return back()->withErrors(['image_urls' => 'Vui lòng tải lên ít nhất một ảnh.'])->withInput();
        }

        $mainImage = $imageFiles[0]; // ✅ ảnh đầu tiên
        $imageFilename = 'property_' . time() . '_' . Str::random(6) . '.' . $mainImage->getClientOriginalExtension();
        $imagePath = $mainImage->storeAs("property_images/{$user->id}", $imageFilename, 'public');
        $imageUrl = Storage::url($imagePath);


        // Create property
        $property = Property::create([
            'landlord_id' => $user->id,
            'name' => $validatedData['name'],
            'address' => $fullAddress,
            'latitude' => $lat,
            'longitude' => $lon,
            'description' => $validatedData['description'],
            'rules' => $validatedData['rules'],
            'image_url' => $imageUrl,
        ]);
        // Handle multiple property images
        foreach (array_slice($imageFiles, 1) as $extraImage) {
            $extraFilename = 'property_extra_' . time() . '_' . Str::random(6) . '.' . $extraImage->getClientOriginalExtension();
            $extraPath = $extraImage->storeAs("public/property_images/{$user->id}", $extraFilename);

            PropertyImage::create([
                'property_id' => $property->property_id,
                'image_path' => Storage::url($extraPath),
            ]);
        }
        if ($request->hasFile('document_files')) {
            $documentTypes = $request->input('document_types', []);
            $documentFiles = $request->file('document_files');
            foreach ($documentFiles as $idx => $file) {
                if ($file && isset($documentTypes[$idx])) {
                    $docType = $documentTypes[$idx];
                    $filename = Str::slug($docType) . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('legal_documents', $filename, 'public');
                    LegalDocument::create([
                        'user_id' => $user->id,
                        'property_id' => $property->property_id,
                        'document_type' => $docType,
                        'file_path' => $path,
                        'status' => 'Pending',
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('landlords.properties.list')->with('success', 'Thêm bất động sản thành công!');
    }
    /**
     * Display the specified resource.
     */
    public function show($property_id)
    {
        // Lấy property bằng Eloquent, nạp quan hệ images
        $property = Property::with('images')
            ->join('users', 'properties.landlord_id', '=', 'users.id')
            ->where('properties.property_id', $property_id)
            ->select(
                'properties.*',
                'users.name as landlord_name',
                'users.id as landlord_id'
            )
            ->first();

        if (!$property) {
            abort(404);
        }

        // Lấy danh sách giấy tờ pháp lý liên quan đến property_id
        $legalDocuments = LegalDocument::where('user_id', $property->landlord_id)
            ->where('property_id', $property_id)
            ->get();

        return view('landlord.propertyManagement.show', compact('property', 'legalDocuments'));
    }

    public function uploadDocument(Request $request, $property_id)
    {
        // Validate tất cả các file giấy tờ
        $request->validate([
            'document_types' => 'required|array',
            'document_files' => 'required|array',
            'document_files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $user = Auth::user();
        $documentTypes = $request->input('document_types', []);
        $documentFiles = $request->file('document_files', []);

        foreach ($documentFiles as $idx => $file) {
            if ($file && isset($documentTypes[$idx])) {
                $docType = $documentTypes[$idx];
                $filename = Str::slug($docType) . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('legal_documents', $filename, 'public');
                LegalDocument::create([
                    'user_id' => $user->id,
                    'property_id' => $property_id,
                    'document_type' => $docType,
                    'file_path' => $path,
                    'status' => 'Pending',
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()->route('landlords.properties.list')->with('success', 'Bổ sung giấy tờ thành công!');
    }
    public function showDetalShow($property_id, Request $request)
    {
        $user = Auth::user(); // Chủ trọ
        $property = Property::findOrFail($property_id);
        $bankAccounts = $user->bankAccounts()->get();

        $month = $request->input('month', now()->format('Y-m'));
        $monthParts = explode('-', $month);
        $monthNum = $monthParts[1] ?? now()->format('m');
        $yearNum = $monthParts[0] ?? now()->format('Y');

        $bills = [];
        if ($request->has('month')) {
            $rooms = \App\Models\Landlord\Room::where('property_id', $property_id)->get();
            foreach ($rooms as $room) {
                $bill = \App\Models\Landlord\Staff\Rooms\RoomBill::where('room_id', $room->room_id)
                    ->whereMonth('month', $monthNum)
                    ->whereYear('month', $yearNum)
                    ->first();

                if ($bill) {
                    $rentalAgreement = \App\Models\Landlord\RentalAgreement::find($room->id_rental_agreements);
                    $tenant = $rentalAgreement ? \App\Models\User::find($rentalAgreement->renter_id) : null;

                    // Dịch vụ phụ
                    $services = [];
                    $service_total = 0;
                    $billServices = \App\Models\Landlord\Staff\Rooms\RoomBillService::where('room_bill_id', $bill->id)->get();
                    foreach ($billServices as $sv) {
                        $service = \App\Models\Landlord\Service::find($sv->service_id);
                        $services[] = [
                            'name' => $service->name ?? 'Không rõ',
                            'price' => $sv->price,
                            'qty' => $sv->qty,
                            'total' => $sv->total,
                        ];
                        $service_total += $sv->total;
                    }

                    $total = ($bill->rent_price ?? 0) + ($bill->electric_total ?? 0) + ($bill->water_total ?? 0) + $service_total;

                    $bills[] = [
                        'room' => $room,
                        'bill' => $bill,
                        'tenant' => $tenant,
                        'services' => $services,
                        'service_total' => $service_total,
                        'total' => $total,
                    ];
                }
            }
        }

        return view('landlord.propertyManagement.shows', compact('property_id', 'property', 'bankAccounts', 'bills'));
    }
    public function exportBillsByMonth(Request $request, $property_id)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $monthParts = explode('-', $month);
        $monthNum = $monthParts[1] ?? now()->format('m');
        $yearNum = $monthParts[0] ?? now()->format('Y');

        // Lấy tất cả phòng thuộc tòa nhà
        $rooms = Room::where('property_id', $property_id)->get();

        $bills = [];
        foreach ($rooms as $room) {
            $bill = RoomBill::where('room_id', $room->room_id)
                ->whereMonth('month', $monthNum)
                ->whereYear('month', $yearNum)
                ->first();

            if ($bill) {
                $rentalAgreement = RentalAgreement::find($room->id_rental_agreements);
                $tenant = $rentalAgreement ? User::find($rentalAgreement->renter_id) : null;

                // Lấy dịch vụ phụ
                $services = [];
                $service_total = 0;
                $billServices = RoomBillService::where('room_bill_id', $bill->id)->get();
                foreach ($billServices as $sv) {
                    $service = \App\Models\Landlord\Service::find($sv->service_id);
                    $services[] = [
                        'name' => $service->name ?? 'Không rõ',
                        'price' => $sv->price,
                        'qty' => $sv->qty,
                        'total' => $sv->total,
                    ];
                    $service_total += $sv->total;
                }

                $total = ($bill->rent_price ?? 0) + ($bill->electric_total ?? 0) + ($bill->water_total ?? 0) + $service_total;

                $bills[] = [
                    'room' => $room,
                    'bill' => $bill,
                    'tenant' => $tenant,
                    'services' => $services,
                    'service_total' => $service_total,
                    'total' => $total,
                ];
            }
        }

        return Excel::download(new PropertyBillsExport($bills, $month), 'tong_hop_hoa_don_' . $month . '.xlsx');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $property = Property::findOrFail($id);

        // Tách địa chỉ
        $addressParts = array_map('trim', explode(',', $property->address));
        $addressParts = array_reverse($addressParts); // Đảo ngược để xử lý từ quốc gia đến chi tiết
        $parsedAddress = [
            'province' => $addressParts[1] ?? '', // Thành phố Hà Nội
            'district' => $addressParts[2] ?? '', // Quận Tây Hồ
            'ward' => $addressParts[3] ?? '',
            'detailed_address' => implode(', ', array: array_slice($addressParts, 4)) ?? '', // Ngõ 84 (có thể có nhiều phần)
        ];

        // Lấy danh sách giấy tờ pháp lý
        $legalDocuments = LegalDocument::where('property_id', $property->property_id)->get();
        return view('landlord.propertyManagement.edit', compact('property', 'parsedAddress', 'legalDocuments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $property_id)
    {
        $user = Auth::user();
        $property = Property::findOrFail($property_id);

        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string',
            'district' => 'required|string',
            'ward' => 'required|string',
            'detailed_address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'required|string',
            'image_urls.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5024',
        ]);

        // Làm sạch nội dung HTML của rules
        $validatedData['rules'] = Purifier::clean($validatedData['rules']);

        // Lấy tên địa phương từ API
        $provinceName = Http::get("https://provinces.open-api.vn/api/p/{$request->province}")['name'] ?? '';
        // $districtName = Http::get("https://provinces.open-api.vn/api/d/{$request->district}")['name'] ?? '';
        $districtResponse = Http::get("https://provinces.open-api.vn/api/d/{$request->district}?depth=2")->json();
        $districtName = $districtResponse['name'] ?? '';
        $wardName = '';

        foreach ($districtResponse['wards'] ?? [] as $ward) {
            if ($ward['code'] == $request->ward) {
                $wardName = $ward['name'];
                break;
            }
        }

        $fullAddress = "{$request->detailed_address}, {$wardName}, {$districtName}, {$provinceName}, Việt Nam";
        // Lấy tọa độ
        $geo = Http::get("https://nominatim.openstreetmap.org/search", [
            'format' => 'json',
            'q' => $fullAddress
        ])->json();
        $lat = $request->input('latitude');
        $lon = $request->input('longitude');

        // Xử lý ảnh chính
        $imageUrl = $property->image_url;
        if ($request->hasFile('image_urls') && count($request->file('image_urls')) > 0) {
            // Xóa ảnh chính cũ nếu tồn tại
            if ($imageUrl && Storage::exists('public/' . str_replace(Storage::url(''), '', $imageUrl))) {
                Storage::delete('public/' . str_replace(Storage::url(''), '', $imageUrl));
            }
            $mainImage = $request->file('image_urls')[0];
            $imageFilename = 'property_' . time() . '_' . Str::random(6) . '.' . $mainImage->getClientOriginalExtension();
            $imagePath = $mainImage->storeAs("property_images/{$user->id}", $imageFilename, 'public');
            $imageUrl = Storage::url($imagePath);
        }

        // Xử lý ảnh phụ
        if ($request->hasFile('image_urls')) {
            // Xóa ảnh phụ cũ
            $oldImages = PropertyImage::where('property_id', $property->property_id)->get();
            foreach ($oldImages as $oldImage) {
                if (Storage::exists('public/' . str_replace(Storage::url(''), '', $oldImage->image_path))) {
                    Storage::delete('public/' . str_replace(Storage::url(''), '', $oldImage->image_path));
                }
                $oldImage->delete();
            }

            // Thêm ảnh phụ mới
            foreach (array_slice($request->file('image_urls'), 1) as $extraImage) {
                $extraFilename = 'property_extra_' . time() . '_' . Str::random(6) . '.' . $extraImage->getClientOriginalExtension();
                $extraPath = $extraImage->storeAs("property_images/{$user->id}", $extraFilename, 'public');
                PropertyImage::create([
                    'property_id' => $property->property_id,
                    'image_path' => Storage::url($extraPath),
                ]);
            }
        }


        // Cập nhật thông tin bất động sản
        $property->update([
            'name' => $validatedData['name'],
            'address' => $fullAddress,
            'latitude' => $lat,
            'longitude' => $lon,
            'description' => $validatedData['description'],
            'rules' => $validatedData['rules'],
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('landlords.properties.list')->with('success', 'Cập nhật bất động sản thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $Property)
    {
        //
    }
}
