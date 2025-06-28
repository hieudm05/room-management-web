<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomStaffController extends Controller
{
    public function edit(Room $room)
    {
        $staffs = User::where('role', 'Staff')->get();
        $assigned = $room->staffs->pluck('id')->toArray();

        return view('landlord.rooms.assign_staff', compact('room', 'staffs', 'assigned'));
    }

    public function update(Request $request, Room $room)
{
    // Nếu bấm nút xoá
    if ($request->filled('remove_staff_id')) {
        $room->staffs()->detach($request->remove_staff_id);
        return redirect()->back()->with('success', 'Đã xoá phân quyền.');
    }

    $data = $request->input('staffs', []);

    $syncData = [];

    foreach ($data as $staffId => $info) {
        if (isset($info['assign'])) {
            $syncData[$staffId] = [
                'status' => $info['status'] ?? 'active',
            ];
        }
    }

    $room->staffs()->sync($syncData);

    return redirect()->route('landlords.rooms.index')->with('success', 'Cập nhật phân quyền thành công.');
}

}
