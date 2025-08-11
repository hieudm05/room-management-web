<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\StaffPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PostApprovalController extends Controller
{
    // Danh sách bài đăng cần duyệt
    public function index()
    {
        $landlordId = Auth::id();

        $pendingPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 0)
            ->latest()
            ->paginate(10);

        $approvedPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 1)
            ->latest()
            ->paginate(10);

        $rejectedPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 2)
            ->latest()
            ->paginate(10);

        $hiddenPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('landlord_id', $landlordId)
            ->where('status', 1) // Đã duyệt nhưng ẩn
            ->where('is_public', 0)
            ->latest()
            ->paginate(10);

        return view('landlord.posts.approval.index', compact('pendingPosts', 'approvedPosts', 'rejectedPosts', 'hiddenPosts'));
    }


    // Duyệt bài đăng
    public function approve(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền duyệt bài viết này.');
        }

        // Chỉ cần cập nhật trạng thái bài được duyệt
        $post->update([
            'status' => 1,
            'is_public' => 1, // vẫn để true nếu bạn dùng để lọc bài public
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejected_reason' => null,
            'published_at' => now(),
        ]);

        return redirect()->route('landlord.posts.approval.index')
            ->with('success', 'Bài viết đã được duyệt.');
    }




    // Từ chối bài đăng
    public function reject(Request $request, StaffPost $post)
    {
        // Kiểm tra quyền duyệt
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền từ chối bài viết này.');
        }

        $request->validate([
            'rejected_reason' => 'required|string|max:255',
        ]);

        $post->update([
            'status' => 2,
            'rejected_reason' => $request->rejected_reason,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('landlord.posts.approval.index')->with('success', 'Đã từ chối bài viết thành công.');
    }

    // Xem chi tiết bài đăng
    public function show(StaffPost $post)
    {
        // Kiểm tra quyền xem
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem bài viết này.');
        }

        $post->load(['category', 'features', 'property']);

        return view('landlord.posts.approval.show', compact('post'));
    }
    public function hide(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403);
        }

        $post->update(['is_public' => 0]);

        return redirect()->route('landlord.posts.approval.index', ['tab' => 'approved'])->with('success', 'Đã ẩn bài viết.');
    }

    public function unhide(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403);
        }

        $post->update(['is_public' => 1]);

        return redirect()->route('landlord.posts.approval.index', ['tab' => 'hidden'])->with('success', 'Đã hiển thị lại bài viết.');
    }
}
