@extends('landlord.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white fw-bold">
                <i class="bi bi-plus-circle"></i> Thêm Feature
            </div>
            <div class="card-body">
                <form action="{{ route('features.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên Feature</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="Nhập tên..." value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button class="btn btn-success shadow"><i class="bi bi-check-circle"></i> Lưu</button>
                    <a href="{{ route('features.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay
                        lại</a>
                </form>
            </div>
        </div>
    </div>
@endsection
