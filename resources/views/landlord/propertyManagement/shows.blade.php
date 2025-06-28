@extends('landlord.layouts.app')
@section('title', $property->name)

@section('content')
    <div class="container">

        {{-- Thông tin tổng quan tòa nhà --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title mb-2">{{ $property->name }}</h3>
                <p class="mb-1"><strong>Địa chỉ:</strong> {{ $property->address }}</p>
                <p class="mb-0"><strong>Trạng thái:</strong>
                    <span
                        class="badge bg-{{ $property->status === 'Approved' ? 'success' : ($property->status === 'Pending' ? 'warning' : 'secondary') }}">
                        {{ $property->status }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Các chức năng liên quan --}}
        <div class="row">
            {{-- Quản lý phòng --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white fw-bold">Phòng</div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('landlords.rooms.index', ['property_id' => $property->property_id]) }}"
                            class="btn btn-outline-primary btn-sm w-100">Danh sách phòng</a>
                    </div>
                </div>
            </div>

            {{-- Ngân hàng --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white fw-bold">Tài khoản ngân hàng</div>
                    <div class="card-body d-flex flex-column gap-2">
                        @if ($property->bank_account_id)
                            <div class="mb-2">
                                <div class="fw-semibold">Đang gắn:</div>
                                <div class="border rounded p-2 bg-light">
                                    <div><strong>{{ $property->bankAccount->bank_name }}</strong> -
                                        {{ $property->bankAccount->bank_account_number }}</div>
                                    <div><small>{{ $property->bankAccount->bank_account_name }}</small></div>
                                </div>
                            </div>
                            {{-- Đổi tài khoản --}}
                            <form action="{{ route('landlords.properties.bank_accounts.update', $property->property_id) }}"
                                method="POST" class="mb-2">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <label for="bank_account_id" class="form-label">Đổi sang tài khoản khác:</label>
                                    <select name="bank_account_id" id="bank_account_id" class="form-select" required>
                                        <option value="">-- Chọn tài khoản --</option>
                                        @foreach ($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}"
                                                {{ $property->bank_account_id == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->bank_name }} - {{ $bank->bank_account_number }}
                                                ({{ $bank->bank_account_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">Cập nhật tài khoản</button>
                            </form>
                            {{-- Huỷ gán --}}
                            <form
                                action="{{ route('landlords.properties.bank_accounts.unassign', $property->property_id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Huỷ gán tài
                                    khoản</button>
                            </form>
                        @else
                            <div class="mb-2 text-muted">Chưa gán tài khoản ngân hàng nào cho tòa này.</div>
                            <form action="{{ route('landlords.properties.bank_accounts.update', $property->property_id) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <label for="bank_account_id" class="form-label">Gán tài khoản:</label>
                                    <select name="bank_account_id" id="bank_account_id" class="form-select" required>
                                        <option value="">-- Chọn tài khoản --</option>
                                        @foreach ($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}">
                                                {{ $bank->bank_name }} - {{ $bank->bank_account_number }}
                                                ({{ $bank->bank_account_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">Gán tài khoản</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Giấy tờ, hợp đồng --}}
            <div class="col-md-4 mb-3">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning text-dark fw-bold">Giấy tờ & Hợp đồng</div>
                    <div class="card-body d-flex flex-column gap-2">
                        <a href="{{ route('landlords.properties.uploadDocument', ['property_id' => $property->property_id]) }}"
                            class="btn btn-outline-warning btn-sm w-100">Upload Document</a>
                        <a href="{{ route('landlords.properties.show', ['property_id' => $property->property_id]) }}"
                            class="btn btn-outline-warning btn-sm w-100">Danh sách Document</a>
                    </div>
                </div>
            </div>


            {{-- Thêm chức năng xuất Excel tổng hợp hóa đơn theo tháng --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form class="row g-2 align-items-end mb-3" method="GET" action="">
                        <div class="col-auto">
                            <label for="month" class="form-label mb-0">Chọn tháng:</label>
                            <input type="month" class="form-control" id="month" name="month"
                                value="{{ request('month', now()->format('Y-m')) }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Xem tổng hợp hóa đơn</button>
                            <a href="{{ route('landlords.properties.bills.export', ['property' => $property->property_id, 'month' => request('month', now()->format('Y-m'))]) }}"
                                class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                            </a>
                        </div>
                    </form>

                    @if (isset($bills) && count($bills))
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-warning">
                                    <tr>
                                        <th>STT</th>
                                        <th>Phòng</th>
                                        <th>Điện cũ</th>
                                        <th>Điện mới</th>
                                        <th>Số điện</th>
                                        <th>Số nước</th>
                                        <th>Tiền phòng</th>
                                        <th>Tiền điện</th>
                                        <th>Tiền nước</th>
                                        <th>Dịch vụ</th>
                                        <th>Tổng cộng</th>
                                        <th>Thanh toán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $tong = 0; @endphp
                                    @foreach ($bills as $i => $item)
                                        @php $tong += $item['total'] ?? 0; @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item['room']->room_number ?? $item['room']->room_name }}</td>
                                            <td>{{ $item['bill']->electric_start }}</td>
                                            <td>{{ $item['bill']->electric_end }}</td>
                                            <td>{{ $item['bill']->electric_kwh }}</td>
                                            <td>{{ $item['bill']->water_m3 }}</td>
                                            <td>{{ number_format($item['bill']->rent_price) }}</td>
                                            <td>{{ number_format($item['bill']->electric_total) }}</td>
                                            <td>{{ number_format($item['bill']->water_total) }}</td>
                                            <td>{{ number_format($item['service_total']) }}</td>
                                            <td>{{ number_format($item['total']) }}</td>
                                            <td>
                                                {{ $item['bill']->status == 'unpaid' ? 'Chưa thanh toán' : 'Đã thanh toán' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-success fw-bold">
                                        <td colspan="10" class="text-end">TỔNG DOANH THU</td>
                                        <td colspan="2">{{ number_format($tong) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
