<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Lọc theo role nếu có
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(12);

        return view('admin.AccountManagement.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.AccountManagement.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Admin,Staff,Manager,Landlord,Renter',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'is_active' => 1,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
         $roles = ['Admin', 'Renter', 'Landlord', 'Staff', 'Manager'];

    return view('admin.AccountManagement.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, User $user)
{
    $request->validate([
        'role' => 'required|in:Admin,Renter,Landlord,Staff,Manager',
        'is_active' => 'required|boolean',
    ]);

    if ($user->role === 'Admin' && $request->is_active == 0) {
        return back()->with('error', 'Không thể khóa tài khoản Admin.');
    }

    $user->update([
        'role' => $request->role,
        'is_active' => $request->is_active,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công!');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
