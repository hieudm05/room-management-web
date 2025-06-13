<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->adminProfile;

        return view('profile.admin.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $profile = $user->adminProfile ?? new AdminProfile(['user_id' => $user->id]);

        $profile->fill($request->only(['full_name', 'phone', 'address']));

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::delete($profile->avatar);
            }
            $path = $request->file('avatar')->store('avatars');
            $profile->avatar = $path;
        }

        $profile->save();

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
