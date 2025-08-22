<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\StaffPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

   public function show(StaffPost $post)
{
    if ($post->status != 1 || !$post->is_public || ($post->room && $post->room->is_contract_locked)) {
        $post->load(['category', 'features', 'property']);
        // dd($post);
        return view('home.detailPost', compact('post'));
    }

    $post->load(['category', 'features', 'property', 'room']);
    return view('home.detailPost', compact('post'));
}

    public function suggestNearby(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = 50; // km

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json(['message' => 'Toạ độ không hợp lệ.'], 422);
        }

        $posts = StaffPost::select(
            'staff_posts.post_id',
            'staff_posts.title',
            'staff_posts.slug',
            'staff_posts.price',
            'staff_posts.area',
            'staff_posts.address',
            'staff_posts.city',
            'staff_posts.district',
            'staff_posts.thumbnail',
            'staff_posts.post_code',
            DB::raw("(
            6371 * acos(
                cos(radians(?)) *
                cos(radians(staff_posts.latitude)) *
                cos(radians(staff_posts.longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(staff_posts.latitude))
            )
        ) AS distance")
        )
            ->addBinding([$lat, $lng, $lat], 'select')
            ->join('rooms', 'rooms.room_id', '=', 'staff_posts.room_id') // 🔑 join rooms
            ->where('staff_posts.status', 1)
            ->where('staff_posts.is_public', true)
            ->where('rooms.is_contract_locked', false) // ✅ chỉ lấy phòng chưa khóa
            ->havingRaw('distance <= ?', [$radius])
            ->orderBy('distance')
            ->limit(6)
            ->get()
            ->map(function ($post) {
                $post->distance = round($post->distance, 2);
                return $post;
            });

        return response()->json($posts);
    }
}
