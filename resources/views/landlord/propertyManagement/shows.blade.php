@extends('landlord.layouts.app')
@section('title', $property->name)

@section('content')
    <div class="container">

        {{-- Thông tin tổng quan tòa nhà --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bage-primary text-white d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0">{{ $property->name }}</h3>
                <span
                    class="badge fs-6 bg-{{ $property->status === 'Approved' ? 'success' : ($property->status === 'Pending' ? 'warning' : 'secondary') }}">
                    {{ $property->status === 'Approved' ? 'Đã duyệt' : ($property->status === 'Pending' ? 'Chờ duyệt' : 'Tạm dừng') }}
                </span>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    {{-- Ảnh đại diện --}}
                    @if ($property->image_url)
                        <div class="col-md-4 mb-3 mb-md-0 text-center">
                            <img src="{{ $property->image_url }}" alt="Ảnh bất động sản"
                                class="img-fluid rounded shadow border" style="max-height: 250px; object-fit:cover;">
                        </div>
                    @endif
                    <div class="col-md-8">
                        <p class="mb-2"><strong>Địa chỉ:</strong> {{ $property->address }}</p>
                        <p class="mb-2"><strong>Kinh độ:</strong> {{ $property->longitude }} &nbsp; <strong>Vĩ
                                độ:</strong> {{ $property->latitude }}</p>
                        <p class="mb-2"><strong>Mô tả:</strong> {{ $property->description ?? 'Không có mô tả' }}</p>
                        <p class="mb-2"><strong>Chủ trọ:</strong> {{ $property->landlord_name }} (ID:
                            {{ $property->landlord_id }})</p>
                        <p class="mb-2"><strong>Nội quy:</strong></p>
                        <div class="mb-2"
                            style="background: #f8f9fa; border-radius: 6px; padding: 12px; max-height: 120px; overflow: hidden; position: relative;">
                            <div id="rules-preview">
                                {!! \Illuminate\Support\Str::limit(strip_tags($property->rules, '<ul><ol><li><strong><em><b><i>'), 200, '...') !!}
                            </div>
                            <button type="button" class="btn btn-link btn-sm position-absolute end-0 bottom-0"
                                style="z-index:2; background:rgba(255,255,255,0.8);" data-bs-toggle="modal"
                                data-bs-target="#rulesModal">
                                Xem chi tiết
                            </button>
                        </div>

                        {{-- Modal hiển thị nội quy đầy đủ --}}
                        <div class="modal fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rulesModalLabel">Nội quy phòng trọ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Đóng"></button>
                                    </div>
                                    <div class="modal-body">
                                        {!! $property->rules ?? '<span class="text-muted">Không có nội quy</span>' !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thư viện ảnh khu trọ --}}
                @if ($property->images && $property->images->count())
                    <div class="mt-4">
                        <h5 class="mb-3">Thư viện ảnh khu trọ</h5>
                        <div class="row g-2">
                            @foreach ($property->images as $img)
                                <div class="col-6 col-md-3">
                                    <div class="border rounded overflow-hidden" style="height: 120px;">
                                        <img src="{{ $img->image_path }}" alt="Ảnh khu trọ" class="img-fluid w-100 h-100"
                                            style="object-fit:cover;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                            class="btn btn-outline-warning btn-sm w-100">Tải Lên Tài Liệu</a>

                        <a href="{{ route('landlords.properties.show', ['property_id' => $property->property_id]) }}"
                            class="btn btn-outline-warning btn-sm w-100">Danh sách Tài Liệu</a>
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
