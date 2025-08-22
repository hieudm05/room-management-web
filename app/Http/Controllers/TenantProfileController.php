<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantProfileController extends Controller
{
    // Hiển thị form chỉnh sửa profile tenant
    public function edit()
    {
        $user = Auth::user();

        $userInfo = UserInfo::select([
            'id',
            'full_name',
            'user_id',
            'tenant_id',
            'room_id',
            'rental_id',
            'active',
            'left_at',
            'cccd',
            'phone',
            'email',
            'avatar',
            'days_stayed', // ✅ thêm trường này
            'created_at',
            'updated_at'
        ])
            ->where('user_id', $user->id)
            ->first();

        // Nếu chưa có tenant thì tạo object trống
        $tenant = $user->tenant ?? new \App\Models\Tenant(['user_id' => $user->id]);

        // Map profile
        $profile = $userInfo ? $userInfo->toArray() : [
            'id' => null,
            'full_name' => '',
            'user_id' => $user->id,
            'tenant_id' => null,
            'room_id' => null,
            'rental_id' => null,
            'active' => null,
            'left_at' => null,
            'cccd' => '',
            'phone' => '',
            'email' => '',
            'avatar' => null,
            'days_stayed' => 0, // ✅ mặc định 0 ngày
            'created_at' => null,
            'updated_at' => null
        ];

        $profile['role'] = $user->role;
        $profile['is_active'] = $user->is_active;

        return view('profile.tenants.edit', compact('tenant', 'profile'));
    }


    // Cập nhật thông tin cá nhân (trừ avatar)
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9,10}$/'],
            'cccd' => ['required', 'string', 'max:20', 'regex:/^\d{9}|\d{12}$/'],
            'email' => ['required', 'email', 'max:255'],
            'dob' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'job' => ['nullable', 'string', 'max:255'],
        ], [
            'full_name.required' => 'Họ và tên là bắt buộc.',
            'full_name.string' => 'Họ và tên phải là chuỗi ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone.regex' => 'Số điện thoại không hợp lệ. Ví dụ: 0912345678 hoặc +84912345678.',

            'cccd.required' => 'Số CMND/CCCD là bắt buộc.',
            'cccd.string' => 'Số CMND/CCCD phải là chuỗi ký tự.',
            'cccd.max' => 'Số CMND/CCCD không được vượt quá 20 ký tự.',
            'cccd.regex' => 'Số CMND/CCCD phải gồm 9 hoặc 12 chữ số.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',

            'dob.date' => 'Ngày sinh không đúng định dạng.',
            'dob.before' => 'Ngày sinh phải nhỏ hơn ngày hiện tại.',

            'gender.in' => 'Giới tính không hợp lệ.',

            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',

            'job.string' => 'Nghề nghiệp phải là chuỗi ký tự.',
            'job.max' => 'Nghề nghiệp không được vượt quá 255 ký tự.',
        ]);

        // Lấy user_infos hiện tại (nếu có) để giữ giá trị active
        $userInfo = UserInfo::where('user_id', $user->id)->first();

        // Cập nhật hoặc tạo mới user_infos, giữ nguyên active
        UserInfo::updateOrCreate(
            ['user_id' => $user->id],
            array_merge(
                $request->only([
                    'full_name',
                    'phone',
                    'cccd',
                    'email',
                    'dob',
                    'gender',
                    'address',
                    'job'
                ]),
                ['active' => $userInfo ? $userInfo->active : 0] // Giữ active hiện tại hoặc mặc định là 0
            )
        );

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

        $userInfo = UserInfo::firstOrNew(['user_id' => $user->id]);

        // Xóa avatar cũ nếu tồn tại
        if ($userInfo->avatar && Storage::disk('public')->exists($userInfo->avatar)) {
            Storage::disk('public')->delete($userInfo->avatar);
        }

        // Lưu avatar mới
        $path = $request->file('avatar')->store('avatars', 'public');
        $userInfo->avatar = $path;
        $userInfo->save();
        // dd($userInfo['avatar']);
        return redirect()->route('tenant.profile.edit')->with('success', 'Cập nhật avatar thành công!');
    }
}
