<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Property ;
use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
    // Lấy người dùng đang đăng nhập
    $user = auth()->user(); // Chủ trọ

    // Lấy danh sách property của landlord đó, sắp xếp mới nhất
    $listProperties = Property::where('landlord_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(5);

    return view("landlord.propertyManagement.list", compact("listProperties"));
    }
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
    
    $user = auth()->user();

    // Validate input
    $validatedData = $request->validate([
        'name' => 'required|string|max:100',
        'province' => 'required|string',
        'district' => 'required|string',
        'ward' => 'required|string',
       'detailed_address' => 'nullable|string|max:255',
         'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         'document_files.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',

    ]);


    // Lấy tên địa phương từ API
    $provinceName = Http::get("https://provinces.open-api.vn/api/p/{$request->province}")['name'] ?? '';
    $districtName = Http::get("https://provinces.open-api.vn/api/d/{$request->district}")['name'] ?? '';
    $wardName = Http::get("https://provinces.open-api.vn/api/x/{$request->ward}")['name'] ?? '';

    $fullAddress = "{$request->detailed_address} {$wardName}, {$districtName}, {$provinceName}, Việt Nam";

    // Lấy tọa độ
    $geo = Http::get("https://nominatim.openstreetmap.org/search", [
        'format' => 'json',
        'q' => $fullAddress
    ])->json();

    $lat = $geo[0]['lat'] ?? 21.028511;
    $lon = $geo[0]['lon'] ?? 105.804817;

     // [3] Xử lý ảnh đại diện (đã validate bắt buộc nên không cần kiểm tra hasFile)
    $image = $request->file('image_url');
    $imageFilename = 'property_' . time() . '.' . $image->getClientOriginalExtension();
    $imagePath = $image->storeAs("public/property_images/{$user->id}", $imageFilename);
    $imageUrl = Storage::url($imagePath);

    // Lưu bất động sản
    $property = Property::create([
        'landlord_id' => $user->id,
        'name' => $validatedData['name'],
        'address' => $fullAddress,
        'latitude' => $lat,
        'longitude' => $lon,
        'description' => $request->input('description', ''),
        'image_url' => $imageUrl,
    ]);

    //  if (!$property || !$property->id) {
    //     return back()->with('error', 'Lỗi khi tạo bất động sản, vui lòng thử lại.');
    // }
    // Danh sách loại giấy tờ
    $documentTypes = [
        'so_do' => 'Sổ đỏ',
        'giay_phep_xay_dung' => 'Giấy phép xây dựng',
        'pccc' => 'Giấy chứng nhận PCCC',
        'giay_phep_kinh_doanh' => 'Giấy phép kinh doanh',
    ];

    $hasDocument = false;
    $propertyId = $property->id;

    // Lưu giấy tờ hợp lệ kèm property_id
    if ($request->hasFile('document_files')) {
        foreach ($documentTypes as $key => $docType) {
            if ($request->file("document_files.$key")) {
                $hasDocument = true;

                $file = $request->file("document_files.$key");
                $filename = Str::slug($docType) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path =  $file->storeAs("public/legal_documents/{$user->id}", $filename);

                LegalDocument::create([
                    'user_id' => $user->id,
                    'property_id' => $propertyId || 1,
                    'document_type' => $docType,
                    'file_path' => $path,
                    'status' => 'Pending',
                    'uploaded_at' => now(),
                ]);
            }
        }
    }
    if (!$hasDocument) {
        $property->delete();  // [4] Xóa bất động sản đã tạo để tránh lưu dữ liệu không hợp lệ
        return back()->with('error', 'Bạn cần gửi ít nhất một loại giấy tờ hợp lệ.');
    }

    return redirect()->route('landlords.properties.list')->with('success', 'Thêm bất động sản thành công!');
}



    /**
     * Display the specified resource.
     */
    public function show(Property  $Property, $id )
    {
    // Lấy thông tin chi tiết bất động sản và chủ trọ
    $property = DB::table('properties')
        ->join('users', 'properties.landlord_id', '=', 'users.id')
        ->where('properties.id', $id)
        ->select(
            'properties.*',
            'users.name as landlord_name',
            'users.id as landlord_id'
        )
        ->first();

    // Lấy danh sách giấy tờ pháp lý liên quan đến property_id
    $legalDocuments = DB::table('legal_documents')
        ->where('user_id', $id)
        ->select('document_type', 'status', 'file_path')
        ->get();


    // Gửi dữ liệu đến view nếu không dùng dd()
    return view('landlord.propertyManagement.show', compact('property', 'legalDocuments'));
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
