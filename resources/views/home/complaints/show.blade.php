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
            <div class="mb-4">
                <h5 class="mb-3">Hình ảnh đính kèm:</h5>
                @if ($complaint->photos->isNotEmpty())
                    <div class="row">
                        @foreach ($complaint->photos as $photo)
                            <div class="col-6 col-sm-4 col-md-3 mb-3 d-flex justify-content-center">
                                <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                         alt="Ảnh khiếu nại"
                                         class="img-thumbnail"
                                         style="max-width: 200px; height: auto;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic">Không có ảnh đính kèm.</p>
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