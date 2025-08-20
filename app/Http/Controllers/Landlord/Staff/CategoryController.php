<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('staff.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('staff.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                'regex:/^[\pL\s\d\-]+$/u', // Chỉ cho phép chữ, số, khoảng trắng, gạch ngang
            ],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là chuỗi.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.regex' => 'Tên danh mục không được chứa ký tự đặc biệt.',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('staff.categories.index')->with('success', 'Tạo danh mục thành công.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('staff.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $id,
                'regex:/^[\pL\s\d\-]+$/u',
            ],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là chuỗi.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.regex' => 'Tên danh mục không được chứa ký tự đặc biệt.',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('staff.categories.index')->with('success', 'Cập nhật thành công.');
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return redirect()->route('staff.categories.index')->with('success', 'Xóa thành công.');
    }
}
