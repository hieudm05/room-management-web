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
        if ($post->status != 1 || !$post->is_public) {
            abort(404);
        }

        $post->load(['category', 'features', 'property']);
        return view('home.detailPost', compact('post'));
    }

    public function suggestNearby(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = 10; // km

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json(['message' => 'Toạ độ không hợp lệ.'], 422);
        }

        $posts = StaffPost::select(
            'post_id',
            'title',
            'slug',
            'price',
            'area',
            'address',
            'city',
            'district',
            'thumbnail',
            'post_code',
            DB::raw("(
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance")
        )
            ->addBinding([$lat, $lng, $lat], 'select')
            ->where('status', 1)
            ->where('is_public', true)
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
