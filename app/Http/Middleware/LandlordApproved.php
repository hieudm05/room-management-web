<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LandlordApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role !== 'Landlord') {
            abort(403, 'Bạn không có quyền truy cập!');
        }

        // Kiểm tra quan hệ moreInfo (một bản ghi, vì user_id là unique)
        $moreInfo = method_exists($user, 'moreInfo') ? $user->moreInfo : null;

        if (!$moreInfo) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đăng ký chủ trọ!');
        }

        // Giả sử bạn thêm cột 'status' trong bảng more_info để trạng thái duyệt
        if (isset($moreInfo->status) && $moreInfo->status !== 'approved') {
            return redirect()->route('home')->with('error', 'Tài khoản đang chờ xét duyệt!');
        }

        return $next($request);
    }
}
