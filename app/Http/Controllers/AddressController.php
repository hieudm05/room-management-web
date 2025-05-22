<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    // Lấy danh sách tỉnh
    public function getProvinces()
    {
        $response = Http::get('https://provinces.open-api.vn/api/p/');
        $provinces = $response->json();

        return response()->json($provinces);
    }

    // Lấy danh sách huyện theo mã tỉnh
    public function getDistricts($provinceCode)
    {
        $response = Http::get("https://provinces.open-api.vn/api/p/{$provinceCode}?depth=2");
        $provinceData = $response->json();

        // $provinceData['districts'] chứa danh sách huyện
        $districts = $provinceData['districts'] ?? [];

        return response()->json($districts);
    }

    // Lấy danh sách xã theo mã huyện
    public function getWards($districtCode)
    {
        $response = Http::get("https://provinces.open-api.vn/api/d/{$districtCode}?depth=2");
        $districtData = $response->json();

        // $districtData['wards'] chứa danh sách xã
        $wards = $districtData['wards'] ?? [];

        return response()->json($wards);
    }
}
