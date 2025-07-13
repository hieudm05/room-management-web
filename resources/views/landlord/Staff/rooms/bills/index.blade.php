@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
<style>
    .tab-content { padding: 20px; border: 1px solid #dee2e6; border-top: none; }
    .invalid-input { border-color: red; }
    .error-message { color: red; font-size: 0.8em; margin-top: 5px; }
    .form-section { margin-bottom: 20px; }
    .form-section h5 { margin-bottom: 15px; }
    .table-services, .table-additional-fees { margin-top: 10px; }
    .btn-group { margin-top: 20px; }
    .additional-fee-row { margin-bottom: 10px; }
    .img-preview { max-width: 100px; margin: 5px; }
    .photo-container { display: flex; flex-wrap: wrap; }
    .locked-message { color: #555; font-style: italic; }
</style>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bage-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Hóa đơn tiền phòng</h4>
            <form id="filter-form" action="{{ route('landlords.staff.payment.index') }}" method="GET" class="d-flex align-items-center">
                <label for="month" class="form-label text-white me-2">Chọn tháng:</label>
                <input type="month" id="month" name="month" class="form-control me-2" value="{{ request('month', now()->format('Y-m')) }}">
                <button type="submit" class="btn btn-light">Lọc</button>
            </form>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (empty($data))
                <div class="alert alert-info">Không có phòng nào để hiển thị.</div>
            @else
                <ul class="nav nav-tabs" id="roomTabs" role="tablist">
                    @foreach ($data as $index => $item)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="room-{{ $item['room_id'] }}-tab" data-bs-toggle="tab" data-bs-target="#room-{{ $item['room_id'] }}" type="button" role="tab" aria-controls="room-{{ $item['room_id'] }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $item['room_name'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content" id="roomTabContent">
                    @foreach ($data as $index => $item)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="room-{{ $item['room_id'] }}" role="tabpanel" aria-labelledby="room-{{ $item['room_id'] }}-tab">
                            <form action="{{ route('landlords.staff.payment.store', $item['room_id']) }}" method="POST" class="bill-form" data-room-id="{{ $item['room_id'] }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="data[month]" value="{{ $item['month'] }}">
                                <input type="hidden" name="data[tenant_name]" value="{{ $item['tenant_name'] }}">
                                <input type="hidden" name="data[area]" value="{{ $item['area'] }}">
                                <input type="hidden" name="data[rent_price]" value="{{ $item['rent_price'] }}">
                                <input type="hidden" name="data[electric_price]" class="electric-price" value="{{ $item['electric_price'] ?? 3000 }}">
                                <input type="hidden" name="data[water_price]" class="water-price" value="{{ $item['water_price'] ?? 20000 }}">
                                <input type="hidden" name="data[water_unit]" class="water-unit" value="{{ $item['water_unit'] ?? 'per_m3' }}">

                                <div class="form-section">
                                    <h5>Thông tin phòng</h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Phòng</label>
                                            <input type="text" class="form-control" value="{{ $item['room_name'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Khách thuê</label>
                                            <input type="text" class="form-control" value="{{ $item['tenant_name'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Diện tích (m²)</label>
                                            <input type="text" class="form-control" value="{{ $item['area'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tiền thuê (VND)</label>
                                            <input type="text" class="form-control" value="{{ number_format($item['rent_price']) }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tháng</label>
                                            <input type="text" class="form-control" value="{{ $item['month'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Trạng thái</label>
                                            <input type="text" class="form-control" value="{{ $item['status'] == 'unpaid' ? 'Chưa thanh toán' : 'Đã thanh toán' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h5>Thông tin điện</h5>
                                    @if ($item['is_bill_locked'])
                                        <p class="locked-message">Hóa đơn đã được lưu, không thể chỉnh sửa thông tin điện.</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Đơn giá (VND/kWh)</label>
                                            <input type="text" class="form-control electric-price-display" value="{{ number_format($item['electric_price'] ?? 3000) }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Chỉ số đầu (kWh)</label>
                                            <input type="number" class="form-control electric-start" data-room-id="{{ $item['room_id'] }}" name="data[electric_start]" value="{{ $item['electric_start'] ?? 0 }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Chỉ số cuối (kWh)</label>
                                            <input type="number" class="form-control electric-end" data-room-id="{{ $item['room_id'] }}" name="data[electric_end]" value="{{ $item['electric_end'] ?? '' }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                            <div class="error-message electric-end-error"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Số kWh</label>
                                            <input type="number" class="form-control electric-kwh" data-room-id="{{ $item['room_id'] }}" name="data[electric_kwh]" value="{{ $item['electric_kwh'] }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tiền điện (VND)</label>
                                            <input type="text" class="form-control electric-total" data-room-id="{{ $item['room_id'] }}" name="data[electric_total]" value="{{ number_format($item['electric_total']) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Ảnh minh chứng (điện)</label>
                                            <input type="file" class="form-control electric-photos" name="data[electric_photos][]" accept="image/*" multiple {{ $item['is_bill_locked'] ? 'disabled' : '' }}>
                                            <div class="photo-container">
                                                @foreach ($item['electric_photos'] as $photo)
                                                    <img src="{{ Storage::url($photo) }}" class="img-preview" alt="Ảnh minh chứng điện">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h5>Thông tin nước</h5>
                                    @if ($item['is_bill_locked'])
                                        <p class="locked-message">Hóa đơn đã được lưu, không thể chỉnh sửa thông tin nước.</p>
                                    @endif
                                    <div class="row">
                                        @if ($item['water_unit'] == 'per_person')
                                            <div class="col-md-3">
                                                <label class="form-label">Đơn giá (VND/người)</label>
                                                <input type="text" class="form-control water-price-display" value="{{ number_format($item['water_price'] ?? 20000) }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Số người</label>
                                                <input type="number" class="form-control water-occupants" data-room-id="{{ $item['room_id'] }}" name="data[water_occupants]" value="{{ $item['water_occupants'] }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                <div class="error-message water-occupants-error"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tiền nước (VND)</label>
                                                <input type="text" class="form-control water-total" data-room-id="{{ $item['room_id'] }}" name="data[water_total]" value="{{ number_format($item['water_total']) }}" readonly>
                                                <input type="hidden" name="data[water_m3]" class="water-m3" value="{{ $item['water_m3'] }}">
                                            </div>
                                        @else
                                            <div class="col-md-3">
                                                <label class="form-label">Đơn giá (VND/m³)</label>
                                                <input type="text" class="form-control water-price-display" value="{{ number_format($item['water_price'] ?? 20000) }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Chỉ số đầu (m³)</label>
                                                <input type="number" class="form-control water-start" data-room-id="{{ $item['room_id'] }}" name="data[water_start]" value="{{ $item['water_start'] ?? 0 }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Chỉ số cuối (m³)</label>
                                                <input type="number" class="form-control water-end" data-room-id="{{ $item['room_id'] }}" name="data[water_end]" value="{{ $item['water_m3'] ? ($item['water_start'] + $item['water_m3']) : '' }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                <div class="error-message water-end-error"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Số m³</label>
                                                <input type="number" class="form-control water-m3" data-room-id="{{ $item['room_id'] }}" name="data[water_m3]" value="{{ $item['water_m3'] }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tiền nước (VND)</label>
                                                <input type="text" class="form-control water-total" data-room-id="{{ $item['room_id'] }}" name="data[water_total]" value="{{ number_format($item['water_total']) }}" readonly>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($item['water_unit'] == 'per_m3')
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label class="form-label">Ảnh minh chứng (nước)</label>
                                                <input type="file" class="form-control water-photos" name="data[water_photos][]" accept="image/*" multiple {{ $item['is_bill_locked'] ? 'disabled' : '' }}>
                                                <div class="photo-container">
                                                    @foreach ($item['water_photos'] as $photo)
                                                        <img src="{{ Storage::url($photo) }}" class="img-preview" alt="Ảnh minh chứng nước">
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="form-section">
                                    <h5>Dịch vụ phụ</h5>
                                    @if (!empty($item['services']))
                                        <table class="table table-bordered table-services">
                                            <thead>
                                                <tr>
                                                    <th>Tên dịch vụ</th>
                                                    <th>Giá (VND)</th>
                                                    <th>Số lượng</th>
                                                    <th>Tổng (VND)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item['services'] as $index => $service)
                                                    <tr>
                                                        <td>{{ $service['name'] }}</td>
                                                        <td>{{ number_format($service['price']) }}</td>
                                                        <td>{{ $service['qty'] }}</td>
                                                        <td>{{ number_format($service['total']) }}</td>
                                                        <input type="hidden" name="data[services][{{ $index }}][service_id]" value="{{ $service['service_id'] }}">
                                                        <input type="hidden" name="data[services][{{ $index }}][price]" value="{{ $service['price'] }}">
                                                        <input type="hidden" name="data[services][{{ $index }}][qty]" value="{{ $service['qty'] }}">
                                                        <input type="hidden" name="data[services][{{ $index }}][total]" value="{{ $service['total'] }}">
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="text-end">
                                            <strong>Tổng tiền dịch vụ: <span class="service-total">{{ number_format($item['service_total']) }}</span> VND</strong>
                                        </div>
                                    @else
                                        <p>Không có dịch vụ phụ.</p>
                                    @endif
                                </div>

                                <div class="form-section">
                                    <h5>Chi phí phát sinh</h5>
                                    <div id="additional-fees-{{ $item['room_id'] }}">
                                        @foreach ($item['additional_fees'] as $index => $fee)
                                            <div class="row additional-fee-row">
                                                <div class="col-md-4">
                                                    <label class="form-label">Tên chi phí</label>
                                                    <input type="text" class="form-control additional-fee-name" name="data[additional_fees][{{ $index }}][name]" value="{{ $fee['name'] ?? '' }}" placeholder="Nhập tên chi phí" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                    <div class="error-message additional-fee-name-error"></div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Giá (VND)</label>
                                                    <input type="number" class="form-control additional-fee-price" name="data[additional_fees][{{ $index }}][price]" value="{{ $fee['price'] ?? '' }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                    <div class="error-message additional-fee-price-error"></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Số lượng</label>
                                                    <input type="number" class="form-control additional-fee-qty" name="data[additional_fees][{{ $index }}][qty]" value="{{ $fee['qty'] ?? 1 }}" placeholder="1" min="1" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                    <div class="error-message additional-fee-qty-error"></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Tổng (VND)</label>
                                                    <input type="text" class="form-control additional-fee-total" name="data[additional_fees][{{ $index }}][total]" value="{{ number_format($fee['total'] ?? 0) }}" readonly>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label"> </label>
                                                    <button type="button" class="btn btn-danger remove-fee" {{ $item['is_bill_locked'] ? 'disabled' : '' }}>Xóa</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-info mt-2 add-fee" data-room-id="{{ $item['room_id'] }}" {{ $item['is_bill_locked'] ? 'disabled' : '' }}>Thêm chi phí</button>
                                    <div class="text-end mt-3">
                                        <strong>Tổng chi phí phát sinh: <span class="additional-fees-total">{{ number_format($item['additional_fees_total'] ?? 0) }}</span> VND</strong>
                                        <input type="hidden" name="data[additional_fees_total]" class="additional-fees-total-input" value="{{ $item['additional_fees_total'] ?? 0 }}">
                                    </div>
                                </div>
                                <div class="form-section">
                                    <h5>Tổng tiền</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Tổng tiền (VND)</label>
                                            <input type="text" class="form-control total" data-room-id="{{ $item['room_id'] }}" name="data[total]" value="{{ number_format($item['total']) }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success" {{ $item['is_bill_locked'] ? 'disabled' : '' }}>Lưu hóa đơn phòng {{ $item['room_name'] }}</button>
                                    <a href="{{ route('landlords.staff.payment.exportExcel', [$item['room_id'], 'month' => $item['month']]) }}" class="btn btn-primary">Xuất Excel</a>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function updateBill(roomId) {
        const form = document.querySelector(`form[data-room-id="${roomId}"]`);
        const electricStartInput = form.querySelector('.electric-start');
        const electricEndInput = form.querySelector('.electric-end');
        const waterStartInput = form.querySelector('.water-start');
        const waterEndInput = form.querySelector('.water-end');
        const waterOccupantsInput = form.querySelector('.water-occupants');
        const additionalFeeRows = form.querySelectorAll('.additional-fee-row');

        // Lấy giá trị từ input, đảm bảo không bị null
        const electricPriceInput = form.querySelector('input[name="data[electric_price]"]');
        const waterPriceInput = form.querySelector('input[name="data[water_price]"]');
        const waterUnitInput = form.querySelector('input[name="data[water_unit]"]');

        const electricStart = parseFloat(electricStartInput.value) || 0;
        const electricEnd = parseFloat(electricEndInput?.value) || 0;
        const electricPrice = parseFloat(electricPriceInput.value) || 3000;
        const waterPrice = parseFloat(waterPriceInput.value) || 20000;
        const waterUnit = waterUnitInput.value || 'per_m3';
        const rentPrice = parseFloat(form.querySelector('input[name="data[rent_price]"]').value) || 0;
        const serviceTotal = parseFloat(form.querySelector('.service-total')?.textContent.replace(/[^0-9]/g, '') || 0);

        // Cập nhật hiển thị đơn giá
        const electricPriceDisplay = form.querySelector('.electric-price-display');
        const waterPriceDisplay = form.querySelector('.water-price-display');
        electricPriceDisplay.value = new Intl.NumberFormat('vi-VN').format(electricPrice);
        waterPriceDisplay.value = new Intl.NumberFormat('vi-VN').format(waterPrice);

        let electricKwh = 0, electricTotal = 0, waterM3 = 0, waterTotal = 0, additionalFeesTotal = 0;
        let isValid = true;

        // Debug: Log các giá trị đầu vào
        console.log('Room ID:', roomId);
        console.log('rentPrice:', rentPrice, 'electricPrice:', electricPrice, 'waterPrice:', waterPrice, 'waterUnit:', waterUnit, 'serviceTotal:', serviceTotal);

        // Validation và tính điện
        if (electricEndInput && !electricEndInput.readOnly) {
            electricEndInput.classList.remove('invalid-input');
            form.querySelector('.electric-end-error').textContent = '';
            if (electricEnd < electricStart) {
                electricEndInput.classList.add('invalid-input');
                form.querySelector('.electric-end-error').textContent = 'Chỉ số cuối phải lớn hơn hoặc bằng chỉ số đầu';
                form.querySelector('.electric-total').value = 'Lỗi chỉ số';
                isValid = false;
            } else {
                electricKwh = electricEnd - electricStart;
                electricTotal = electricKwh * electricPrice;
                form.querySelector('.electric-kwh').value = electricKwh.toFixed(2);
                form.querySelector('.electric-total').value = new Intl.NumberFormat('vi-VN').format(electricTotal);
                form.querySelector('input[name="data[electric_kwh]"]').value = electricKwh;
                form.querySelector('input[name="data[electric_total]"]').value = electricTotal;
            }
        } else {
            electricKwh = parseFloat(form.querySelector('.electric-kwh').value) || 0;
            electricTotal = parseFloat(form.querySelector('input[name="data[electric_total]"]').value.replace(/[^0-9]/g, '')) || 0;
        }

        // Validation và tính nước
        if (waterUnit === 'per_person') {
            const waterOccupants = parseFloat(waterOccupantsInput?.value) || 0;
            if (waterOccupantsInput && !waterOccupantsInput.readOnly) {
                waterOccupantsInput.classList.remove('invalid-input');
                form.querySelector('.water-occupants-error').textContent = '';
                if (waterOccupants < 0) {
                    waterOccupantsInput.classList.add('invalid-input');
                    form.querySelector('.water-occupants-error').textContent = 'Số người phải lớn hơn hoặc bằng 0';
                    form.querySelector('.water-total').value = 'Lỗi số người';
                    isValid = false;
                } else {
                    waterTotal = waterOccupants * waterPrice;
                    waterM3 = waterOccupants;
                    form.querySelector('.water-m3').value = waterM3.toFixed(2);
                    form.querySelector('.water-total').value = new Intl.NumberFormat('vi-VN').format(waterTotal);
                    form.querySelector('input[name="data[water_m3]"]').value = waterM3;
                    form.querySelector('input[name="data[water_total]"]').value = waterTotal;
                    form.querySelector('input[name="data[water_occupants]"]').value = waterOccupants;
                }
            } else {
                waterM3 = parseFloat(form.querySelector('.water-m3').value) || 0;
                waterTotal = parseFloat(form.querySelector('input[name="data[water_total]"]').value.replace(/[^0-9]/g, '')) || 0;
            }
        } else {
            const waterStart = parseFloat(waterStartInput?.value) || 0;
            const waterEnd = parseFloat(waterEndInput?.value) || 0;
            if (waterEndInput && !waterEndInput.readOnly) {
                waterEndInput.classList.remove('invalid-input');
                form.querySelector('.water-end-error').textContent = '';
                if (waterEnd < waterStart) {
                    waterEndInput.classList.add('invalid-input');
                    form.querySelector('.water-end-error').textContent = 'Chỉ số cuối phải lớn hơn hoặc bằng chỉ số đầu';
                    form.querySelector('.water-total').value = 'Lỗi chỉ số';
                    isValid = false;
                } else {
                    waterM3 = waterEnd - waterStart;
                    waterTotal = waterM3 * waterPrice;
                    form.querySelector('.water-m3').value = waterM3.toFixed(2);
                    form.querySelector('.water-total').value = new Intl.NumberFormat('vi-VN').format(waterTotal);
                    form.querySelector('input[name="data[water_m3]"]').value = waterM3;
                    form.querySelector('input[name="data[water_total]"]').value = waterTotal;
                    form.querySelector('input[name="data[water_start]"]').value = waterStart;
                }
            } else {
                waterM3 = parseFloat(form.querySelector('.water-m3').value) || 0;
                waterTotal = parseFloat(form.querySelector('input[name="data[water_total]"]').value.replace(/[^0-9]/g, '')) || 0;
            }
        }

        // Debug: Log kết quả tính toán điện và nước
        console.log('electricKwh:', electricKwh, 'electricTotal:', electricTotal);
        console.log('waterM3:', waterM3, 'waterTotal:', waterTotal);

        // Tính chi phí phát sinh
        additionalFeesTotal = 0; // Khởi tạo lại để tránh cộng dồn
        additionalFeeRows.forEach((row, index) => {
            const nameInput = row.querySelector('.additional-fee-name');
            const priceInput = row.querySelector('.additional-fee-price');
            const qtyInput = row.querySelector('.additional-fee-qty');
            const totalInput = row.querySelector('.additional-fee-total');

            nameInput.classList.remove('invalid-input');
            priceInput.classList.remove('invalid-input');
            qtyInput.classList.remove('invalid-input');
            row.querySelector('.additional-fee-name-error').textContent = '';
            row.querySelector('.additional-fee-price-error').textContent = '';
            row.querySelector('.additional-fee-qty-error').textContent = '';

            const name = nameInput.value.trim();
            const price = parseFloat(priceInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 1;

            if (!name && !row.closest('form').querySelector('button.btn-success').disabled) {
                nameInput.classList.add('invalid-input');
                row.querySelector('.additional-fee-name-error').textContent = 'Vui lòng nhập tên chi phí';
                isValid = false;
            }
            if (price < 0) {
                priceInput.classList.add('invalid-input');
                row.querySelector('.additional-fee-price-error').textContent = 'Giá phải lớn hơn hoặc bằng 0';
                isValid = false;
            }
            if (qty < 1) {
                qtyInput.classList.add('invalid-input');
                row.querySelector('.additional-fee-qty-error').textContent = 'Số lượng phải lớn hơn hoặc bằng 1';
                isValid = false;
            }

            const total = price * qty;
            totalInput.value = new Intl.NumberFormat('vi-VN').format(total);
            additionalFeesTotal += total;

            nameInput.name = `data[additional_fees][${index}][name]`;
            priceInput.name = `data[additional_fees][${index}][price]`;
            qtyInput.name = `data[additional_fees][${index}][qty]`;
            totalInput.name = `data[additional_fees][${index}][total]`;

            // Debug: Log chi phí phát sinh
            console.log(`Additional Fee ${index}:`, { name, price, qty, total });
        });

        // Cập nhật tổng chi phí phát sinh
        form.querySelector('.additional-fees-total').textContent = new Intl.NumberFormat('vi-VN').format(additionalFeesTotal);
        form.querySelector('.additional-fees-total-input').value = additionalFeesTotal;

        // Tính tổng hóa đơn
        const total = isValid ? (rentPrice + electricTotal + waterTotal + serviceTotal + additionalFeesTotal) : 0;
        form.querySelector('.total').value = isValid ? new Intl.NumberFormat('vi-VN').format(total) : 'Lỗi tính toán';
        form.querySelector('input[name="data[total]"]').value = total;

        // Cập nhật bảng tóm tắt
        form.querySelector('.rent-price').textContent = new Intl.NumberFormat('vi-VN').format(rentPrice);
        form.querySelector('.electric-price').textContent = new Intl.NumberFormat('vi-VN').format(electricPrice);
        form.querySelector('.water-price').textContent = new Intl.NumberFormat('vi-VN').format(waterPrice);
        form.querySelector('.electric-total').textContent = new Intl.NumberFormat('vi-VN').format(electricTotal);
        form.querySelector('.water-total').textContent = new Intl.NumberFormat('vi-VN').format(waterTotal);
        form.querySelector('.service-total').textContent = new Intl.NumberFormat('vi-VN').format(serviceTotal);
        form.querySelector('.additional-fees-total').textContent = new Intl.NumberFormat('vi-VN').format(additionalFeesTotal);
        form.querySelector('.total').textContent = isValid ? new Intl.NumberFormat('vi-VN').format(total) : 'Lỗi tính toán';

        // Debug: Log tổng hóa đơn
        console.log('Total Calculation:', {
            rentPrice,
            electricTotal,
            waterTotal,
            serviceTotal,
            additionalFeesTotal,
            total
        });

        // So sánh tổng tiền client và server
        const serverTotal = parseFloat(form.querySelector('input[name="data[total]"]').value.replace(/[^0-9]/g, '')) || 0;
        if (isValid && Math.abs(total - serverTotal) > 1) {
            console.warn('Tổng tiền client và server không khớp:', { client: total, server: serverTotal });
        }

        // Vô hiệu hóa nút Lưu nếu có lỗi hoặc hóa đơn chưa đầy đủ
        const submitButton = form.querySelector('.btn-success');
        submitButton.disabled = !isValid || (electricTotal === 0 && waterTotal === 0);
    }

    // Thêm chi phí phát sinh
    document.querySelectorAll('.add-fee').forEach(button => {
        button.addEventListener('click', () => {
            const roomId = button.dataset.roomId;
            const container = document.querySelector(`#additional-fees-${roomId}`);
            const index = container.querySelectorAll('.additional-fee-row').length;
            const row = document.createElement('div');
            row.className = 'row additional-fee-row';
            row.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label">Tên chi phí</label>
                    <input type="text" class="form-control additional-fee-name" name="data[additional_fees][${index}][name]" placeholder="Nhập tên chi phí">
                    <div class="error-message additional-fee-name-error"></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Giá (VND)</label>
                    <input type="number" class="form-control additional-fee-price" name="data[additional_fees][${index}][price]" placeholder="0" min="0">
                    <div class="error-message additional-fee-price-error"></div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Số lượng</label>
                    <input type="number" class="form-control additional-fee-qty" name="data[additional_fees][${index}][qty]" value="1" min="1">
                    <div class="error-message additional-fee-qty-error"></div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tổng (VND)</label>
                    <input type="text" class="form-control additional-fee-total" name="data[additional_fees][${index}][total]" value="0" readonly>
                </div>
                <div class="col-md-1">
                    <label class="form-label"> </label>
                    <button type="button" class="btn btn-danger remove-fee">Xóa</button>
                </div>
            `;
            container.appendChild(row);

            // Gắn sự kiện input cho các trường mới
            row.querySelectorAll('.additional-fee-name, .additional-fee-price, .additional-fee-qty').forEach(input => {
                input.addEventListener('input', () => {
                    console.log('New fee input changed:', input.name, input.value);
                    updateBill(roomId);
                });
            });

            updateBill(roomId);
        });
    });

    // Xóa chi phí phát sinh
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-fee')) {
            const row = e.target.closest('.additional-fee-row');
            const roomId = row.closest('form').dataset.roomId;
            row.remove();
            updateBill(roomId);
        }
    });

    // Cập nhật khi nhập liệu
    document.querySelectorAll('.electric-end, .water-end, .water-occupants, .additional-fee-name, .additional-fee-price, .additional-fee-qty').forEach(input => {
        input.addEventListener('input', () => {
            const roomId = input.closest('form').dataset.roomId;
            console.log('Input changed:', input.name, input.value);
            updateBill(roomId);
        });
    });

    // Xem trước ảnh
    document.querySelectorAll('.electric-photos, .water-photos').forEach(input => {
        input.addEventListener('change', (e) => {
            const container = e.target.parentElement.querySelector('.photo-container');
            container.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                const preview = document.createElement('img');
                preview.className = 'img-preview';
                preview.src = URL.createObjectURL(file);
                container.appendChild(preview);
            });
        });
    });

    // Kích hoạt tính toán khi tải trang
    document.querySelectorAll('.bill-form').forEach(form => {
        const roomId = form.dataset.roomId;
        console.log('Initial update for room:', roomId);
        updateBill(roomId);
    });
});
</script>
@endsection