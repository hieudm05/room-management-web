@extends('landlord.layouts.app')

@section('title', '📤 Tải lên minh chứng đặt cọc')

@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 fw-bold">📤 Tải lên minh chứng đặt cọc</h5>
        </div>
        <div class="card-body">

            {{-- Thông tin phòng --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Phòng</label>
                <input type="text" class="form-control"
                    value="{{ $room->room_number }} - {{ $room->property->name }}"
                    disabled>
            </div>

            {{-- Nếu chưa có minh chứng, hiển thị form upload --}}
            @if($deposits->isEmpty())
            <form action="{{ route('landlords.rooms.deposit.upload', $room->room_id) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">📷 Ảnh minh chứng đặt cọc</label>
                    <input type="file" name="deposit_image"
                        class="form-control @error('deposit_image') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png" required>
                    @error('deposit_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success">📤 Tải lên</button>
            </form>
            @else
            {{-- Nếu đã có ảnh, hiển thị ảnh --}}
            <div class="mt-4">
                <h6 class="fw-bold">📑 Minh chứng đã tải lên</h6>
                <div class="row">
                    @foreach($deposits as $deposit)
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset($deposit->image_url) }}" class="card-img-top" alt="Deposit image">
                            <div class="card-body text-center">
                                <small class="text-muted">{{ $deposit->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quay lại --}}
            <div class="mt-4">
                <a href="{{ route('landlords.rooms.show', $room->room_id) }}" class="btn btn-secondary">🔙 Quay lại</a>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert thông báo --}}
@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            position: "center",
            icon: "success",
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
@endif
@endsection
