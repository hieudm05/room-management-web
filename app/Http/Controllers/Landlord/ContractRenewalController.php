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
        'room.currentAgreement',
        'room.staffs',
        'user'
    ])
    ->where(function ($query) {
        $query->whereHas('room.staffs', function ($q) {
            // Trường hợp là nhân viên phụ trách
            $q->where('users.id', auth()->id());
        })
        ->orWhereHas('room.currentAgreement', function ($q) {
            // Trường hợp là chủ trọ của hợp đồng hiện tại
            $q->where('landlord_id', auth()->id());
        });
    })
    ->where('status', 'pending')
    ->latest()
    ->get();

    return view('landlord.Staff.ContractRenewal.index', compact('renewals'));
}

}
