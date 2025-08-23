<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;
use App\Models\StaffPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PostController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // ID người đang đăng nhập

        $posts = StaffPost::where('post_by', $userId) // lọc theo người đăng
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
            'move_in_date' => 'required|date|after_or_equal:today', // ✅ validate ngày dọn vào
        ], [
            'move_in_date.required' => 'Ngày dọn vào là bắt buộc.',
            'move_in_date.date' => 'Ngày dọn vào không hợp lệ.',
            'move_in_date.after_or_equal' => 'Ngày dọn vào phải từ hôm nay trở đi.',
        ]);

        $post = new StaffPost();
        $post->category_id = $request->category_id;
        $post->staff_id = null;
        $post->landlord_id = auth()->id();
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
        $post->move_in_date = $request->move_in_date; // ✅ lưu ngày dọn vào

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

    public function edit($id)
    {
        $post = StaffPost::with('features')
            ->where('landlord_id', auth()->id())
            ->findOrFail($id);

        $categories = Category::all();
        $properties = Property::where('landlord_id', auth()->id())->get();
        $features = Feature::all();
        $rooms = Room::where('property_id', $post->property_id)->get();

        return view('landlord.posts.edit', compact('post', 'categories', 'properties', 'features', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $post = StaffPost::where('landlord_id', auth()->id())->findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'title' => 'required|string|max:255',
            'price' => 'required|string|max:255',
            'area' => 'required|integer',
            'address' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'property_id' => [
                'required',
                'exists:properties,property_id',
                function ($attribute, $value, $fail) {
                    if (!Property::where('property_id', $value)->where('landlord_id', auth()->id())->exists()) {
                        $fail('Bất động sản không thuộc quyền sở hữu của bạn.');
                    }
                }
            ],
            'room_id' => [
                'nullable',
                'exists:rooms,room_id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && !Room::where('room_id', $value)->where('property_id', $request->property_id)->exists()) {
                        $fail('Phòng không thuộc bất động sản đã chọn.');
                    }
                }
            ],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'move_in_date' => 'required|date|after_or_equal:today', // ✅ validate ngày dọn vào
        ], [
            'move_in_date.required' => 'Ngày dọn vào là bắt buộc.',
            'move_in_date.date' => 'Ngày dọn vào không hợp lệ.',
            'move_in_date.after_or_equal' => 'Ngày dọn vào phải từ hôm nay trở đi.',
        ]);

        $post->fill([
            'category_id' => $request->category_id,
            'property_id' => $request->property_id,
            'room_id' => $request->room_id ?: null,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'price' => $request->price,
            'area' => $request->area,
            'address' => $request->address,
            'district' => $request->district,
            'ward' => $request->ward,
            'city' => $request->province,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description,
            'move_in_date' => $request->move_in_date, // ✅ lưu ngày dọn vào
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $post->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('gallery')) {
            if ($post->gallery) {
                foreach (json_decode($post->gallery, true) as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('galleries', 'public');
            }
            $post->gallery = json_encode($gallery);
        }

        $post->save();
        $post->features()->sync($request->input('features', []));

        return redirect()->route('landlord.posts.index')->with('success', 'Cập nhật bài viết thành công');
    }
}
