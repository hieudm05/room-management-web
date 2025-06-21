@extends('home.layouts.app')

@section('title', $room->title)


@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container mt-4">
    <div class="row">
        <!-- Cột trái -->
        <div class="col-md-8">
            <!-- Ảnh đại diện -->
            <div class="mb-3">
                {{-- Ảnh đại diện lớn --}}
                <img src="{{ $room->photos[0]->image_url ?? asset('storage/default.jpg') }}" class="img-fluid w-100 rounded" alt="Ảnh phòng">


                <div class="d-flex mt-2 overflow-auto">
                    @forelse($room->photos as $photo)
                    <img src="{{ $photo->image_url }}" width="50" class="me-2 rounded" alt="Ảnh phòng">
                    @empty
                    <span class="text-muted">Chưa có ảnh</span>
                    @endforelse
                </div>

            </div>


            <!-- Tiêu đề + giá -->
            <h4 class="fw-bold text-danger">{{ $room->title }}</h4>
            <p class="mb-1">Giá thuê: <strong class="text-success">{{ number_format($room->price) }} VNĐ/tháng</strong></p>
            <p class="mb-1">Diện tích: {{ $room->area }} m²</p>
            <p class="mb-1">Địa chỉ: {{ $room->address }}</p>
            <p class="mb-1">Ngày đăng: {{ $room->created_at->format('d/m/Y') }}</p>

            <!-- Mô tả -->
            <div class="mt-4">
                <h5 class="fw-semibold">Thông tin mô tả</h5>
                <p>{!! nl2br(e($room->description)) !!}</p>
            </div>

            <!-- Tiện ích -->
            @if($room->facilities->count())
            <div class="mt-4">
                <h5 class="fw-semibold">Tiện ích</h5>
                <ul>
                    @foreach($room->facilities as $facility)
                    <li>{{ $facility->name }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Dịch vụ đi kèm -->
            @if($room->services->count())
            <div class="mt-4">
                <h5 class="fw-semibold">Dịch vụ</h5>
                <ul>
                    @foreach($room->services as $service)
                    <li>{{ $service->name }}: {{ number_format($service->price) }} VNĐ</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Bản đồ -->
            <div class="mt-4">
                <h5 class="fw-semibold">Vị trí trên bản đồ</h5>
                <iframe
                    src="https://maps.google.com/maps?q={{ urlencode($room->address) }}&output=embed"
                    width="100%"
                    height="300"
                    frameborder="0"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>

        </div>

        <!-- Cột phải -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{ asset('images/avatar.png') }}" class="rounded-circle mb-2" width="80" height="80" alt="Người đăng">
                    <h6 class="fw-bold">{{ $room->user->name ?? 'Chủ phòng' }}</h6>
                    <p class="text-muted">SĐT: {{ $room->user->phone ?? 'Chưa cập nhật' }}</p>
                    <a href="" class="btn btn-success btn-sm w-100 mb-2">
                        Gọi ngay
                    </a>
                    <a href="" class="btn btn-primary btn-sm w-100" target="_blank">
                        Nhắn Zalo
                    </a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#thuePhongModal">
                        Thuê Phòng
                    </a>

                </div>
            </div>

            <!-- Tin nổi bật (tuỳ bạn có dữ liệu không) -->
            {{-- @include('components.hot-posts') --}}
        </div>
    </div>
</div>
<!-- Modal Thuê Phòng -->
<div class="modal fade" id="thuePhongModal" tabindex="-1" aria-labelledby="thuePhongModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="thuePhongModalLabel">Hợp đồng thuê phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body">
                @if ($room->contract_pdf_file || $room->contract_word_file)
                <div class="mb-3 text-center">
                    @if ($room->contract_pdf_file)
                    <a href="{{ route('landlords.rooms.contract.pdf', $room) }}" class="btn btn-outline-success mb-2" target="_blank">👁️ Xem hợp đồng mẫu PDF</a>
                    <a href="{{ route('landlords.rooms.contract.download', $room) }}" class="btn btn-outline-primary mb-2">📄 Tải hợp đồng PDF</a>
                    @endif

                    @if ($room->contract_word_file)
                    <a href="{{ route('landlords.rooms.contract.word', $room) }}" class="btn btn-outline-warning mb-2">📝 Tải hợp đồng Word (.docx)</a>
                    @endif
                </div>
                @else
                <p class="text-muted text-center">Chưa có hợp đồng mẫu cho phòng này.</p>
                @endif

                @auth
                {{-- Bước 1: Upload file để xem trước --}}
                <form action="{{ route('contracts.preview', $room) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="contract_pdf_file" class="form-label">Tải hợp đồng PDF</label>
                        <input type="file" name="contract_pdf_file" accept=".pdf" class="form-control mb-2">

                        <label for="contract_word_file" class="form-label">Tải hợp đồng Word</label>
                        <input type="file" name="contract_word_file" accept=".doc,.docx" class="form-control mb-2">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">📤 Xem trước hợp đồng Word</button>
                    </div>
                </form>


                {{-- Bước 2: Nếu đã xem trước thì hiện nội dung và nút xác nhận --}}
                @if(session('word_content') && session('temp_path'))
                <div class="alert alert-info mt-4 text-start" style="white-space: pre-wrap; max-height: 400px; overflow-y: auto;">
                    <h6 class="fw-bold">Nội dung hợp đồng:</h6>
                    {!! session('word_content') !!}
                </div>

                <form action="{{ route('contracts.confirm', $room) }}" method="POST">
                    @csrf
                    <input type="hidden" name="temp_path" value="{{ session('temp_path') }}">
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">✅ Xác nhận lưu hợp đồng</button>
                    </div>
                </form>
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>

@endsection
