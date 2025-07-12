<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomEditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRoomEditController extends Controller
{
    public function edit(Room $room)
    {
        if (!$room->staffs->contains(Auth::id())) {
            abort(403, 'Bạn không có quyền truy cập phòng này');
        }

        $facilities = \App\Models\Landlord\Facility::where('name', '!=', 'Thang máy')->get();
        $services = \App\Models\Landlord\Service::all();
        $roomFacilities = $room->facilities->pluck('facility_id')->toArray();
        $roomServices = $room->services->mapWithKeys(function ($service) {
            return [
                $service->service_id => [
                    'is_free' => $service->pivot->is_free,
                    'price' => $service->pivot->price,
                    'unit' => $service->pivot->unit,
                ]
            ];
        })->toArray();

        return view('landlord.staff.rooms.edit', compact('room', 'facilities', 'services', 'roomFacilities', 'roomServices'));
    }

    public function submitRequest(Request $request, Room $room)
{
    if (!$room->staffs->contains(Auth::id())) {
        abort(403, 'Bạn không được phép sửa phòng này');
    }

    $request->validate([
        'area' => 'required|numeric|min:1',
        'rental_price' => 'required|numeric|min:0',
        'status' => 'required|in:Available,Rented,Hidden,Suspended,Confirmed',
        'occupants' => 'required|integer|min:0',
        'deposit_price' => 'nullable|numeric|min:0',
    ]);

    $original = $room->only(['area', 'rental_price', 'status', 'occupants', 'deposit_price']);
    $requested = $request->only(['area', 'rental_price', 'status', 'occupants', 'deposit_price']);

    $diff = array_diff_assoc($requested, $original);

    // So sánh tiện nghi
    $newFacilities = $request->input('facilities', []);
    $oldFacilities = $room->facilities->pluck('facility_id')->toArray();
    sort($newFacilities);
    sort($oldFacilities);
    if ($newFacilities !== $oldFacilities) {
        $diff['facilities'] = $newFacilities;
    }

    // So sánh dịch vụ
    $newServices = $request->input('services', []);
    $serviceChanges = [];
    foreach (\App\Models\Landlord\Service::all() as $service) {
        $sid = $service->service_id;
        $old = $room->services->firstWhere('service_id', $sid);
        $oldPrice = $old ? $old->pivot->price : null;
        $oldUnit = $old ? $old->pivot->unit : null;
        $wasEnabled = $old !== null;

        $new = $newServices[$sid] ?? [];
        $isEnabled = isset($new['enabled']);
        $newPrice = $new['price'] ?? null;
        $newUnit = $new['unit'] ?? null;

        if ($isEnabled != $wasEnabled || $newPrice != $oldPrice || $newUnit != $oldUnit) {
            $serviceChanges[$sid] = [
                'enabled' => $isEnabled,
                'price' => $newPrice,
                'unit' => $newUnit,
            ];
        }
    }
    if (!empty($serviceChanges)) {
        $diff['services'] = $serviceChanges;
    }

    // Ảnh bị xóa
    $deletePhotos = $request->input('delete_photos', []);
    if (!empty($deletePhotos)) {
        $diff['delete_photos'] = $deletePhotos;
    }

    // Ảnh mới
    if ($request->hasFile('photos')) {
        $photoFiles = [];
        foreach ($request->file('photos') as $file) {
            $photoFiles[] = $file->getClientOriginalName(); // Hoặc uuid nếu muốn
        }
        $diff['new_photos'] = $photoFiles;
    }

    if (empty($diff)) {
        return back()->with('info', 'Bạn chưa thay đổi thông tin nào.');
    }

    RoomEditRequest::create([
        'room_id' => $room->room_id,
        'staff_id' => Auth::id(),
        'original_data' => json_encode($original),
        'requested_data' => json_encode($diff),
        'status' => 'pending',
    ]);

    return redirect()->route('landlords.staff.index')->with('success', 'Yêu cầu sửa đã được gửi!');
}

}
