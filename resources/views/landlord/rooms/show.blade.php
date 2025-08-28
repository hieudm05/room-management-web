@extends('landlord.layouts.app')

@section('title', 'Chi tiết phòng')

@section('content')
    <style>
        .badge.bg-purple {
            background-color: #6f42c1 !important;
            color: #fff !important;
        }

        .room-photo {
            width: 150px;
            height: auto;
            object-fit: cover;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>

    <div class="container mt-4">
        <div class="card shadow-sm border-0">
            <div class="bg-primary text-white fw-bold px-3 py-2 rounded-top fs-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                🏠 Thông tin chi tiết phòng <span class="text-warning">#{{ $room->room_number }}</span>
            </div>

            <div class="card-body">
                {{-- Thông tin cơ bản --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="section-title">Khu trọ</label>
                        <input type="text" class="form-control" value="{{ $room->property->name }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="section-title">Số phòng</label>
                        <input type="text" class="form-control" value="{{ $room->room_number }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="section-title">Diện tích (m²)</label>
                        <input type="text" class="form-control" value="{{ $room->area }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="section-title">Giá thuê</label>
                        <input type="text" class="form-control"
                            value="{{ number_format($room->rental_price) }} VNĐ (Đã sửa {{ $room->price_edit_count ?? 0 }} lần)"
                            disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="section-title">Tiền cọc</label>
                        <input type="text" class="form-control"
                            value="{{ number_format($room->deposit_price) }} VNĐ (Đã sửa {{ $room->deposit_edit_count ?? 0 }} lần)"
                            disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="section-title">Trạng thái</label>
                        <input type="text" class="form-control" value="{{ $room->status }}" disabled>
                    </div>
                </div>

                {{-- Tiện nghi --}}
                <div class="mb-4">
                    <label class="section-title">Tiện nghi</label>
                    <ul class="list-group">
                        @forelse ($room->facilities as $facility)
                            <li class="list-group-item">{{ $facility->name }}</li>
                        @empty
                            <li class="list-group-item text-muted">Không có tiện nghi</li>
                        @endforelse
                    </ul>
                </div>

                <div class="mb-4">
    <label class="section-title">Người thuê</label>

    @if ($room->currentAgreement)
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" class="form-control"
                    value="{{ $room->tenant_info['full_name'] }}" disabled>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control"
                    value="SĐT: {{ $room->tenant_info['phone'] }}" disabled>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control"
                    value="Email: {{ $room->tenant_info['email'] }}" disabled>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control"
                    value="CCCD/CMND: {{ $room->tenant_info['cccd'] }}" disabled>
            </div>
        </div>
    @else
        <p class="text-muted">Chưa có người thuê đại diện trong hợp đồng.</p>
    @endif
</div>


            {{-- <div class="mb-4">
                    <label class="section-title">Người thuê</label>

                    @if ($agreement = $room->currentAgreement)
                        @php
                            // Ưu tiên lấy từ user_infos trước
                            $userInfo = $agreement->userInfos()->latest()->first();
                        @endphp

                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    value="{{ $userInfo?->full_name ?? ($agreement->full_name ?? 'Chưa có tên') }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    value="SĐT: {{ $userInfo?->phone ?? ($agreement->phone ?? 'Chưa có số điện thoại') }}"
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    value="Email: {{ $userInfo?->email ?? ($agreement->email ?? 'Chưa có email') }}"
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    value="CCCD/CMND: {{ $userInfo?->cccd ?? ($agreement->cccd ?? 'Chưa có CCCD') }}"
                                    disabled>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Chưa có người thuê đại diện trong hợp đồng.</p>
                    @endif
                </div> --}}
{{-- <div class="mb-4">
                <label class="section-title">Người thuê</label>
                @if ($room->userInfos->isNotEmpty())
                <div class="row g-2">
                    @foreach($room->userInfos as $info)
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                            value="{{ $info->full_name ?? 'Chưa có tên' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                            value="SĐT: {{ $info->phone ?? 'Chưa có số điện thoại' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                            value="Email: {{ $info->email ?? 'Chưa có email' }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                            value="CCCD/CMND: {{ $info->cccd ?? 'Chưa có CCCD' }}" disabled>
                    </div>
                    <hr class="my-2">
                    @endforeach
                </div>
                @else
                <p class="text-muted">Chưa có người thuê đại diện trong hợp đồng.</p>
                @endif
            </div> --}}



                {{-- Dịch vụ --}}
                <div class="mb-4">
                    <label class="section-title">Dịch vụ</label>
                    @if ($room->services->count())
                        <ul class="list-group">
                            @foreach ($room->services as $service)
                                @php
                                    $unit = $service->pivot->unit;
                                    $isFree = $service->pivot->is_free;
                                    $price = $service->pivot->price ?? 0;
                                    $people_renter = $room->people_renter ?? 0;
                                    $description = '';
                                    $rightText = $isFree ? 'Miễn phí' : number_format($price) . ' VNĐ';

                                    if (!$isFree) {
                                        if ($unit === 'per_person') {
                                            $total = $people_renter * $price;
                                            $rightText = number_format($total) . ' VNĐ';
                                            $description =
                                                "Tính theo đầu người ({$people_renter} người x " .
                                                number_format($price) .
                                                ' VNĐ)';
                                        } elseif ($unit === 'per_m3') {
                                            $description = 'Tính theo khối (m³)';
                                            $rightText = number_format($price) . ' VNĐ / m³';
                                        } elseif ($unit === 'per_room') {
                                            $description = 'Tính theo phòng (giá cố định)';
                                        }
                                    } else {
                                        $description = 'Không tính phí';
                                    }
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">{{ $service->name }}</div>
                                        <small class="text-muted">{{ $description }}</small>
                                    </div>
                                    <span class="badge bg-purple fs-6">{{ $rightText }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Không có dịch vụ nào.</p>
                    @endif
                </div>

                {{-- Ảnh phòng --}}
                @if ($room->photos->count())
                    <div class="mb-4">
                        <label class="section-title">Ảnh phòng</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($room->photos as $photo)
                                <div class="border p-1 rounded">
                                    <img src="{{ $photo->image_url }}" class="room-photo rounded shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Không có ảnh phòng.</p>
                @endif
            </div>

            {{-- Hợp đồng mẫu --}}
            @if ($room->contract_pdf_file || $room->contract_word_file)
                <div class="mb-4">
                    <label class="section-title">Hợp đồng mẫu</label><br>
                    @if ($room->contract_pdf_file)
                        <a href="{{ route('landlords.rooms.contract.pdf', $room) }}" class="btn btn-outline-success  me-2"
                            target="_blank">
                            👁️ Xem PDF
                        </a>
                        <a href="{{ route('landlords.rooms.contract.download', $room) }}"
                            class="btn btn-outline-primary  me-2">
                            📄 Tải PDF
                        </a>
                        <a href="{{ route('landlords.rooms.contract.contractIndex', $room) }}"
                            class="btn btn-outline-primary ">
                            📄 Hợp đồng
                        </a>
                        <a href="{{ route('landlords.rooms.contracts.create', $room) }}" class="btn btn-outline-primary ">
                            📄 Điền form thông tin
                        </a>
                    @endif
                    @if ($room->contract_word_file)
                        <a href="{{ route('landlords.rooms.contract.word', $room) }}" class="btn btn-outline-warning ">
                            📝 Tải Word (.docx)
                        </a>
                    @endif
                    <a href="{{ route('landlords.rooms.deposit.form', $room) }}" class="btn btn-outline-info ">
                        💰 Đặt cọc
                    </a>
                </div>
            @endif

            {{-- Nút quay lại --}}
            <div class="text-start mt-4">
                <a href="{{ route('landlords.rooms.index') }}" class="btn btn-secondary">
                    🔙 Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
    </div>
@endsection
