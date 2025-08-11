@extends('landlord.layouts.app')
@section('title', 'Chi tiết hoá đơn')

@section('content')
<style>
    .list-group-item {
        padding: 15px;
        border-left: 4px solid #007bff;
        margin-bottom: 10px;
    }
    .badge {
        font-size: 0.9em;
        padding: 8px 12px;
    }
    .img-fluid {
        max-width: 150px;
        height: auto;
        border: 1px solid #ddd;
        padding: 5px;
    }
    .total-amount {
        font-size: 1.2em;
        color: #dc3545;
    }
</style>

<div class="container">
    <h4>📃 Chi tiết hóa đơn: {{ $bill->month }}</h4>
    <div class="card shadow-sm">
        <div class="card-body">
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <strong>🏢 Tòa:</strong> {{ $property->name }}
                </li>
                <li class="list-group-item">
                    <strong>🔢 Phòng:</strong> {{ $room->room_number }}
                </li>
                <li class="list-group-item">
                    <strong>👤 Khách thuê:</strong> {{ $tenant->name ?? 'Chưa có' }}
                </li>
                <li class="list-group-item">
                    <strong>💰 Tiền thuê:</strong> {{ number_format($bill->rent_price, 0, ',', '.') }} VND
                </li>
                <li class="list-group-item">
                    <strong>🔌 Tiền điện:</strong> {{ number_format($bill->electric_total, 0, ',', '.') }} VND 
                    ({{ number_format($electricPrice, 0, ',', '.') }}đ/kWh)
                </li>
                <li class="list-group-item">
                    <strong>🚿 Tiền nước:</strong> {{ number_format($bill->water_total, 0, ',', '.') }} VND 
                    ({{ number_format($waterPrice, 0, ',', '.') }}đ - {{ $waterUnit == 'per_m3' ? 'theo m³' : 'theo người' }})
                </li>
                <li class="list-group-item">
                    <strong>🧾 Phí phát sinh:</strong>
                    <ul>
                        @forelse ($additionalFees as $fee)
                            <li>{{ $fee['name'] ?? 'Chi phí không tên' }} - {{ number_format($fee['total'] ?? 0, 0, ',', '.') }} VND ({{ $fee['qty'] ?? 1 }} x {{ number_format($fee['price'] ?? 0, 0, ',', '.') }} VND)</li>
                        @empty
                            <li>Không có</li>
                        @endforelse
                    </ul>
                    <strong>Tổng phí phát sinh:</strong> {{ number_format($additionalFeesTotal, 0, ',', '.') }} VND
                </li>
                <li class="list-group-item">
                    <strong>🧰 Dịch vụ khác:</strong>
                    <ul>
                        @forelse ($services as $sv)
                            <li>{{ $sv['name'] ?? 'Dịch vụ' }} - {{ number_format($sv['total'] ?? 0, 0, ',', '.') }} VND</li>
                        @empty
                            <li>Không có</li>
                        @endforelse
                    </ul>
                    <strong>Tổng dịch vụ:</strong> {{ number_format($serviceTotal, 0, ',', '.') }} VND
                </li>
                <li class="list-group-item">
                    <strong>💬 Khiếu nại:</strong>
                    <ul>
                        @forelse ($complaints as $complaint)
                            <li>
                                {{ $complaint->content }} <br>
                                👤 Người thuê chịu: {{ number_format($complaint->user_cost ?? 0, 0, ',', '.') }} VND, 
                                🧑‍💼 Chủ trọ chịu: {{ number_format($complaint->landlord_cost ?? 0, 0, ',', '.') }} VND
                            </li>
                        @empty
                            <li>Không có</li>
                        @endforelse
                    </ul>
                    <strong>Tổng chi phí người thuê chịu (khiếu nại):</strong> {{ number_format($totalAfterComplaint, 0, ',', '.') }} VND
                </li>
                <li class="list-group-item">
                    <strong>📸 Ảnh điện:</strong>
                    <div class="row">
                        @forelse ($electricPhotos as $photo)
                            <div class="col-md-3 mb-2">
                                <img src="{{ asset('storage/' . $photo) }}" class="img-fluid rounded" alt="Ảnh điện">
                            </div>
                        @empty
                            <div class="col-md-12">Không có ảnh</div>
                        @endforelse
                    </div>
                </li>
                <li class="list-group-item">
                    <strong>📸 Ảnh nước:</strong>
                    <div class="row">
                        @forelse ($waterPhotos as $photo)
                            <div class="col-md-3 mb-2">
                                <img src="{{ asset('storage/' . $photo) }}" class="img-fluid rounded" alt="Ảnh nước">
                            </div>
                        @empty
                            <div class="col-md-12">Không có ảnh</div>
                        @endforelse
                    </div>
                </li>
                <li class="list-group-item">
                    <strong>✅ Trạng thái:</strong>
                    @if ($bill->status == 'unpaid')
                        <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                    @elseif ($bill->status == 'pending')
                        <span class="badge bg-info">Đang xử lý</span>
                    @else
                        <span class="badge bg-success">Đã thanh toán</span>
                    @endif
                </li>
                <li class="list-group-item total-amount">
                    <strong>💵 Tổng cộng:</strong> {{ number_format($total, 0, ',', '.') }} VND
                </li>
            </ul>
        </div>
    </div>

    <a href="{{ route('landlords.bills.index') }}" class="btn btn-secondary mt-3">⬅ Quay lại</a>
</div>
@endsection