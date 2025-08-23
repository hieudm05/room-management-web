@extends('home.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Chi tiết khiếu nại</h2>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item"><strong>ID:</strong> {{ $complaint->id }}</li>
                <li class="list-group-item"><strong>Họ tên:</strong> {{ $complaint->full_name }}</li>
                <li class="list-group-item"><strong>SĐT:</strong> {{ $complaint->phone }}</li>
                <li class="list-group-item"><strong>Tòa:</strong> {{ $complaint->property->name ?? '---' }}</li>
                <li class="list-group-item"><strong>Phòng:</strong> {{ $complaint->room->room_number ?? '---' }}</li>
                <li class="list-group-item"><strong>Vấn đề:</strong> {{ $complaint->commonIssue->name ?? '---' }}</li>
                <li class="list-group-item"><strong>Mô tả:</strong> {{ $complaint->detail ?? '(Không có)' }}</li>
                <li class="list-group-item"><strong>Trạng thái:</strong> {{ ucfirst($complaint->status) }}</li>
                <li class="list-group-item"><strong>Số tiền phải chịu:</strong> {{ number_format($complaint->user_cost) }} VNĐ</li>
                @if ($complaint->staff)
                    <li class="list-group-item">
                        <strong>Nhân viên xử lý:</strong> {{ $complaint->staff->name }}
                        @if ($complaint->staff->email)
                            ({{ $complaint->staff->email }})
                        @endif
                    </li>
                @endif
                <li class="list-group-item"><strong>Ngày gửi:</strong> {{ $complaint->created_at->format('d/m/Y H:i') }}</li>
            </ul>

            {{-- Hình ảnh đính kèm --}}
         {{-- Hình ảnh trước khi xử lý --}}
<div class="mb-4">
    <h5 class="mb-3 text-primary">📷 Ảnh trước khi xử lý</h5>
    @php
        $beforePhotos = $complaint->photos->filter(function($p) use ($complaint) {
            return $p->created_at->lte($complaint->created_at->addMinutes(5)); 
        });
    @endphp

    @if ($beforePhotos->isNotEmpty())
        <div class="row g-3">
            @foreach ($beforePhotos as $photo)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <a href="{{ asset('storage/' . $photo->photo_path) }}"
                           data-lightbox="before-photos"
                           data-title="Ảnh trước xử lý #{{ $complaint->id }}">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                 class="card-img-top rounded"
                                 style="height: 180px; object-fit: cover;">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted fst-italic">Không có ảnh trước xử lý.</p>
    @endif
</div>

{{-- Hình ảnh sau khi xử lý --}}
<div class="mb-4">
    <h5 class="mb-3 text-success">🛠 Ảnh sau khi xử lý</h5>
    @php
        $afterPhotos = $complaint->photos->filter(function($p) use ($complaint) {
            return $p->created_at->gt($complaint->created_at->addMinutes(5));
        });
    @endphp

    @if ($afterPhotos->isNotEmpty())
        <div class="row g-3">
            @foreach ($afterPhotos as $photo)
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <a href="{{ asset('storage/' . $photo->photo_path) }}"
                           data-lightbox="after-photos"
                           data-title="Ảnh sau xử lý #{{ $complaint->id }}">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                 class="card-img-top rounded"
                                 style="height: 180px; object-fit: cover;">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted fst-italic">Chưa có ảnh sau xử lý.</p>
    @endif
</div>

            <div class="text-end">
                <a href="{{ route('home.complaints.index') }}" class="btn btn-outline-primary">
                    ← Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection