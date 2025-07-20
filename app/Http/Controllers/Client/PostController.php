<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\StaffPost;

class PostController extends Controller
{
    public function index()
    {
        $posts = StaffPost::with(['category', 'features', 'property'])
            ->where('status', 1)
            ->where('is_public', true)
            ->latest('approved_at')
            ->paginate(6);
        // dd($posts);
        return view('home.render', compact('posts'));
    }


    public function show(StaffPost $post)
    {
        if ($post->status != 1 || !$post->is_public) {
            abort(404);
        }

        $post->load(['category', 'features', 'property']);

        return view('home.detailPost', compact('post'));
    }
}
