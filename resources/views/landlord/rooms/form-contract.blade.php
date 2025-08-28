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

                {{-- Hiển thị lỗi chung --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <h6 class="fw-bold">Thông tin người thuê</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="ten" class="form-control @error('ten') is-invalid @enderror"
                            value="{{ old('ten') }}" required>
                        @error('ten')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">CCCD</label>
                        <input type="text" name="cccd" class="form-control @error('cccd') is-invalid @enderror"
                            value="{{ old('cccd') }}" required>
                        @error('cccd')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">SĐT</label>
<input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" required>
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Các field khác tương tự --}}
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số lượng người ở</label>
                        <input type="number" name="so_nguoi_o" class="form-control @error('so_nguoi_o') is-invalid @enderror"
                            value="{{ old('so_nguoi_o', $room->renter_people ?? '') }}" required>
                        @error('so_nguoi_o')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Số người tối đa</label>
                        <input type="number" name="so_nguoi_toi_da" class="form-control @error('so_nguoi_toi_da') is-invalid @enderror"
                            value="{{ old('so_nguoi_toi_da', $room->occupants ?? '') }}" required>
                        @error('so_nguoi_toi_da')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" class="form-control @error('ngay_bat_dau') is-invalid @enderror"
                            value="{{ old('ngay_bat_dau') }}" required>
                        @error('ngay_bat_dau')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" class="form-control @error('ngay_ket_thuc') is-invalid @enderror"
                            value="{{ old('ngay_ket_thuc') }}" required>
                        @error('ngay_ket_thuc')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
</div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diện tích (m²)</label>
                        <input type="text" name="dien_tich" class="form-control @error('dien_tich') is-invalid @enderror"
                            value="{{ old('dien_tich', $room->area) }}" required>
                        @error('dien_tich')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Giá thuê (VNĐ)</label>
                        <input type="text" name="gia_thue" class="form-control @error('gia_thue') is-invalid @enderror"
                            value="{{ old('gia_thue', $room->rental_price) }}" required>
                        @error('gia_thue')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tiền cọc (VNĐ)</label>
                        <input type="text" name="gia_coc" class="form-control @error('gia_coc') is-invalid @enderror"
                            value="{{ old('gia_coc', $deposit_price ?? $room->deposit_price ?? 0) }}" required>
                        @error('gia_coc')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-success">📄 Tạo PDF hợp đồng</button>
            </form>
            @endif
            <div class="mt-3">
                <a href="{{ route('landlords.rooms.show', $room->room_id) }}" class="btn btn-secondary ms-2">
                    ⬅️ Quay lại phòng
                </a>
            </div>
        </div>
    </div>
</div>
@endsection