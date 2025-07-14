@extends('landlord.layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <h2 class="mb-4 text-primary">🔎 Chi tiết khiếu nại #{{ $complaint->id }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Thông tin cơ bản --}}
            <div class="row">
                <div class="col-md-6 mb-3"><strong>Tòa:</strong> {{ $complaint->property->name ?? 'N/A' }}</div>
                <div class="col-md-6 mb-3"><strong>Phòng:</strong> {{ $complaint->room->room_number ?? 'N/A' }}</div>
                <div class="col-md-6 mb-3"><strong>Người gửi:</strong> {{ $complaint->full_name }}</div>
                <div class="col-md-6 mb-3"><strong>SĐT:</strong> {{ $complaint->phone }}</div>
                <div class="col-md-6 mb-3"><strong>Ngày gửi:</strong> {{ $complaint->created_at->format('d/m/Y H:i') }}</div>
                <div class="col-md-12 mb-3"><strong>Nội dung:</strong> {{ $complaint->detail }}</div>
                <div class="col-md-6 mb-3">
                    <strong>Trạng thái:</strong>
                    @switch($complaint->status)
                        @case('pending') <span class="badge bg-warning text-dark">Chờ duyệt</span> @break
                        @case('in_progress') <span class="badge bg-primary">Đang xử lý</span> @break
                        @case('resolved') <span class="badge bg-success">Đã xử lý</span> @break
                        @case('rejected') <span class="badge bg-danger">Từ chối</span> @break
                        @case('cancelled') <span class="badge bg-secondary">Đã hủy</span> @break
                        @default <span class="badge bg-light text-dark">Không rõ</span>
                    @endswitch
                </div>
                @if ($complaint->staff)
                    <div class="col-md-6 mb-3"><strong>Nhân viên xử lý:</strong> {{ $complaint->staff->name }}</div>
                @endif
            </div>

            {{-- Thông tin xử lý nếu đã hoàn tất --}}
            @if ($complaint->status === 'resolved')
                <hr>
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Chi phí người thuê:</strong> {{ number_format($complaint->user_cost) }} VNĐ</div>
                    <div class="col-md-6 mb-3"><strong>Chi phí chủ trọ:</strong> {{ number_format($complaint->landlord_cost) }} VNĐ</div>
                    <div class="col-md-12 mb-3"><strong>Ghi chú xử lý:</strong> {{ $complaint->note }}</div>
                    <div class="col-md-12 mb-3"><strong>Thời gian xử lý:</strong> {{ $complaint->resolved_at }}</div>
                </div>
            @endif

            {{-- Ảnh khiếu nại --}}
            <div class="mb-4">
                <h5>🖼 Ảnh khiếu nại ban đầu:</h5>
                @forelse ($complaint->photos->where('type', 'initial') as $photo)
                    <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" class="img-thumbnail me-2 mb-2" style="max-width: 150px;">
                    </a>
                @empty
                    <p class="text-muted fst-italic">Không có ảnh ban đầu.</p>
                @endforelse
            </div>

            {{-- Ảnh sau xử lý --}}
            <div class="mb-3">
                <h5>📸 Ảnh sau xử lý:</h5>
                @php
                    $resolvedPhotos = $complaint->photos->where('type', 'resolved');
                @endphp
                @if ($resolvedPhotos->isNotEmpty())
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($resolvedPhotos as $photo)
                            <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" width="200" class="img-thumbnail shadow-sm">
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic">Chưa cập nhật ảnh xử lý.</p>
                @endif
            </div>
        </div>
    </div>

    <a href="{{ route('landlord.complaints.index') }}" class="btn btn-secondary mt-4">
        ⬅ Quay lại danh sách
    </a>
</div>
@endsection