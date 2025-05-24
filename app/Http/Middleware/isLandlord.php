<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isLandlord
{
//    public function handle(Request $request, Closure $next): Response
// {
//     if (auth()->check() && auth()->user()->role === 'Admin') {
//         return $next($request);
//     }

//     return redirect('/')->with('error', 'Bạn không có quyền truy cập!');
// }
 public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!Auth::check() || !Auth::user()->IsRoleLandlord()){
            // \dd('Bạn chưa đăng nhập');
            return \redirect()->route('auth.login');
        }

        // $userRole = \auth()->user()->role;

        // if(!in_array($userRole, $roles)){
        //     \abort(403, 'Unauthorized action.');
        // }
        return $next($request);

    }
    protected function redirectTo($request)
{
    if (!$request->expectsJson()) {
        return route('auth.login'); // ← thay vì 'login'
    }
}
}
