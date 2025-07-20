<?php

namespace App\Http\Controllers\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StaffAccountController extends Controller
{
    public function index()
    {
        // lấy danh sách tài khoản của staff
        $staffAccounts = User::where('role', 'staff')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return \view('landlord.StaffAccounts.index',compact('staffAccounts'));
    }

  public function create()
{
    // 0 là không hoạt động, 1 là đang hoạt động
    $statuses = [
        1 => 'Đang hoạt động',
        0 => 'Không hoạt động',
    ];

    return view('landlord.StaffAccounts.createSatffAccounts1', compact('statuses'));
}

public function store(Request $request)
{
    $request->validate([
        'name'                  => 'required|string|max:255',
        'email'                 => 'required|email|unique:users,email',
        'identity_number'       => 'required|digits_between:9,12|unique:users,identity_number',
        'is_active'             => 'required|in:0,1',
    ]);

    // Lấy landlord_id từ user đang đăng nhập
    $landlordId = Auth::id();
    // Hoặc nếu bạn dùng multi guard: Auth::guard('landlord')->id()

    // Tạo password random
    $password = Str::random(8);

    // Tạo tài khoản nhân viên
    $staffAccount = User::create([
        'name'            => $request->name,
        'email'           => $request->email,
        'identity_number' => $request->identity_number,
        'is_active'       => $request->is_active,
        'role'            => 'Staff',
        'landlord_id'     => $landlordId, // gán landlord_id ở đây
        'password'        => bcrypt($password),
    ]);

    // Gửi email thông báo tài khoản mới
    Mail::send('landlord.StaffAccounts.emails.new_staff_account', [
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => $password,
    ], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Bạn đã được chủ trọ tạo tài khoản với tư cách là nhân viên');
    });

    return redirect()->route('landlords.staff_accounts.index')
        ->with('success', 'Tạo tài khoản cho nhân viên mới thành công');
}

    public function show($id)
{
    // Lấy thông tin nhân viên + các phòng đang quản lý
    $staff = User::with('rooms')->where('role', 'Staff')->findOrFail($id);

    return view('landlord.StaffAccounts.show', compact('staff'));
}
}
