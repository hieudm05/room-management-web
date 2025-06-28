<?php
// app/Http/Controllers/Landlord/PropertyBankAccountController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\BankAccount;
use App\Models\Landlord\Property;
use Illuminate\Http\Request;

class PropertyBankAccountController extends Controller
{
     public function update(Request $request, $property_id)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id'
        ]);

        $property = Property::findOrFail($property_id);
        $property->bank_account_id = $request->bank_account_id;
        $property->save();

        return back()->with('success', 'Cập nhật tài khoản ngân hàng thành công!');
    }

    // Huỷ gán tài khoản ngân hàng cho tòa
    public function unassign($property_id)
    {
        $property = Property::findOrFail($property_id);
        $property->bank_account_id = null;
        $property->save();

        return back()->with('success', 'Huỷ gán tài khoản ngân hàng thành công!');
    }
}