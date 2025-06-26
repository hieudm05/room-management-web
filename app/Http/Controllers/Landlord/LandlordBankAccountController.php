<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Landlord\Property;

class LandlordBankAccountController extends Controller
{
    /**
     * Hiển thị danh sách ngân hàng của chủ trọ hiện tại.
     */
    public function index()
    {
        $bankAccounts = auth()->user()->bankAccounts()->get();
        return view('landlord.Bank.index', compact('bankAccounts'));
    }

    /**
     * Thêm mới ngân hàng.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        auth()->user()->bankAccounts()->create($request->only(
            'bank_name',
            'bank_account_name',
            'bank_account_number'
        ));

        return back()->with('success', 'Thêm tài khoản ngân hàng thành công.');
    }

    /**
     * Cập nhật ngân hàng.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);

        $bank = auth()->user()->bankAccounts()->findOrFail($id);
        $bank->update($request->only(
            'bank_name',
            'bank_account_name',
            'bank_account_number'
        ));

        return back()->with('success', 'Cập nhật thành công.');
    }

    /**
     * Xóa ngân hàng.
     */
    public function destroy($id)
    {
        $bank = auth()->user()->bankAccounts()->findOrFail($id);
        $bank->delete();

        return back()->with('success', 'Xóa thành công.');
    }
    public function assignToProperties()
    {
        $user = auth()->user();
        $bankAccounts = $user->bankAccounts()->get();
        $properties = $user->properties()->get();

        return view('Landlord.Bank.assign', compact('bankAccounts', 'properties'));
    }
    public function assignToPropertiesStore(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:properties,property_id',
        ]);

        $bankAccountId = $request->bank_account_id;
        $propertyIds = $request->property_ids ?? [];

        $user = auth()->user();

        // 1. Gán tài khoản cho các tòa được tick
        if (!empty($propertyIds)) {
            Property::whereIn('property_id', $propertyIds)
                ->where('landlord_id', $user->id)
                ->update(['bank_account_id' => $bankAccountId]);
        }

        // 2. Huỷ gán tài khoản cho các tòa KHÔNG được tick (nếu trước đó đã gán tài khoản này)
        if (!empty($propertyIds)) {
            Property::where('landlord_id', $user->id)
                ->where('bank_account_id', $bankAccountId)
                ->whereNotIn('property_id', $propertyIds)
                ->update(['bank_account_id' => null]);
        } else {
            // Nếu không tick tòa nào, huỷ gán ở tất cả các tòa đang gán tài khoản này
            Property::where('landlord_id', $user->id)
                ->where('bank_account_id', $bankAccountId)
                ->update(['bank_account_id' => null]);
        }

        return back()->with('success', 'Cập nhật gán tài khoản thành công!');
    }
}
