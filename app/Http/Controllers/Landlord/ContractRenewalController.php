<?php

namespace App\Http\Controllers\Landlord;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Landlord\ContractRenewal;

class ContractRenewalController extends Controller
{
public function index()
{
   $renewals = ContractRenewal::with([
        'room.currentAgreement', // lấy hợp đồng hiện tại của phòng
        'room.staffs',
        'user'
    ])
    ->whereHas('room.staffs', function ($query) {
        $query->where('users.id', auth()->id());
    })
    ->where('status', 'pending')
    ->latest()
    ->get();

    return view('landlord.Staff.ContractRenewal.index', compact('renewals'));
}

}
