<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Landlord\Facility;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::all();
        return view('landlord.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('landlord.facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Facility::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('landlords.facilities.index')->with('success', 'Thêm tiện nghi thành công!');
    }

    public function edit(Facility $facility)
    {
        return view('landlord.facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $facility->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('landlords.facilities.index')->with('success', 'Cập nhật tiện nghi thành công!');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return back()->with('success', 'Xoá tiện nghi thành công!');
    }
}
