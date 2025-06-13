@extends('landlord.layouts.app')

@section('title', 'Chi tiết phòng')

@section('content')
    <style>
        .badge.bg-purple {
            background-color: #6f42c1 !important;
            color: #fff !important;
        }
    </style>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">🔍 Chi tiết phòng</h5>
            </div>
            <div class="card-body">

                {{-- Khu trọ --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Khu trọ</label>
                    <input type="text" class="form-control" value="{{ $room->property->name }}" disabled>
                </div>

                {{-- Số phòng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số phòng</label>
                    <input type="text" class="form-control" value="{{ $room->room_number }}" disabled>
                </div>

                {{-- Diện tích --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Diện tích (m²)</label>
                    <input type="text" class="form-control" value="{{ $room->area }}" disabled>
                </div>

                {{-- Giá thuê --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Giá thuê (VNĐ)</label>
                    <input type="text" class="form-control" value="{{ number_format($room->rental_price) }}" disabled>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <input type="text" class="form-control" value="{{ $room->status }}" disabled>
                </div>

                {{-- Tiện nghi --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiện nghi</label>
                    <ul class="list-group">
                        @forelse ($room->facilities as $facility)
                            <li class="list-group-item">{{ $facility->name }}</li>
                        @empty
                            <li class="list-group-item text-muted">Không có tiện nghi</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Số người ở --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số người ở</label>
                    <input type="text" class="form-control" value="{{ $room->occupants }}" disabled>
                </div>

                {{-- Dịch vụ --}}
                @if ($room->services->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <ul class="list-group">
                            @foreach ($room->services as $service)
                                @php
                                    $unit = $service->pivot->unit;
                                    $isFree = $service->pivot->is_free;
                                    $price = $service->pivot->price ?? 0;
                                    $occupants = $room->occupants ?? 0;
                                    $rightText = '';
                                    $description = '';
                                    $badgeClass = 'purple';

                                    if ($isFree) {
                                        $rightText = 'Miễn phí';
                                        $description = '<small class="text-muted">Không tính phí</small>';
                                    } elseif ($service->service_id == 2) {
                                        // Nước
                                        if ($unit === 'per_person') {
                                            $total = $occupants * $price;
                                            $rightText = number_format($total) . ' VNĐ';
                                            $description =
                                                'Tính theo đầu người<br><small class="text-muted">Tổng: ' .
                                                number_format($total) .
                                                ' VNĐ (' .
                                                $occupants .
                                                ' người x ' .
                                                number_format($price) .
                                                ' VNĐ)</small>';
                                        } elseif ($unit === 'per_m3') {
                                            $rightText = number_format($price) . ' VNĐ / m³';
                                            $description = '<small class="text-muted">Tính theo khối</small>';
                                        } else {
                                            $rightText = number_format($price) . ' VNĐ';
                                            $description = '<small class="text-muted">Không rõ đơn vị tính</small>';
                                        }
                                    } elseif ($service->service_id == 3) {
                                        // Wifi
                                        if ($unit === 'per_person') {
                                            $total = $occupants * $price;
                                            $rightText = number_format($total) . ' VNĐ';
                                            $description =
                                                'Tính theo đầu người<br><small class="text-muted">Tổng: ' .
                                                number_format($total) .
                                                ' VNĐ (' .
                                                $occupants .
                                                ' người x ' .
                                                number_format($price) .
                                                ' VNĐ)</small>';
                                        } elseif ($unit === 'per_room') {
                                            $rightText = number_format($price) . ' VNĐ';
                                            $description =
                                                '<small class="text-muted">Tính theo phòng (giá cố định)</small>';
                                        } else {
                                            $rightText = number_format($price) . ' VNĐ';
                                            $description = '<small class="text-muted">Không rõ đơn vị tính</small>';
                                        }
                                    } else {
                                        // Dịch vụ khác
                                        $rightText = number_format($price) . ' VNĐ';
                                        $description = '<small class="text-muted">Dịch vụ tính phí cố định</small>';
                                    }
                                @endphp

                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $service->name }}</div>
                                        <div>{!! $description !!}</div>
                                    </div>
                                    <span class="badge bg-{{ $badgeClass }} fs-6">
                                        {{ $rightText }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <p class="text-muted">Không có dịch vụ nào.</p>
                    </div>
                @endif

                {{-- Ảnh phòng --}}
                @if ($room->photos->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh phòng</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($room->photos as $photo)
                                <div class="border p-1">
                                    <img src="{{ $photo->image_url }}" width="150" class="rounded shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($room->contract_pdf_file || $room->contract_word_file)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hợp đồng mẫu</label><br>

                        @if ($room->contract_pdf_file)
                            <a href="{{ route('landlords.rooms.contract.pdf', $room) }}" class="btn btn-outline-success"
                                target="_blank">
                                👁️ Xem hợp đồng mẫu PDF
                            </a>
                            <a href="{{ route('landlords.rooms.contract.download', $room) }}"
                                class="btn btn-outline-primary ms-2">
                                📄 Tải hợp đồng PDF
                            </a>
                        @endif

                        @if ($room->contract_word_file)
                            <a href="{{ route('landlords.rooms.contract.word', $room) }}"
                                class="btn btn-outline-warning ms-2">
                                📝 Tải hợp đồng Word (.docx)
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Nút quay lại --}}
                <div class="text-start mt-4">
                    <a href="{{ route('landlords.rooms.index') }}" class="btn btn-secondary">🔙 Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
@endsection
