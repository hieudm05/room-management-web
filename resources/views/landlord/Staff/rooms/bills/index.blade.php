@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
    <div class="container">
        {{-- Bộ lọc tháng --}}
        <div class="card mb-4">
            <div class="card-body">
                <form id="filter-form" action="{{ route('landlords.staff.payment.', $room->room_id) }}" method="GET"
                    class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label for="month" class="col-form-label">Chọn tháng:</label>
                    </div>
                    <div class="col-auto">
                        <input type="month" id="month" name="month" class="form-control"
                            value="{{ request('month') ?? date('Y-m') }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Nội dung hóa đơn --}}
        @if (isset($noBill) && $noBill)
            <div class="alert alert-warning text-center">
                Không có hóa đơn cho tháng này.
            </div>
        @else
            <div class="card shadow-sm" id="bill-content">
                <div class="card-header bage-success text-white">
                    <h4 class="mb-0">Hoá đơn tiền phòng {{ $data['room_name'] }} (Tháng {{ $data['month'] }})</h4>
                </div>
                <div class="card-body">

                    {{-- Thông tin phòng và khách --}}
                    <h5 class="mb-3">Thông tin phòng & khách thuê</h5>
                    <table class="table table-bordered mb-4">
                        <tbody>
                            <tr>
                                <th>Phòng</th>
                                <td>{{ $data['room_name'] }}</td>
                            </tr>
                            <tr>
                                <th>Khách thuê</th>
                                <td>{{ $data['tenant_name'] }}</td>
                            </tr>
                            <tr>
                                <th>Diện tích</th>
                                <td>{{ $data['area'] }} m²</td>
                            </tr>
                            <tr>
                                <th>Giá thuê</th>
                                <td>{{ number_format($data['rent_price']) }} VND</td>
                            </tr>
                            <tr>
                                <th>Tháng thanh toán</th>
                                <td>{{ $data['month'] }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Các khoản phí --}}
                    <h5 class="mb-3">Chi tiết chi phí</h5>
                    <table class="table table-hover mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>Khoản</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tiền thuê phòng</td>
                                <td>{{ number_format($data['rent_price']) }} VND</td>
                                <td>1</td>
                                <td>{{ number_format($data['rent_price']) }} VND</td>
                            </tr>
                            <tr>
                                <td>
                                    Điện
                                    <br>
                                    <small>
                                        Chỉ số đầu: {{ $data['electric_start'] }}<br>
                                        Chỉ số cuối: {{ $data['electric_end'] }}
                                    </small>
                                </td>
                                <td>{{ number_format($data['eletric_price']) }} VND/kWh</td>
                                <td>{{ $data['electric_kwh'] }} kWh</td>
                                <td>{{ number_format($data['electric_total']) }} VND</td>
                            </tr>
                            <tr>
                                <td>
                                    Nước
                                    <br>
                                    <small>
                                        Đơn vị:
                                        @if ($data['water_unit'] === 'per_person')
                                            Theo đầu người
                                        @elseif($data['water_unit'] === 'per_m3')
                                            Theo m³
                                        @else
                                            {{ $data['water_unit'] }}
                                        @endif
                                        <br>
                                        Số người dùng: {{ $data['water_occupants'] }}
                                    </small>
                                </td>
                                <td>{{ number_format($data['water_price']) }} VND/m³</td>
                                <td>
                                    @if ($data['water_unit'] === 'per_person')
                                        {{ $data['water_occupants'] }} người
                                    @else
                                        {{ $data['water_m3'] }} m³
                                    @endif
                                </td>
                                <td>{{ number_format($data['water_total']) }} VND</td>
                            </tr>
                            {{-- Dịch vụ động --}}
                            @if (!empty($data['services']))
                                @foreach ($data['services'] as $sv)
                                    <tr>
                                        <td>{{ $sv['name'] }}</td>
                                        <td>{{ number_format($sv['price']) }}
                                            VND{{ $sv['unit'] ? '/' . $sv['unit'] : '' }}</td>
                                        <td>{{ $sv['qty'] }}</td>
                                        <td>{{ number_format($sv['total']) }} VND</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Tổng cộng</th>
                                <th>{{ number_format($data['total']) }} VND</th>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Nút thao tác --}}
                    <div class="text-end mt-4">
                        <a href="#" class="btn btn-success">Thu tiền</a>
                        <button class="btn btn-outline-secondary" onclick="window.print()">🖨 In hóa đơn</button>
                        <a href="#" class="btn btn-link">Quay lại danh sách phòng</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

