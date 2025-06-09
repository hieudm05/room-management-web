<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(!\Illuminate\Support\Facades\Auth::check()){
            \dd('Bạn chưa đăng nhập');
            // return \redirect()->route('auth.login');
        }

        // $userRole = \auth()->user()->role;

        // if(!in_array($userRole, $roles)){
        //     \abort(403, 'Unauthorized action.');
        // }
        return $next($request);

    }
    protected function redirectTo($request)
{
    if (! $request->expectsJson()) {
        return route('auth.login'); // Đổi tên route login phù hợp với bạn
    }
}
}
