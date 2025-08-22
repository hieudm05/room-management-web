@extends('landlord.layouts.app')

@section('title', 'Quản lý hợp đồng')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bage-primary text-white">
            <h5 class="mb-0 fw-bold">📑 Quản lý hợp đồng thuê phòng</h5>
        </div>
        <div class="card-body">

            {{-- Thông tin phòng --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Phòng</label>
                <input type="text" class="form-control" value="{{ $room->room_number }} - {{ $room->property->name }}" disabled>
            </div>

            {{-- Nếu đã có hợp đồng --}}
            @if ($activeAgreement && $activeAgreement->contract_file)
            <div class="mb-4">
                <label class="form-label fw-bold text-success">📎 Hợp đồng hiện tại</label>
                <div class="mt-2 d-flex gap-2">
                    <a href="{{ asset('storage/' . $activeAgreement->contract_file) }}" target="_blank"
                        class="btn btn-outline-success">
                        👁️ Xem hợp đồng
                    </a>
                </div>
            </div>

            {{-- Nếu chưa có hợp đồng --}}
            @else
            <div class="alert alert-warning">
                ⚠️ Hiện tại chưa có hợp đồng thuê. Bạn có thể tải lên hợp đồng mới.
            </div>
            <form action="{{ route('landlords.rooms.contract.upload', $room->room_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">📤 Tải lên hợp đồng thuê mới (PDF)</label>
                    <input type="file" name="agreement_file" class="form-control" accept=".pdf" required>
                </div>
                <button type="submit" class="btn btn-success">📤 Tải lên hợp đồng</button>
            </form>
            @endif

            {{-- Hợp đồng cũ đã bị vô hiệu --}}
            @if ($terminatedAgreements->count())
            <hr>
            <h5 class="mt-4">📜 Hợp đồng cũ đã bị vô hiệu hóa:</h5>
            <ul class="list-group">
                @foreach ($terminatedAgreements as $agreement)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        🗂️ <strong>{{ $agreement->file_name ?? 'Hợp đồng trước' }}</strong><br>
                        <span class="badge bg-danger">Đã bị vô hiệu hóa</span>
                    </div>
                    <a href="{{ asset('storage/' . $agreement->contract_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">👁️ Xem</a>
                </li>
                @endforeach
            </ul>
            @endif

            {{-- Quay lại --}}
            <div class="mt-4">
                <a href="{{ route('landlords.rooms.contract.contractIndex', $room->room_id) }}" class="btn btn-secondary">
                    🔙 Quay lại phòng này
                </a>
            </div>

        </div>
    </div>
</div>

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
