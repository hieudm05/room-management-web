@extends('landlord.layouts.app')

@section('title', 'Sửa tiện nghi')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">✏️ Sửa tiện nghi</h5>
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

            {{-- Form sửa tiện nghi --}}
            <form action="{{ route('landlords.facilities.update', $facility->facility_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Tên tiện nghi</label>
                    <input type="text" name="name" class="form-control" id="name"
                        value="{{ old('name', $facility->name) }}" required>
                </div>

                <button type="submit" class="btn btn-success">💾 Cập nhật</button>
                <a href="{{ route('landlords.facilities.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection
