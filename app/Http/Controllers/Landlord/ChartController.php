<?php

namespace App\Http\Controllers\Landlord;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use App\Models\Landlord\Property;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChartController extends Controller
{

public function index(Request $request)
{
    $userId = Auth::id();
    $monthComplaint = $request->input('month_complaint');
    $monthBooking = $request->input('month_booking');

    // Lấy các complaints của staff
    $complaintRooms = Room::with(['property', 'complaints' => function ($q) use ($userId, $monthComplaint) {
        $q->where('staff_id', $userId);
        if ($monthComplaint) {
            $q->whereMonth('created_at', substr($monthComplaint, 5, 2))
              ->whereYear('created_at', substr($monthComplaint, 0, 4));
        }
    }])->get();

    // Lấy danh sách phòng có bookings đã được confirm bởi staff
    $bookingRooms = Room::with('property')->get();

    // Gom theo property_id
    $groupedComplaints = $complaintRooms->groupBy('property_id');
    $groupedBookings = $bookingRooms->groupBy('property_id');

    $allPropertyIds = collect([...$groupedComplaints->keys(), ...$groupedBookings->keys()])->unique();

    $result = $allPropertyIds->map(function ($propertyId) use ($groupedComplaints, $groupedBookings, $userId, $monthBooking) {
        $complaints = [
            'labels' => [],
            'resolved' => [],
            'rejected' => [],
            'cancelled' => [],
        ];

        $bookings = [
            'labels' => [],
            'completed' => [],
            'no-cancel' => [],
        ];

        // Complaints
        foreach ($groupedComplaints->get($propertyId, []) as $room) {
            $counts = $room->complaints->groupBy('status')->map->count();
            $complaints['labels'][] = "Phòng {$room->room_number}";
            $complaints['resolved'][] = $counts['resolved'] ?? 0;
            $complaints['rejected'][] = $counts['rejected'] ?? 0;
            $complaints['cancelled'][] = $counts['cancelled'] ?? 0;
        }

        // Bookings
        foreach ($groupedBookings->get($propertyId, []) as $room) {
            // Truy vấn bookings theo confirmed_by + tháng
            $bookingQuery = $room->bookings()->where('confirmed_by', $userId);
            if ($monthBooking) {
                $bookingQuery->whereMonth('created_at', substr($monthBooking, 5, 2))
                             ->whereYear('created_at', substr($monthBooking, 0, 4));
            }

            $bookingCount = $bookingQuery->selectRaw('status, COUNT(*) as total')
                                         ->groupBy('status')
                                         ->pluck('total', 'status');

            $bookings['labels'][] = "Phòng {$room->room_number}";
            $bookings['completed'][] = $bookingCount['completed'] ?? 0;
            $bookings['no-cancel'][] = $bookingCount['no-cancel'] ?? 0;
        }

        // Lấy tên tòa
        $propertyName = optional(
            $groupedBookings->get($propertyId, collect())->first()
                ?? $groupedComplaints->get($propertyId, collect())->first()
        )->property->name ?? 'Không rõ tên toà';

        return [
            'building_name' => $propertyName,
            'complaints' => $complaints,
            'bookings' => $bookings,
        ];
    });

    if ($request->ajax()) {
        return response()->json($result->values());
    }

    return view('landlord.Staff.complaints.ComplaintsChart', compact('result'));
}


    public function complaintChart(Request $request)
    {
        $monthComplaintInput = $request->input('month_complaint') ?? now()->format('Y-m');
        $monthStart = Carbon::parse($monthComplaintInput)->startOfMonth();
        $monthEnd = Carbon::parse($monthComplaintInput)->endOfMonth();

        $userId = Auth::id();

        $complaints = Complaint::with('room')
            ->where('staff_id', $userId)
            ->whereBetween('resolved_at', [$monthStart, $monthEnd])
            ->get();

        $complaintLabels = $complaints
            ->filter(fn($c) => $c->room)
            ->pluck('room.room_number')
            ->unique()
            ->values()
            ->toArray();

        $resolvedData = [];
        $rejectedData = [];
        $cancelledData = [];

        foreach ($complaintLabels as $roomNumber) {
            $resolvedData[] = $complaints->where('room.room_number', $roomNumber)->where('status', 'resolved')->count();
            $rejectedData[] = $complaints->where('room.room_number', $roomNumber)->where('status', 'rejected')->count();
            $cancelledData[] = $complaints->where('room.room_number', $roomNumber)->where('status', 'cancelled')->count();
        }

        if ($request->ajax()) {
            return response()->json([
                'labels' => $complaintLabels,
                'resolved' => $resolvedData,
                'rejected' => $rejectedData,
                'cancelled' => $cancelledData
            ]);
        }

        return view('landlord.Staff.complaints.ComplaintsChart', [
            'complaintLabels' => $complaintLabels,
            'resolvedData' => $resolvedData,
            'rejectedData' => $rejectedData,
            'cancelledData' => $cancelledData,
            'monthComplaintInput' => $monthComplaintInput
        ]);
    }

    public function bookingChart(Request $request)
{
    $monthBookingInput = $request->input('month_booking') ?? now()->format('Y-m');
    $monthStart = Carbon::parse($monthBookingInput)->startOfMonth();
    $monthEnd = Carbon::parse($monthBookingInput)->endOfMonth();

    $userId = Auth::id();

    // Kiểm tra có booking nào không
    $bookings = Booking::with('room') // <- cần thiết
        ->whereBetween('created_at', [$monthStart, $monthEnd])
        ->where('confirmed_by', $userId)
        ->get();
    // Lấy các phòng có trong danh sách booking
    $bookingLabels = $bookings
        ->filter(fn($b) => $b->room)
        ->pluck('room.room_number')
        ->unique()
        ->values()
        ->toArray();

    $acceptedData = [];
    $rejectedData = [];

    foreach ($bookingLabels as $roomNumber) {
        $roomBookings = $bookings->filter(fn($b) => $b->room && $b->room->room_number === $roomNumber);

        $acceptedData[] = $roomBookings->whereIn('status', ['accepted', 'completed'])->count();
        $rejectedData[] = $roomBookings->whereIn('status', ['rejected', 'no-cancel'])->count();
    }

    if ($request->ajax()) {
        return response()->json([
            'labels' => $bookingLabels,
            'accepted' => $acceptedData,
            'rejected' => $rejectedData
        ]);
    }

    return view('landlord.Staff.complaints.ComplaintsChart', [
        'bookingLabels' => $bookingLabels,
        'acceptedData' => $acceptedData,
        'rejectedData' => $rejectedData,
        'monthBookingInput' => $monthBookingInput
    ]);
}

}
