<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_hidden', false)->get();
        return view('landlord.services.index', compact('services'));
    }


    public function create()
    {
        return view('landlord.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:555',
        ]);

        Service::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('landlords.services.index')->with('success', 'Thêm dịch vụ thành công!');
    }

    public function edit(Service $service)
    {
        return view('landlord.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('landlords.services.index')->with('success', 'Cập nhật dịch vụ thành công!');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Xoá dịch vụ thành công!');
    }

    public function hide(Service $service)
    {
        $service->update(['is_hidden' => true]);
        return redirect()->route('landlords.services.index')->with('success', 'Dịch vụ đã được ẩn.');
    }

    public function unhide(Service $service)
    {
        $service->update(['is_hidden' => false]);
        return redirect()->route('landlords.services.hidden')->with('success', 'Dịch vụ đã được bỏ ẩn.');
    }

    public function hidden()
    {
        $services = Service::where('is_hidden', true)->get();
        return view('landlord.services.hidden', compact('services'));
    }

    public function toggle(Service $service)
    {
        $service->is_hidden = !$service->is_hidden;
        $service->save();

        return response()->json([
            'status' => 'success',
            'is_hidden' => $service->is_hidden,
        ]);
    }
}
