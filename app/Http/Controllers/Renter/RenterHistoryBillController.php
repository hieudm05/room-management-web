<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RenterHistoryBillController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $monthFilter = $request->input('month'); // dạng YYYY-MM

        $billsQuery = DB::table('room_leave_logs as rll')
            ->join('rental_agreements as ra', 'ra.rental_id', '=', 'rll.rental_id')
            ->join('room_bills as rb', 'rb.room_id', '=', 'rll.room_id')
            ->join('rooms as ro', 'ro.room_id', '=', 'rb.room_id')
            ->join('properties as p', 'p.property_id', '=', 'ro.property_id')
            ->where('rll.user_id', $userId)
            ->where('rll.status', '=', 'Approved')
            ->whereColumn('rb.month', '>=', 'ra.start_date')
            ->whereColumn('rb.month', '<=', 'rll.leave_date');

        // Lọc theo tháng nếu có
        if ($monthFilter) {
            $billsQuery->whereRaw("DATE_FORMAT(rb.month, '%Y-%m') = ?", [$monthFilter]);
        }

        $bills = $billsQuery
            ->select(
                'rb.*',
                'ro.room_number as room_name',
                'p.name as property_name',
                'ra.start_date',
                'rll.leave_date'
            )
            ->orderBy('rb.month', 'asc')
            ->get();

        return view('profile.tenants.historyBill', compact('bills', 'monthFilter'));
    }
}
