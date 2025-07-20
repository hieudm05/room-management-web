@extends('landlord.layouts.app')
@section('title', 'Hoá đơn')

@section('content')
<div class="container">
    <h3 class="mb-4">📄 Danh sách Hóa Đơn</h3>

    <form method="GET" class="form-inline mb-4 bg-light p-3 rounded shadow-sm">
        <div class="form-group mr-3">
            <label class="mr-2 font-weight-bold">Tháng:</label>
            <input type="month" name="month" value="{{ $month }}" class="form-control">
        </div>

        <div class="form-group mr-3">
            <label class="mr-2 font-weight-bold">Trạng thái:</label>
            <select name="status" class="form-control">
                <option value="">Tất cả</option>
                <option value="unpaid" {{ $status == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mr-2">🔍 Lọc</button>
        <a href="{{ route('landlords.bills.export', ['month' => $month, 'status' => $status]) }}" class="btn btn-success">⬇️ Xuất Excel</a>
    </form>

    @foreach ($properties as $property)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white font-weight-bold">
                🏢 {{ $property->name }}
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Phòng</th>
                            <th>Khách thuê</th>
                            <th>Tháng</th>
                            <th>Tiền thuê (VNĐ)</th>
                            <th>Điện (VNĐ)</th>
                            <th>Nước (VNĐ)</th>
                            <th>Tổng cộng (VNĐ)</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($property->rooms as $room)
                            @foreach ($room->bills as $bill)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ optional($room->rentalAgreement->renter)->name ?? 'Chưa có' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bill->month)->format('m/Y') }}</td>
                                    <td>{{ number_format($bill->rent_price) }}</td>
                                    <td>{{ number_format($bill->electric_total) }}</td>
                                    <td>{{ number_format($bill->water_total) }}</td>
                                    <td class="font-weight-bold text-danger">{{ number_format($bill->total) }}</td>
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
                                        <a href="{{ route('landlords.bills.show', $bill->id) }}" class="btn btn-sm btn-outline-info">📄 Chi tiết</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
