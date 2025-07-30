@extends('home.layouts.app')

@section('title', 'Thêm Usser vào phòng')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white fw-bold">➕ Thêm User vào phòng</h5>
        </div>
        <div class="card-body">

            {{-- Thông báo thành công --}}
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- Hiển thị lỗi tổng quát --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('room-users.store') }}" method="POST" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                @csrf
                <div class="form-group">
                    <label for="rental_id_display">Mã hợp đồng</label>
                    <input type="text" class="form-control" id="rental_id_display"
                        value="{{ $rental?->rental_id ?? 'Không có hợp đồng' }}" readonly>
                </div>
                <input type="hidden" name="rental_id" value="{{ $rentalId }}">

                {{-- Chọn phòng --}}
                <div class="mb-3">
                    <label for="" class="form-label fw-bold"> Phòng</label>
                    <input type="text" name="room_number" value="{{ $rooms->room_number }}" class="form-control @error('room_id') is-invalid @enderror"
                        readonly>

                    @error('room_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label fw-bold"> Họ và Tên</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label fw-bold"> Phone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}" required>
                    @error('phone')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label fw-bold"> CCCD</label>
                    <input type="text" name="cccd" class="form-control @error('cccd') is-invalid @enderror"
                        value="{{ old('cccd') }}" required>
                    @error('cccd')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label fw-bold"> Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <input type="hidden" name="room_id" value="{{ $roomId }}">

                {{-- Nút xác nhận --}}
                <div class="text-start">
                    <button type="submit" class="btn btn-success">💾 Xác Nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
