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
                    <a href="{{ route('landlords.staff.contract.', $room) }}" class="btn btn-outline-primary">📄 Hợp
                        đồng</a>
                    {{-- <a href="{{ route('landlords.staff.electric_water.', $room) }}"
                        class="btn btn-outline-warning">⚡ Điện/Nước</a>
                    <a href="{{ route('landlords.staff.documents.', $room) }}" class="btn btn-outline-secondary">📑
                        Giấy tờ</a>
                    <a href="{{ route('landlords.staff.payment.', $room) }}" class="btn btn-outline-danger">💰 Thu
                        tiền</a> --}}
                </div>


                <div>
                    {{-- Nội dung chi tiết từng phần sẽ render ở đây --}}
                    @yield('room_detail_content')
                </div>
            </div>
        </div>
    </div>


@endsection
