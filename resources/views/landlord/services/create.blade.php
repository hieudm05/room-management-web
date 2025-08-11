@extends('landlord.layouts.app')

@section('title', 'Thêm dịch vụ')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white ">➕ Thêm dịch vụ</h5>
        </div>
        <div class="card-body">
            {{-- Hiển thị lỗi --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form thêm dịch vụ --}}
            <form action="{{ route('landlords.services.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Tên dịch vụ</label>
                    <input type="text" name="name" class="form-control" id="name"
                        value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Mô tả dịch vụ (hiển thị cạnh checkbox)</label>
                    <input type="text" name="description" class="form-control" id="description"
                        value="{{ old('description') }}">
                    <small class="text-muted">Ví dụ: "Phí thu gom rác định kỳ", "Miễn phí nếu để trống", v.v.</small>
                </div>

                <button type="submit" class="btn btn-success">💾 Lưu</button>
                <a href="{{ route('landlords.services.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection
