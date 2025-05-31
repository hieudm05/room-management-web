<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantProfileController extends Controller
{
    // Hiển thị form chỉnh sửa profile tenant
    public function edit()
    {
        $user = Auth::user();

        // Lấy tenant hoặc tạo mới nếu chưa có
        $tenant = $user->tenant ?? new Tenant(['user_id' => $user->id]);

        return view('profile.tenants.edit', compact('tenant'));
    }

    // Cập nhật thông tin cá nhân (trừ avatar)
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9,10}$/'],
            'identity_number' => ['required', 'string', 'max:20', 'regex:/^\d{9}|\d{12}$/'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'job' => ['nullable', 'string', 'max:255'],
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'phone_number.required' => 'Số điện thoại là bắt buộc.',
            'phone_number.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone_number.regex' => 'Số điện thoại không hợp lệ. Ví dụ: 0912345678 hoặc +84912345678.',

            'identity_number.required' => 'Số CMND/CCCD là bắt buộc.',
            'identity_number.string' => 'Số CMND/CCCD phải là chuỗi ký tự.',
            'identity_number.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự.',
            'identity_number.regex' => 'Số CMND/CCCD phải gồm 9 hoặc 12 chữ số.',

            'dob.date' => 'Ngày sinh không đúng định dạng.',
            'dob.before' => 'Ngày sinh phải nhỏ hơn ngày hiện tại.',

            'gender.required' => 'Giới tính là bắt buộc.',
            'gender.in' => 'Giới tính không hợp lệ.',

            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',

            'job.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'job.max' => 'Nghề nghiệp không được vượt quá 255 ký tự.',
        ]);

        // Lấy tenant hoặc tạo mới nếu chưa có
        $tenant = $user->tenant ?? new Tenant(['user_id' => $user->id]);

        // Điền dữ liệu
        $tenant->fill($request->only([
            'full_name',
            'phone_number',
            'identity_number',
            'dob',
            'gender',
            'address',
            'job'
        ]));

        $tenant->save();

        return redirect()->route('tenant.profile.edit')->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    // Cập nhật avatar riêng biệt
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
        ], [
            'avatar.required' => 'Vui lòng chọn ảnh đại diện.',
            'avatar.image' => 'Tệp tải lên phải là ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, jpg, png hoặc gif.',
            'avatar.max' => 'Kích thước ảnh đại diện tối đa 2MB.',
        ]);

        // Lấy tenant hoặc tạo mới nếu chưa có
        $tenant = $user->tenant ?? new Tenant(['user_id' => $user->id]);

        // Xóa avatar cũ nếu có
        if ($tenant->avatar && Storage::disk('public')->exists($tenant->avatar)) {
            Storage::disk('public')->delete($tenant->avatar);
        }

        $tenant->avatar = $request->file('avatar')->store('avatars', 'public');
        $tenant->save();

        return redirect()->route('tenant.profile.edit')->with('success', 'Cập nhật avatar thành công!');
    }
}
