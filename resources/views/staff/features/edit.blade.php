@extends('landlord.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="bi bi-pencil-square"></i> Sửa Feature
            </div>
            <div class="card-body">
                <form action="{{ route('features.update', $feature) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên Feature</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $feature->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="btn btn-warning text-dark shadow"><i class="bi bi-save"></i> Cập nhật</button>
                    <a href="{{ route('features.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay
                        lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
