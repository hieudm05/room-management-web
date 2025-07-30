@extends('landlord.layouts.app')
@section('title', 'Chi tiết hoá đơn')

@section('content')
<div class="container">
    <h4>📃 Chi tiết hóa đơn: {{ $bill->month }}</h4>
    <ul class="list-group mb-3">
        <li class="list-group-item">🏢 Tòa: {{ $bill->room->property->name }}</li>
        <li class="list-group-item">🔢 Phòng: {{ $bill->room->room_number }}</li>
        <li class="list-group-item">👤 Khách thuê: {{ optional($bill->room->rentalAgreement->renter)->name ?? 'Chưa có' }}</li>
        <li class="list-group-item">💰 Tiền thuê: {{ number_format($bill->rent_price) }} VND</li>
        <li class="list-group-item">🔌 Tiền điện: {{ number_format($bill->electric_total) }} VND</li>
        <li class="list-group-item">🚿 Tiền nước: {{ number_format($bill->water_total) }} VND</li>

        <li class="list-group-item">🧾 Phụ phí:
            <ul>
                @forelse ($bill->additionalFees as $fee)
                    <li>{{ $fee->name }} - {{ number_format($fee->total) }} VND</li>
                @empty
                    <li>Không có</li>
                @endforelse
            </ul>
        </li>

        <li class="list-group-item">💬 Khiếu nại:
            @php
                use Carbon\Carbon;
                $target = Carbon::parse($bill->month);
                $complaints = $bill->room->complaints()
                    ->where('status', 'resolved')
                    ->whereMonth('updated_at', $target->month)
                    ->whereYear('updated_at', $target->year)
                    ->get();
            @endphp
            <ul>
                @forelse ($complaints as $complaint)
                    <li>{{ $complaint->content }} (User chịu: {{ number_format($complaint->user_cost) }} VND)</li>
                @empty
                    <li>Không có</li>
                @endforelse
            </ul>
        </li>

        <li class="list-group-item">
            ✅ Trạng thái:
            @if ($bill->status == 'unpaid')
                <span class="badge badge-warning">Chưa thanh toán</span>
            @elseif ($bill->status == 'pending')
                <span class="badge badge-info">Đang xử lý</span>
            @else
                <span class="badge badge-success">Đã thanh toán</span>
            @endif
        </li>

        <li class="list-group-item">🔢 Tổng cộng: <strong>{{ number_format($bill->total) }} VND</strong></li>
    </ul>
    <a href="{{ route('landlords.bills.index') }}" class="btn btn-secondary">⬅ Quay lại</a>
</div>
@endsection
