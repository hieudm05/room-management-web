<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthAdminController extends Controller
{
    public function LoginFormAdmin()
    {
        return view('auth.admin.loginAdmin');
    }
}
