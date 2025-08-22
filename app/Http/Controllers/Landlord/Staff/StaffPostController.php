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
        $staffId = auth()->id(); // hoặc auth()->user()->id nếu dùng guard mặc định
        // Nếu dùng guard riêng cho staff thì: auth('staff')->id()

        $posts = StaffPost::with(['category', 'features'])
            ->where('staff_id', $staffId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
            'room_id' => 'required|exists:rooms,room_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'move_in_date' => 'required|date|after_or_equal:today',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $post = new StaffPost();
        $post->category_id = $request->category_id;
        $post->post_by = auth()->id();
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
        $post->move_in_date = $request->move_in_date;
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

    // Xóa bài viết
    public function destroy($post_id)
    {
        $post = StaffPost::findOrFail($post_id);
        $post->delete();

        return redirect()->route('staff.posts.index')->with('success', 'Đã xóa bài viết thành công.');
    }

    // Form chỉnh sửa bài viết
    public function edit($id)
    {
        $post = StaffPost::with(['features'])->findOrFail($id);
        $categories = Category::all();
        $features = Feature::all();
        $landlords = User::where('role', 'landlord')->get();
        $properties = LandlordProperty::all();
        $rooms = Room::where('property_id', $post->property_id)->get();

        return view('staff.posts.edit', compact('post', 'categories', 'features', 'landlords', 'properties', 'rooms'));
    }

    // Cập nhật bài viết
    public function update(Request $request, StaffPost $post)
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
            'room_id' => 'required|exists:rooms,room_id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg',
            'move_in_date' => 'required|date',
        ], [
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'price.required' => 'Vui lòng nhập giá.',
            'area.required' => 'Vui lòng nhập diện tích.',
            'area.integer' => 'Diện tích phải là số nguyên.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'province.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'district.required' => 'Vui lòng chọn Quận/Huyện.',
            'ward.required' => 'Vui lòng chọn Phường/Xã.',
            'landlord_id.required' => 'Vui lòng chọn chủ trọ.',
            'property_id.required' => 'Vui lòng chọn bất động sản.',
            'room_id.required' => 'Vui lòng chọn phòng.',
            'latitude.required' => 'Vui lòng nhập tọa độ vĩ độ.',
            'latitude.numeric' => 'Vĩ độ phải là số.',
            'longitude.required' => 'Vui lòng nhập tọa độ kinh độ.',
            'longitude.numeric' => 'Kinh độ phải là số.',
            'thumbnail.image' => 'Ảnh đại diện phải là hình ảnh.',
            'thumbnail.mimes' => 'Ảnh đại diện chỉ hỗ trợ định dạng: jpeg, png, jpg.',
            'gallery.*.image' => 'Ảnh trong bộ sưu tập phải là hình ảnh.',
            'gallery.*.mimes' => 'Ảnh trong bộ sưu tập chỉ hỗ trợ định dạng: jpeg, png, jpg.',
            'move_in_date.required' => 'Vui lòng nhập ngày nhập phòng.',
            'move_in_date.date' => 'Ngày nhập phòng không hợp lệ.',
        ]);

        // Update các field cơ bản
        $post->fill([
            'category_id' => $request->category_id,
            'landlord_id' => $request->landlord_id,
            'property_id' => $request->property_id,
            'room_id' => $request->room_id,
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
            'move_in_date' => $request->move_in_date,
        ]);

        // Update thumbnail
        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $path;
        }

        // Update gallery
        if ($request->hasFile('gallery')) {
            if ($post->gallery) {
                $oldGallery = json_decode($post->gallery, true);
                foreach ($oldGallery as $image) {
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

        // Sync features
        if ($request->has('features')) {
            $post->features()->sync($request->input('features'));
        } else {
            $post->features()->detach();
        }

        return redirect()
            ->route('staff.posts.index')
            ->with('success', 'Cập nhật bài viết thành công');
    }

}
