@extends('home.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow mt-10">
    <h2 class="text-xl font-semibold mb-4 text-center">Chỉnh sửa khiếu nại</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('home.complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-medium">Họ tên:</label>
            <input type="text" name="full_name" class="w-full border rounded p-2"
                   value="{{ old('full_name', $complaint->full_name) }}" required>
        </div>

        <div>
            <label class="block font-medium">Số điện thoại:</label>
            <input type="text" name="phone" class="w-full border rounded p-2"
                   value="{{ old('phone', $complaint->phone) }}" required>
        </div>

        <div>
            <label class="block font-medium">Vấn đề:</label>
            <select name="common_issue_id" class="w-full border rounded p-2" required>
                @foreach ($commonIssues as $issue)
                    <option value="{{ $issue->id }}" {{ $complaint->common_issue_id == $issue->id ? 'selected' : '' }}>
                        {{ $issue->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-medium">Mô tả:</label>
            <textarea name="detail" class="w-full border rounded p-2" rows="4">{{ old('detail', $complaint->detail) }}</textarea>
        </div>

        <div>
            <label class="block font-medium">Thêm ảnh mới (tùy chọn):</label>
            <input type="file" name="photos[]" multiple class="w-full border rounded p-2" accept="image/*">
        </div>

        @if ($complaint->photos->count())
            <div>
                <label class="block font-medium mb-2">Ảnh hiện tại:</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($complaint->photos as $photo)
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-20 h-20 object-cover rounded border" />
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text-center">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
