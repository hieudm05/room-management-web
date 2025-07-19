@extends('landlord.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-danger mb-4">
                ❗ Lý do từ chối khiếu nại #{{ $complaint->id }}
            </h2>

            <div class="row mb-4">
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

            <div class="mb-4">
                <p><strong>Nội dung khiếu nại:</strong></p>
                <div class="border rounded bg-light p-3 text-muted">
                    {{ $complaint->detail ?? '(Không có mô tả)' }}
                </div>
            </div>

            {{-- Ảnh đính kèm --}}
            @if ($complaint->photos->isNotEmpty())
                <div class="mb-4">
                    <p><strong>Ảnh đính kèm:</strong></p>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($complaint->photos as $photo)
                            <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" width="200" class="img-thumbnail shadow-sm">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-4">
                <p><strong>💬 Lý do từ chối từ nhân viên:</strong></p>
                <div class="border rounded bg-light p-3 text-danger fw-medium">
                    {{ $complaint->reject_reason ?? 'Không có lý do.' }}
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('landlord.complaints.index') }}" class="btn btn-link text-decoration-none">
                    ⬅ Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
