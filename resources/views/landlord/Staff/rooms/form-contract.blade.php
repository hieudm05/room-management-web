@extends('landlord.layouts.app')

@section('title', '📑 Quản lý hợp đồng')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            📑 Quản lý hợp đồng phòng {{ $room->room_number }}
        </div>
        <div class="card-body">

            {{-- Thông tin phòng --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Phòng</label>
                <input type="text" class="form-control"
                    value="{{ $room->room_number }} - {{ $room->property->name }}" disabled>
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
            @else
            {{-- Nếu chưa có hợp đồng -> hiện form --}}
            <form action="{{ route('landlords.staff.contract.generate', $room->room_id) }}" method="POST">
                @csrf

                <h6 class="fw-bold">Thông tin người thuê</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="ten" class="form-control" value="{{ old('ten') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CCCD</label>
                        <input type="text" name="cccd" class="form-control" value="{{ old('cccd') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SĐT</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                </div>

                <h6 class="fw-bold">Thông tin hợp đồng</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số lượng người ở</label>
                        <input type="number" name="so_nguoi_o" class="form-control" value="{{ old('so_nguoi_o', $room->renter_people ?? '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số người tối đa</label>
                        <input type="number" name="so_nguoi_toi_da" class="form-control" value="{{ old('so_nguoi_toi_da', $room->occupants ?? '') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" class="form-control" value="{{ old('ngay_bat_dau') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" class="form-control" value="{{ old('ngay_ket_thuc') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diện tích (m²)</label>
                        <input type="text" name="dien_tich" class="form-control" value="{{ old('dien_tich', $room->area) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Giá thuê (VNĐ)</label>
                        <input type="text" name="gia_thue" class="form-control" value="{{ old('gia_thue', $room->rental_price) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tiền cọc (VNĐ)</label>
                        <input type="text" name="gia_coc" class="form-control"
                            value="{{ old('gia_coc', $deposit_price ?? $room->deposit_price ?? 0) }}" required>

                    </div>
                </div>

                <button type="submit" class="btn btn-success">📄 Tạo PDF hợp đồng</button>
            </form>
            @endif

        </div>
    </div>
</div>
@endsection
