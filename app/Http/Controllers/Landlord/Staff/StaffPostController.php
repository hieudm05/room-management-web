<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffPost;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Landlord\Property as LandlordProperty;
use App\Models\Landlord\Room;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StaffPostController extends Controller
{
    // Danh sách bài viết
    public function index()
    {
        $posts = StaffPost::with(['category', 'features'])->orderBy('created_at', 'desc')->paginate(10);
        return view('staff.posts.index', compact('posts'));
    }

    // Form tạo bài viết
    public function create()
    {
        $categories = Category::all();
        $features = Feature::all();
        $landlords = User::where('role', 'landlord')->get();
        $properties = LandlordProperty::all();
        $rooms = Room::all();

        return view('staff.posts.create', compact('categories', 'features', 'landlords', 'properties', 'rooms'));
    }

    // Lưu bài viết
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
            'landlord_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,property_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $post = new StaffPost();
        $post->category_id = $request->category_id;
        $post->staff_id = auth()->id();
        $post->landlord_id = $request->landlord_id;
        $post->property_id = $request->property_id;
        $post->room_id = $request->room_id;
        $post->title = $request->title;
        $post->slug = Str::slug($request->title) . '-' . time();
        $post->price = $request->price;
        $post->area = $request->area;
        $post->address = $request->address;
        $post->district = $request->district;
        $post->ward = $request->ward;
        $post->city = $request->province;
        $post->latitude = $request->latitude;
        $post->longitude = $request->longitude;
        $post->description = $request->description;

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $path;
        }

        // Upload gallery
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('galleries', 'public');
            }
            $post->gallery = json_encode($gallery);
        }

        $post->status = 0; // Mặc định chờ duyệt
        $post->save();

        // Gán đặc điểm nổi bật (nếu có)
        if ($request->has('features')) {
            $post->features()->sync($request->input('features'));
        }

        return redirect()->route('staff.posts.index')->with('success', 'Đăng bài thành công');
    }


    // Xem chi tiết bài viết
    public function show($id)
    {
        $post = StaffPost::with(['category', 'features'])->findOrFail($id);

        return view('staff.posts.show', compact('post'));
    }



    // Các hàm khác (index, create, store, v.v.)

    public function destroy($post_id)
    {
        $post = StaffPost::findOrFail($post_id);
        $post->delete();

        return redirect()->route('staff.posts.index')->with('success', 'Đã xóa bài viết thành công.');
    }
}
