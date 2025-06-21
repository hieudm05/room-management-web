@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
<div class="container">
    {{-- Bộ lọc tháng --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="#" method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label for="month" class="col-form-label">Chọn tháng:</label>
                </div>
                <div class="col-auto">
                    <input type="month" id="month" name="month" class="form-control" value="{{ request('month') ?? date('Y-m') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Nội dung hóa đơn --}}
    <div class="card shadow-sm">
        <div class="card-header bage-success text-white">
            <h4 class="mb-0">Hoá đơn tiền phòng P101 (Tháng 06/2024)</h4>
        </div>
        <div class="card-body">

            {{-- Thông tin phòng và khách --}}
            <h5 class="mb-3">Thông tin phòng & khách thuê</h5>
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <th>Phòng</th>
                        <td>P101</td>
                    </tr>
                    <tr>
                        <th>Khách thuê</th>
                        <td>Nguyễn Văn A</td>
                    </tr>
                    <tr>
                        <th>Diện tích</th>
                        <td>25 m²</td>
                    </tr>
                    <tr>
                        <th>Giá thuê</th>
                        <td>2,500,000 VND</td>
                    </tr>
                    <tr>
                        <th>Tháng thanh toán</th>
                        <td>06/2024</td>
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
                        <td>2,500,000 VND</td>
                        <td>1</td>
                        <td>2,500,000 VND</td>
                    </tr>
                    <tr>
                        <td>Điện</td>
                        <td>3,500 VND/kWh</td>
                        <td>50 kWh</td>
                        <td>175,000 VND</td>
                    </tr>
                    <tr>
                        <td>Nước</td>
                        <td>20,000 VND/m³</td>
                        <td>5 m³</td>
                        <td>100,000 VND</td>
                    </tr>
                    <tr>
                        <td>Internet</td>
                        <td>-</td>
                        <td>-</td>
                        <td>100,000 VND</td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Tổng cộng</th>
                        <th>2,875,000 VND</th>
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
</div>
@endsection
