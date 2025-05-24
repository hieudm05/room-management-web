<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Facility;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;

use App\Models\Landlord\RoomPhoto;
use App\Models\Landlord\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['facilities', 'property', 'photos', 'services'])
            ->withCount('facilities')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('landlord.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $properties = Property::all(); // Lấy toàn bộ danh sách khu trọ
        $facilities = Facility::all();
        $services = Service::all();

        return view('landlord.rooms.create', compact('facilities', 'properties', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,property_id',
            'room_number' => 'required|string|max:50',
            'area' => 'required|numeric|min:1',
            'rental_price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Hidden,Suspended,Confirmed',
            'facilities' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        $exists = Room::where('property_id', $request->property_id)
            ->where('room_number', $request->room_number)
            ->exists();

        if ($exists) {
            return back()->withErrors(['room_number' => 'Phòng này đã tồn tại trong khu.'])->withInput();
        }

        $room = Room::create($request->only([
            'property_id',
            'room_number',
            'area',
            'rental_price',
            'status'
        ]));

        // Gắn tiện nghi nếu có
        $room->facilities()->sync($request->facilities ?? []);

        // Lưu ảnh nếu có
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('uploads/rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->room_id,
                    'image_url' => '/storage/' . $path,
                ]);
            }
        }

        // Gắn dịch vụ nếu có
        if ($request->filled('services')) {
            $serviceData = [];

            foreach ($request->input('services') as $serviceId => $data) {
                if (isset($data['enabled'])) {
                    $serviceData[$serviceId] = [
                        'is_free' => empty($data['price']),
                        'price' => $data['price'] ?? null
                    ];
                }
            }

            $room->services()->sync($serviceData);
        }

        return redirect()->route('landlords.rooms.index')
            ->with('success', 'Bạn đã thêm phòng thành công!');
    }


    public function show(Room $room)
    {
        $room->load('property', 'facilities', 'photos', 'services'); // 🔹 thêm services
        return view('landlord.rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $facilities = Facility::all();
        $services = Service::all(); // 🔹 Thêm
        $roomFacilities = $room->facilities->pluck('facility_id')->toArray();

        // 🔹 Dịch vụ đã gán (dùng pivot)
        $roomServices = $room->services->mapWithKeys(function ($service) {
            return [
                $service->service_id => [
                    'is_free' => $service->pivot->is_free,
                    'price' => $service->pivot->price,
                ]
            ];
        })->toArray();

        return view('landlord.rooms.edit', compact('room', 'facilities', 'roomFacilities', 'services', 'roomServices'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'area' => 'required|numeric|min:1',
            'rental_price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Hidden,Suspended,Confirmed',
            'facilities' => 'array',
            'photos.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'delete_photos' => 'array',
            'delete_photos.*' => 'integer|exists:room_photos,photo_id',
        ]);

        $room->update($request->only(['area', 'rental_price', 'status']));
        $room->facilities()->sync($request->facilities);

        // ❌ XÓA ảnh được chọn
        if ($request->has('delete_photos')) {
            $photosToDelete = RoomPhoto::whereIn('photo_id', $request->delete_photos)->get();
            foreach ($photosToDelete as $photo) {
                // Xoá file khỏi storage nếu bạn lưu ảnh trên server
                if (Storage::disk('public')->exists(str_replace('/storage/', '', $photo->image_url))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $photo->image_url));
                }
                $photo->delete();
            }
        }

        // ✅ LƯU ảnh mới nếu có
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('uploads/rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->room_id,
                    'image_url' => '/storage/' . $path,
                ]);
            }
        }

        // 🔄 Cập nhật dịch vụ
        if ($request->filled('services')) {
            $serviceData = [];

            foreach ($request->input('services') as $serviceId => $data) {
                if (isset($data['enabled'])) {
                    $serviceData[$serviceId] = [
                        'is_free' => empty($data['price']),
                        'price' => $data['price'] ?? null
                    ];
                }
            }

            $room->services()->sync($serviceData); // Ghi đè các dịch vụ cũ
        } else {
            $room->services()->detach(); // Không chọn gì thì xoá hết
        }

        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Cập nhật phòng thành công!');
    }

    public function hide(Room $room)
    {
        $room->status = 'Hidden';
        $room->save();
        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Bạn đã xóa phòng thành công!');
    }

    public function destroy(Room $room)
    {
        if ($room->rentalAgreements()->exists()) {
            return back()->withErrors(['delete' => 'Không thể xóa phòng có hợp đồng thuê.']);
        }
        $room->facilities()->detach();
        $room->delete();
        return redirect()->route('landlords.rooms.index', ['property_id' => $room->property_id])
            ->with('success', 'Phòng đã được xóa thành công!');
    }
}
