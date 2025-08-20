@extends('landlord.layouts.app')

@section('title', 'Danh sách phòng')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bage-primary text-white">
            <h4 class="mb-0">Phòng {{ $room->room_number }} - {{ $room->property?->name ?? 'N/A' }} </h4>
        </div>
        <div class="card-body">
            <p><strong>Diện tích:</strong> {{ $room->area }} m²</p>
            <p><strong>Giá thuê:</strong> {{ number_format($room->rental_price) }} VND</p>
            <p><strong>Trạng thái:</strong> <span class="badge bg-info">{{ $room->status }}</span></p>

            <div class="d-flex flex-wrap gap-2 my-3">
                <a href="{{ route('landlords.staff.contract.index', $room) }}" class="btn btn-outline-primary">📄 Hợp
                    đồng</a>
                <a href="{{ route('landlords.staff.deposit.form', $room) }}" class="btn btn-outline-success">
                    💰 Đặt cọc
                </a>
            </div>
            <div>
                {{-- Nội dung chi tiết từng phần sẽ render ở đây --}}
                @yield('room_detail_content')
            </div>
        </div>
    </div>
</div>


@endsection
