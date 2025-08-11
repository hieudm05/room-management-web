@extends('home.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4 text-center mb-4">Chỉnh sửa khiếu nại</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('home.complaints.update', $complaint) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Họ tên:</label>
                    <input type="text" name="full_name" class="form-control"
                           value="{{ old('full_name', $complaint->full_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại:</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone', $complaint->phone) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Vấn đề:</label>
                    <select name="common_issue_id" class="form-select" required>
                        @foreach ($commonIssues as $issue)
                            <option value="{{ $issue->id }}" {{ $complaint->common_issue_id == $issue->id ? 'selected' : '' }}>
                                {{ $issue->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả:</label>
                    <textarea name="detail" class="form-control" rows="4">{{ old('detail', $complaint->detail) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Thêm ảnh mới (tùy chọn):</label>
                    <input type="file" name="photos[]" multiple class="form-control" accept="image/*">
                </div>

                @if ($complaint->photos->count())
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($complaint->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
