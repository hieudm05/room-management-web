<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Landlord\Property;
use Illuminate\Http\Request;
use App\Models\StaffPost;

class PostController extends Controller
{
    public function index()
    {
        // Lấy bài đăng của landlord hiện tại
        $posts = StaffPost::where('landlord_id', auth()->id())
            ->with(['category', 'property', 'features'])
            ->latest()
            ->paginate(10);

        return view('landlord.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $properties = Property::where('landlord_id', auth()->id())->get();
        $features = Feature::all();

        return view('landlord.posts.create', compact('categories', 'properties', 'features'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'title' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'area' => 'required|integer',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'property_id' => 'required|exists:properties,property_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $post = new StaffPost();
        $post->category_id = $request->category_id;
        $post->staff_id = null; // Không phải staff
        $post->landlord_id = auth()->id(); // Landlord đăng
        $post->property_id = $request->property_id;
        $post->post_by = auth()->id();
        $post->room_id = $request->room_id;
        $post->title = $request->title;
        $post->slug = \Str::slug($request->title) . '-' . time();
        $post->price = $request->price;
        $post->area = $request->area;
        $post->address = $request->address;
        $post->district = $request->district;
        $post->ward = $request->ward;
        $post->city = $request->province;
        $post->latitude = $request->latitude;
        $post->longitude = $request->longitude;
        $post->description = $request->description;

        if ($request->hasFile('thumbnail')) {
            $post->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('galleries', 'public');
            }
            $post->gallery = json_encode($gallery);
        }

        $post->status = 0;
        $post->save();

        if ($request->has('features')) {
            $post->features()->sync($request->input('features'));
        }

        return redirect()->route('landlord.posts.index')->with('success', 'Đăng bài thành công');
    }

    public function destroy($post_id)
    {
        $post = StaffPost::where('landlord_id', auth()->id())->findOrFail($post_id);
        $post->delete();

        return redirect()->route('landlord.posts.index')->with('success', 'Đã xóa bài viết thành công.');
    }

    public function show($id)
    {
        $post = StaffPost::where('landlord_id', auth()->id())
            ->with(['category', 'features', 'property'])
            ->findOrFail($id);

        return view('landlord.posts.show', compact('post'));
    }
}
