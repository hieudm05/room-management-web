<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomEditRequest;
use Illuminate\Http\Request;
use App\Notifications\RoomEditRequestResultNotification;

class RoomEditRequestController extends Controller
{
    // Hiển thị danh sách yêu cầu chờ duyệt
    public function index()
    {
        $requests = RoomEditRequest::with('room', 'staff')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('landlord.edit_requests.index', compact('requests'));
    }

    // Phê duyệt yêu cầu (chủ bấm "duyệt")
    public function approve($id)
{
    $requestEdit = RoomEditRequest::findOrFail($id);
    $room = $requestEdit->room;

    $room->update(json_decode($requestEdit->requested_data, true));
    $requestEdit->status = 'approved';
    $requestEdit->save();

    // Gửi thông báo
    $requestEdit->staff->notify(new RoomEditRequestResultNotification('approved'));

    return redirect()->back()->with('success', 'Đã duyệt yêu cầu và cập nhật phòng.');
}

    // Từ chối yêu cầu
    public function reject(Request $request, $id)
{
    $request->validate([
        'note' => 'nullable|string|max:1000',
    ]);

    $requestEdit = RoomEditRequest::findOrFail($id);
    $requestEdit->status = 'rejected';
    $requestEdit->note = $request->input('note');
    $requestEdit->save();

    // Gửi thông báo kèm lý do
    $requestEdit->staff->notify(new RoomEditRequestResultNotification('rejected', $requestEdit->note));

    return redirect()->back()->with('success', 'Đã từ chối yêu cầu.');
}


    // Xem chi tiết từng yêu cầu
    public function show($id)
    {
        $requestEdit = RoomEditRequest::with('room.photos', 'room.facilities', 'room.services', 'staff')->findOrFail($id);

        $original = json_decode($requestEdit->original_data, true);
        $requested = json_decode($requestEdit->requested_data, true);

        // Thông tin cơ bản thay đổi
        $changes = [];
        foreach (['area', 'rental_price', 'deposit_price', 'status', 'occupants'] as $key) {
            if (isset($requested[$key]) && $requested[$key] != ($original[$key] ?? null)) {
                $changes[$key] = [
                    'old' => $original[$key] ?? '',
                    'new' => $requested[$key]
                ];
            }
        }

        // Tiện nghi
        $addedFacilities = $removedFacilities = [];
        if (isset($requested['facilities']) && is_array($requested['facilities'])) {
            $facilityMap = \App\Models\Landlord\Facility::pluck('name', 'facility_id');
            $originalFacilities = $requestEdit->room->facilities->pluck('facility_id')->map(fn($id) => (int)$id)->toArray();
            $requestedFacilities = array_map('intval', $requested['facilities']);

            $added = array_diff($requestedFacilities, $originalFacilities);
            $removed = array_diff($originalFacilities, $requestedFacilities);

            $addedFacilities = array_map(fn($id) => $facilityMap[$id] ?? 'Không rõ', $added);
            $removedFacilities = array_map(fn($id) => $facilityMap[$id] ?? 'Không rõ', $removed);
        }

        // Dịch vụ
        $serviceChanges = [];
        if (isset($requested['services']) && is_array($requested['services'])) {
            $serviceMap = \App\Models\Landlord\Service::pluck('name', 'service_id');
            foreach ($requested['services'] as $sid => $change) {
                $sid = (int)$sid;
                $entry = ['name' => $serviceMap[$sid] ?? 'Dịch vụ'];
                $originalService = $requestEdit->room->services->firstWhere('service_id', $sid);

                if ($originalService) {
                    if (($originalService->pivot->price ?? null) != ($change['price'] ?? null)) {
                        $entry['price'] = [
                            'old' => $originalService->pivot->price,
                            'new' => $change['price'] ?? null,
                        ];
                    }
                    if (($originalService->pivot->unit ?? null) != ($change['unit'] ?? null)) {
                        $entry['unit'] = [
                            'old' => $originalService->pivot->unit,
                            'new' => $change['unit'] ?? null,
                        ];
                    }
                    if (($change['enabled'] ?? true) === false) {
                        $entry['enabled'] = ['old' => true, 'new' => false];
                    }
                } else {
                    if (($change['enabled'] ?? false) === true) {
                        $entry['enabled'] = ['old' => false, 'new' => true];
                    }
                }

                if (count($entry) > 1) {
                    $serviceChanges[$sid] = $entry;
                }
            }
        }

        // Ảnh xoá
        $deletedPhotos = [];
        if (isset($requested['delete_photos']) && is_array($requested['delete_photos'])) {
            $deletedPhotos = $requestEdit->room->photos->whereIn('photo_id', $requested['delete_photos']);
        }

        // Ảnh mới
        $newPhotoNames = $requested['new_photos'] ?? [];

        return view('landlord.edit_requests.show', compact(
            'requestEdit',
            'changes',
            'addedFacilities',
            'removedFacilities',
            'serviceChanges',
            'deletedPhotos',
            'newPhotoNames'
        ));
    }
}
