@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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
        <form action="{{ route('landlords.staff.payment.store', $room->room_id) }}" method="post">
            @csrf
            <input type="hidden" name="data[month]" value="{{ $data['month'] }}">
            <input type="hidden" name="data[tenant_name]" value="{{ $data['tenant_name'] }}">
            <input type="hidden" name="data[area]" value="{{ $data['area'] }}">
            <input type="hidden" name="data[rent_price]" value="{{ $data['rent_price'] }}">
            <input type="hidden" name="data[electric_unit_price]" value="{{ $data['electric_price'] }}">
            <input type="hidden" name="data[electric_start]" value="{{ $data['electric_start'] }}">
            <input type="hidden" name="data[electric_end]" value="{{ $data['electric_end'] }}">
            <input type="hidden" name="data[electric_kwh]" value="{{ $data['electric_kwh'] }}">
            <input type="hidden" name="data[electric_total]" value="{{ $data['electric_total'] }}">
            <input type="hidden" name="data[water_price]" value="{{ $data['water_price'] }}">
            <input type="hidden" name="data[water_unit]" value="{{ $data['water_unit'] }}">
            <input type="hidden" name="data[water_occupants]" value="{{ $data['water_occupants'] }}">
            <input type="hidden" name="data[water_m3]" value="{{ $data['water_m3'] }}">
            <input type="hidden" name="data[water_total]" value="{{ $data['water_total'] }}">
            <input type="hidden" name="data[total]" value="{{ $data['total'] }}">
            @foreach ($data['services'] as $index => $sv)
                <input type="hidden" name="data[services][{{ $index }}][service_id]"
                    value="{{ $sv['service_id'] }}">
                <input type="hidden" name="data[services][{{ $index }}][price]" value="{{ $sv['price'] }}">
                <input type="hidden" name="data[services][{{ $index }}][qty]" value="{{ $sv['qty'] }}">
                <input type="hidden" name="data[services][{{ $index }}][total]" value="{{ $sv['total'] }}">
            @endforeach
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
                                    <td>{{ number_format($data['electric_price']) }} VND/kWh</td>
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
                            <button class="btn btn-success">Thu tiền</button>
                            <a href="{{ route('landlords.staff.payment.export', ['room' => $room->room_id, 'month' => $data['month']]) }}"
                                class="btn btn-outline-success">
                                📥 Xuất Excel
                            </a>
                            <a href="#" class="btn btn-link">Quay lại danh sách phòng</a>
                        </div>
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection
