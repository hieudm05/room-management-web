@extends('landlord.layouts.app')
@section('title', 'Hoá đơn')
@section('content')
<form method="GET" class="mb-3 form-inline">
    <label class="mr-2">Tháng:</label>
    <input type="month" name="month" value="{{ $month }}" class="form-control mr-3">

    <label class="mr-2">Trạng thái:</label>
    <select name="status" class="form-control mr-3">
        <option value="">Tất cả</option>
        <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
    </select>

    <button type="submit" class="btn btn-primary">🔍 Lọc</button>
    <a href="{{ route('landlords.bills.export', ['month' => $month, 'status' => $status]) }}" class="btn btn-success ml-auto">⬇️ Xuất Excel</a>
</form>

@foreach ($properties as $property)
    <h5>🏢 {{ $property->name }}</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Phòng</th>
                <th>Khách thuê</th>
                <th>Tháng</th>
                <th>Tiền thuê</th>
                <th>Điện</th>
                <th>Nước</th>
                <th>Tiền Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Xem</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($property->rooms as $room)
                @foreach ($room->bills as $bill)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ optional($room->rentalAgreement->renter)->name ?? 'Chưa có' }}</td>
                        <td>{{ $bill->month }}</td>
                        <td>{{ number_format($bill->rent_price) }}</td>
                        <td>{{ number_format($bill->electric_total) }}</td>
                        <td>{{ number_format($bill->water_total) }}</td>
                        <td>{{ number_format($bill->total) }}</td>
                        <td>
                            @if ($bill->status == 'unpaid')
                                <span class="bg bg-warning">Chưa thanh toán</span>
                            @elseif ($bill->status == 'pending')
                                <span class="bg bg-info">Đang xử lý</span>
                            @else
                                <span class="bg bg-success">Đã thanh toán</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('landlords.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-info">Chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endforeach

@endsection
