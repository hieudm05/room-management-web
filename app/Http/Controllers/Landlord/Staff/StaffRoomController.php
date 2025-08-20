<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\ImageDeposit;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRoomController extends Controller
{
    //

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Room::with(['facilities', 'property', 'photos', 'services'])
            ->withCount('facilities')
            ->orderBy('created_at', 'desc');

        if ($user->role === 'Staff') {
            $roomIds = RoomStaff::where('staff_id', $user->id)
                ->where('status', 'active')
                ->pluck('room_id')
                ->toArray();
            if (!empty($roomIds)) {
                $query->whereIn('room_id', $roomIds);
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('property', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('facilities', function ($q3) use ($search) {
                        $q3->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('rental_price', 'LIKE', "%{$search}%")
                    ->orWhere('area', 'LIKE', "%{$search}%");
            });
        }

        if ($propertyId = $request->input('property_id')) {
            $query->where('property_id', $propertyId);
        }

        if ($range = $request->input('price_range')) {
            if (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                $query->whereBetween('rental_price', [(int) $min, (int) $max]);
            } elseif (is_numeric($range)) {
                $query->where('rental_price', '>', (int) $range);
            }
        }

        if ($request->filled('price_min')) {
            $query->where('rental_price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('rental_price', '<=', $request->input('price_max'));
        }

        $rooms = $query->paginate(8);
        // Lấy property tương ứng các phòng mà nhân viên quản lý
        if (!empty($roomIds)) {
            $allProperties = Property::whereHas('rooms', function ($q) use ($roomIds) {
                $q->whereIn('room_id', $roomIds);
            })->get();
        } else {
            $allProperties = collect();
        }


        return view('landlord.Staff.rooms.list', compact('rooms', 'allProperties'));
    }
    public function show(Room $room)
    {
        // dd($room);
        return view('landlord.Staff.rooms.show', compact("room"));
    }
    public function depositForm($room_id)
    {
        $room = Room::with('property')->findOrFail($room_id);

        // Nếu phòng không ở trạng thái Available thì không cho vào form
        if ($room->status !== 'Available') {
            return redirect()
                ->route('landlords.staff.contract.index', $room)
                ->withErrors('Phòng này hiện không khả dụng để đặt cọc!');
        }

        // Lấy danh sách minh chứng đã nộp (từ approvals, chờ duyệt)
        $deposits = ImageDeposit::where('room_id', $room_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('landlord.Staff.rooms.deposit', compact('room', 'deposits'));
    }

    public function depositUpload(Request $request, Room $room)
    {
        // Kiểm tra trạng thái phòng trước khi cho upload
        if ($room->status !== 'Available') {
            return back()->withErrors('Phòng này không khả dụng để đặt cọc!');
        }

        $request->validate([
            'deposit_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('deposit_image')->store('deposits', 'public');

        $landlordId = optional($room->property)->landlord_id;
        if (!$landlordId) {
            return back()->withErrors('Không tìm thấy chủ trọ của phòng này!');
        }

        Approval::create([
            'room_id'     => $room->room_id,
            'staff_id'    => Auth::id(),
            'landlord_id' => $landlordId,
            'file_path'   => $path,
            'type'        => 'deposit_image',
            'status'      => 'pending',
            'note'        => 'Staff tải ảnh đặt cọc, chờ landlord duyệt.',
        ]);

        return redirect()
            ->route('landlords.staff.deposit.form', $room->room_id)
            ->with('success', 'Ảnh đặt cọc đã được tải lên và gửi cho landlord duyệt!');
    }
}
