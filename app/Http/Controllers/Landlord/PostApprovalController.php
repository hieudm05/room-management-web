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

        $allPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('landlord_id', $landlordId)
            ->latest()
            ->paginate(10);

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
            ->where('status', 1)
            ->where('is_public', 0)
            ->latest()
            ->paginate(10);

        return view('landlord.posts.approval.index', compact('allPosts', 'pendingPosts', 'approvedPosts', 'rejectedPosts', 'hiddenPosts'));
    }

    // Duyệt bài đăng
    public function approve(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền duyệt bài viết này.');
        }

        $post->update([
            'status' => 1,
            'is_public' => 1,
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
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền từ chối bài viết này.');
        }

        $request->validate([
            'rejected_reason' => 'required|string|max:255',
            'rejected_reason_detail' => 'nullable|string|max:500',
        ]);

        $rejectedReason = $request->rejected_reason;
        if ($request->rejected_reason === 'Khác' && $request->rejected_reason_detail) {
            $rejectedReason = 'Khác: ' . $request->rejected_reason_detail;
        } elseif ($request->rejected_reason_detail) {
            $rejectedReason .= ': ' . $request->rejected_reason_detail;
        }

        $post->update([
            'status' => 2,
            'rejected_reason' => $rejectedReason,
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->route('landlord.posts.approval.index')->with('success', 'Đã từ chối bài viết thành công.');
    }

    // Xem chi tiết bài đăng
    public function show(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem bài viết này.');
        }

        $post->load(['category', 'features', 'property']);

        return view('landlord.posts.approval.show', compact('post'));
    }

    // Ẩn bài đăng
    public function hide(Request $request, StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền ẩn bài viết này.');
        }

        $request->validate([
            'hidden_reason' => 'required|string|max:255',
        ]);

        $post->update([
            'is_public' => 0,
            'hidden_reason' => $request->hidden_reason,
        ]);

        return redirect()->route('landlord.posts.approval.index', ['tab' => 'approved'])->with('success', 'Đã ẩn bài viết.');
    }

    // Hiện lại bài đăng
    public function unhide(StaffPost $post)
    {
        if ($post->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hiển thị lại bài viết này.');
        }

        $post->update([
            'is_public' => 1,
            'hidden_reason' => null,
        ]);

        return redirect()->route('landlord.posts.approval.index', ['tab' => 'hidden'])->with('success', 'Đã hiển thị lại bài viết.');
    }
}
