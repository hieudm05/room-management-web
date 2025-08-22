<?php

// app/Http/Controllers/FeatureController.php
namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::latest()->paginate(10);
        return view('staff.features.index', compact('features'));
    }

    public function create()
    {
        return view('staff.features.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
        ]);

        Feature::create([
            'name' => $request->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('features.index')->with('success', 'Thêm thành công');
    }

    public function show(Feature $feature)
    {
        return view('staff.features.show', compact('feature'));
    }

    public function edit(Feature $feature)
    {
        return view('staff.features.edit', compact('feature'));
    }

    public function update(Request $request, Feature $feature)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
        ]);

        $feature->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);

        return redirect()->route('features.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();
        return redirect()->route('features.index')->with('success', 'Xóa thành công');
    }
}
