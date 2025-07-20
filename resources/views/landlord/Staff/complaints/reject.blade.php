@extends('landlord.layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <h2 class="text-danger mb-4">❌ Từ chối xử lý khiếu nại #{{ $complaint->id }}</h2>

    {{-- Thông tin người gửi & phòng --}}
    <div class="card mb-4">
        <div class="card-body row">
            <div class="col-md-6">
                <p><strong>Phòng:</strong> {{ $complaint->room->room_number ?? '---' }}</p>
                <p><strong>Người gửi:</strong> {{ $complaint->full_name }}</p>
                <p><strong>SĐT:</strong> {{ $complaint->phone }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Tòa:</strong> {{ $complaint->property->name ?? '---' }}</p>
                <p><strong>Ngày gửi:</strong> {{ $complaint->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Nội dung khiếu nại --}}
    <div class="card mb-4">
        <div class="card-body">
            <p class="fw-semibold mb-2">📄 <strong>Nội dung khiếu nại:</strong></p>
            <div class="text-muted">{{ $complaint->detail }}</div>
        </div>
    </div>

    {{-- Ảnh đính kèm --}}
    @if ($complaint->photos && $complaint->photos->count())
    <div class="card mb-4">
        <div class="card-body">
            <p class="fw-semibold mb-3">🖼️ <strong>Ảnh đính kèm:</strong></p>
            <div class="d-flex flex-wrap gap-3">
                @foreach ($complaint->photos as $photo)
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}"
                             class="rounded border shadow-sm"
                             style="width: 200px; object-fit: cover;">
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Form từ chối --}}
    <div class="card mb-5">
        <div class="card-body">
            <form action="{{ route('landlords.staff.complaints.reject', $complaint->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="reject_reason" class="form-label fw-semibold">📝 Lý do từ chối:</label>
                    <textarea name="reject_reason" id="reject_reason"
                              rows="6"
                              class="form-control"
                              placeholder="Vui lòng nhập lý do rõ ràng và cụ thể..." required></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-danger">
                        Gửi lý do từ chối
                    </button>
                    <a href="{{ route('landlords.staff.complaints.index') }}" class="text-decoration-underline text-primary">
                        ⬅ Quay lại danh sách
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection