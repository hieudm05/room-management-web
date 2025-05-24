<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Property ;
use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            $landlordIds = User::where('role', 'Landlord')->pluck('id');

    // Lấy danh sách property thuộc về các landlord
    $listProperties = Property::whereIn('landlord_id', $landlordIds)->paginate(5);;
        return view("landlord.propertyManagement.list",compact("listProperties"));
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
    // $user = auth()->user(); // Chủ trọ đang đăng nhập
    $user = auth()->user(); // Chủ trọ đang đăng nhập
    $request->validate([
        'name' => 'required|string|max:100',
        'province' => 'required|string',
        'district' => 'required|string',
        'ward' => 'required|string',
        'detailed_address' => 'required|string|max:255',
    ]);
    // Lấy tên địa lý
    $provinceData = Http::get("https://provinces.open-api.vn/api/p/{$request->province}")->json();
    $districtData = Http::get("https://provinces.open-api.vn/api/d/{$request->district}")->json();
    $wardData     = Http::get("https://provinces.open-api.vn/api/x/{$request->ward}")->json();

    $provinceName = $provinceData['name'] ?? '';
    $districtName = $districtData['name'] ?? '';
    $wardName     = $wardData['name'] ?? '';

    // Ghép địa chỉ đầy đủ
    $fullAddress = "{$request->detailed_address}, {$wardName}, {$districtName}, {$provinceName}, Việt Nam";

    // Gọi API lấy tọa độ
    $geo = Http::get("https://nominatim.openstreetmap.org/search", [
        'format' => 'json',
        'q' => $fullAddress
    ])->json();

    $lat = $geo[0]['lat'] ?? null;
    $lon = $geo[0]['lon'] ?? null;

    // Nếu không có tọa độ thì cho toạ độ mặc định (VD: Hà Nội)
    if (!$lat || !$lon) {
        $lat = 21.028511;
        $lon = 105.804817;
    }

    // Lưu vào DB
    Property::create([
        'landlord_id' => 2,
        'name' => $request->name,
        'address' => $fullAddress,
        'latitude' => $lat,
        'longitude' => $lon,
        'description' => $request->input('description', ''),
    ]);
     // 2. Lưu các giấy tờ hợp lệ vào bảng legal_documents
    $documentTypes = [
        'so_do' => 'Sổ đỏ',
        'giay_phep_xay_dung' => 'Giấy phép xây dựng',
        'pccc' => 'Giấy chứng nhận PCCC',
        'giay_phep_kinh_doanh' => 'Giấy phép kinh doanh',
    ];

    if ($request->hasFile('document_files')) {
        foreach ($documentTypes as $key => $docType) {
            if ($request->file("document_files.$key")) {
                $file = $request->file("document_files.$key");
                $filename = Str::slug($docType) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("public/legal_documents/2", $filename);

                LegalDocument::create([
                    'user_id' => 2,
                    'document_type' => $docType,
                    'file_path' => $path,
                    'status' => 'Pending',
                    'uploaded_at' => now(),
                ]);
            }
        }
    }

    return redirect()->route('landlords.properties.list')->with('success', 'Thêm bất động sản thành công');
}

    /**
     * Display the specified resource.
     */
    public function show(Property  $Property )
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property  $Property )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property  $Property )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property  $Property )
    {
        //
    }
}
