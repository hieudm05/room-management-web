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
        $staffs = auth()->user()->staffs()->with('bankAccounts')->get();
        return view('landlord.Bank.index', compact('bankAccounts', 'staffs'));
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
            'status' => 'required|in:active,inactive',
        ]);

        $user = auth()->user();

        // Tìm tài khoản ngân hàng theo id
        $bank = \App\Models\Landlord\BankAccount::findOrFail($id);

        // Kiểm tra quyền: là chủ trọ của tài khoản, hoặc là staff thuộc landlord, hoặc là chính staff đó
       // Chủ trọ được sửa tài khoản của mình hoặc của nhân viên (staff) thuộc quyền quản lý của mình
    $isLandlord = $user->id == $bank->user_id;
    $isStaffOfLandlord = $user->staffs()->where('id', $bank->user_id)->exists();


        if (!($isLandlord || $isStaffOfLandlord)) {
            abort(403, 'Bạn không có quyền cập nhật tài khoản này');
        }

        $bank->status = $request->input('status');
        $bank->save();

        return back()->with('success', 'Cập nhật trạng thái thành công!');
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

        // Lấy tài khoản của landlord
        $landlordBankAccounts = $user->bankAccounts()->get();

        // Lấy tài khoản của staff
        $staffs = $user->staffs()->with('bankAccounts')->get();
        $staffBankAccounts = collect();
        foreach ($staffs as $staff) {
            foreach ($staff->bankAccounts as $bank) {
                // Gắn thêm tên staff để hiển thị
                $bank->owner_name = $staff->name . ' (Quản lý)';
                $staffBankAccounts->push($bank);
            }
        }

        // Gộp lại
        $bankAccounts = $landlordBankAccounts->map(function ($b) {
            $b->owner_name = 'Chủ trọ';
            return $b;
        })->concat($staffBankAccounts);

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

    // Lưu tài khoản ngân hàng cho staff
    public function storeForStaff(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'required|string|max:10',
            'bank_account_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:50',
        ]);
        $user = auth()->user(); // chủ trọ
        $staff = $user->staffs()
            ->where('id', $request->staff_id)
            ->where('role', 'Staff') // nếu có
            ->firstOrFail();

        $staff->bankAccounts()->create($request->only(
            'bank_name',
            'bank_code',
            'bank_account_name',
            'bank_account_number'
        ));

        return back()->with('success', 'Thêm tài khoản ngân hàng cho quản lý thành công.');
    }
}
