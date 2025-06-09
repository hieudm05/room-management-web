<?php

namespace App\Http\Controllers\Client;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('Auth.client.forgotPassword'); // Tạo view này sau
    }

    // Gửi email reset password
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Gửi link reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        } else {
            return back()->withErrors(['email' => __($status)]);
        }
    }
}
