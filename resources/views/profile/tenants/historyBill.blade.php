@extends('home.layouts.app')

@section('content')
<div class="container">
    <h2>Hóa đơn thuê phòng</h2>

    <form method="GET" action="{{ route('home.profile.tenants.history') }}" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="month" name="month" class="form-control" value="{{ $monthFilter }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </div>
    </form>

    @if($bills->isEmpty())
        <div class="alert alert-info">Không có hóa đơn nào.</div>
    @else
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Phòng</th>
                    <th>Nhà trọ</th>
                    <th>Tháng</th>
                    <th>Tổng tiền</th>
                    <th>Bắt đầu thuê</th>
                    <th>Ngày rời</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $index => $bill)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $bill->room_name }}</td>
                        <td>{{ $bill->property_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->month)->format('m/Y') }}</td>
                        <td>{{ number_format($bill->total, 0, ',', '.') }} VND</td>
                        <td>{{ \Carbon\Carbon::parse($bill->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->leave_date)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
