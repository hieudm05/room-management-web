<?php

namespace App\Http\Controllers\Client;
use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
class AuthUserController extends Controller
{
    public function RegisterForm()
    {
        return view('Auth.client.registerUser');
    }

    public function register(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|unique:users,name',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'name.unique' => 'Tên đăng nhập đã tồn tại.',
            'password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường và số.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Tạo user mới
        $user = User::create([
            'email' => $request->email,
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);


        // Log the user in
        // auth()->login($user);

        // Redirect to a desired location
        return view('Auth.client.register-success');
    }
   public function loginForm()
{
    return view('Auth.client.singinUser');
}

public function Login(Request $request)
{
    // ✅ Validate dữ liệu đầu vào
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // ✅ Tìm user theo email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return back()->withErrors(['email' => 'Email không tồn tại'])->withInput();
    }

    if (!Hash::check($request->password, $user->password)) {
        return back()->withErrors(['password' => 'Mật khẩu không đúng'])->withInput();
    }

    // ✅ Đăng nhập người dùng
    Auth::login($user, $request->filled('remember'));
    $request->session()->regenerate();

    // ✅ Phân hướng theo vai trò
    if ($user->role === 'Landlord') {
        return redirect()->route('landlords.dashboard')->with('success', 'Đăng nhập thành công với vai trò Landlord');
    }

    return redirect()->route('renter')->with('success', 'Đăng nhập thành công với vai trò Renter');
}
public function logout(Request $request)
{
    Auth::logout();

    // \dd(Auth::user()); // Kiểm tra xem đã đăng xuất chưa

    // Hủy session hiện tại để bảo mật
    $request->session()->invalidate();

    // Tạo session token mới
    $request->session()->regenerateToken();

    // Redirect về trang đăng nhập hoặc trang chủ
    return redirect()->route('auth.login')->with('success', 'Bạn đã đăng xuất thành công.');
}

}
