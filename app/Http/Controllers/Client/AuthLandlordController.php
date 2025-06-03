<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MoreInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthLandlordController extends Controller
{
    /**
     * Hiển thị form đăng ký làm chủ trọ
     */
    public function showForm()
    {
        return view('auth.client.registerLandlord');
    }

    /**
     * Xử lý dữ liệu form đăng ký làm chủ trọ
     */
    public function submit(Request $request)
    {
        $request->validate([
            'province_code' => 'required',
            'district_code' => 'required',
            'ward_code'     => 'required',
            'full_address'  => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'cccd_front'    => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'cccd_back'     => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();

        $folder = "more_info/{$user->id}";

        $cccdFrontPath = $request->file('cccd_front')->store($folder, 'public');
        $cccdBackPath  = $request->file('cccd_back')->store($folder, 'public');


        $moreInfo = MoreInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'address'    => $request->input('address'),
                'cccd_front' => $cccdFrontPath,
                'cccd_back'  => $cccdBackPath,
                'status'     => 'pending', // trạng thái xét duyệt
            ]
        );

        // Cập nhật role của user thành Landlord
        $user->role = 'Landlord';
        $user->save();

        return redirect()->route('renter')->with('success', 'Đăng ký chủ trọ thành công! Vui lòng chờ admin xét duyệt.');
    }
}
