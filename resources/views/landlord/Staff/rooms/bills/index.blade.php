@extends('landlord.layouts.app')

@section('title', 'Hoá đơn tiền phòng')

@section('content')
    <style>
        .tab-content { padding: 20px; border: 1px solid #dee2e6; border-top: none; }
        .invalid-input { border-color: red; }
        .error-message { color: red; font-size: 0.8em; margin-top: 5px; }
        .form-section { margin-bottom: 20px; }
        .form-section h5 { margin-bottom: 15px; }
        .btn-group { margin-top: 20px; }
        .additional-fee-row { margin-bottom: 10px; }
        .img-preview { max-width: 100px; margin: 5px; }
        .photo-container { display: flex; flex-wrap: wrap; }
        .locked-message { color: #555; font-style: italic; }
        .billing-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .billing-info h6 { color: #007bff; margin-bottom: 10px; }
        .price-breakdown { font-size: 0.9em; color: #6c757d; }
        .original-price { text-decoration: line-through; color: #dc3545; }
        .actual-price { font-weight: bold; color: #28a745; }
    </style>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bage-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Hóa đơn tiền phòng</h4>
                <form id="filter-form" action="{{ route('landlords.staff.payment.index') }}" method="GET" class="d-flex align-items-center">
                    <label for="month" class="form-label text-white me-2">Chọn tháng:</label>
                    <input type="month" id="month" name="month" class="form-control me-2" value="{{ request('month', now()->format('Y-m')) }}">
                    <input type="hidden" name="property_id" value="{{ request('property_id') }}">
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
                    @php $countTenant = collect($data)->where('tenant_name', '!=', 'Chưa có')->count(); @endphp

                    @if ($countTenant === 0)
                        <div class="alert alert-info">Hiện không có phòng nào có người thuê để lập hóa đơn.</div>
                    @else
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="roomTabs" role="tablist">
                            @foreach ($data as $index => $item)
                                @if($item['tenant_name'] !== 'Chưa có')
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                                id="room-{{ $item['room_id'] }}-tab" data-bs-toggle="tab"
                                                data-bs-target="#room-{{ $item['room_id'] }}" type="button" role="tab"
                                                aria-controls="room-{{ $item['room_id'] }}" 
                                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $item['room_name'] }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content" id="roomTabContent">
                            @foreach ($data as $index => $item)
                                @if($item['tenant_name'] !== 'Chưa có')
                                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                         id="room-{{ $item['room_id'] }}" role="tabpanel"
                                         aria-labelledby="room-{{ $item['room_id'] }}-tab">
                                        
                                        <!-- Billing Info -->
                                        @if($item['should_show_billing_info'])
                                            <div class="billing-info">
                                                <h6><i class="fas fa-calendar-alt"></i> Thông tin kỳ tính tiền</h6>
                                                <div class="row">
                                                    <div class="col-md-3"><strong>Từ ngày:</strong> {{ $item['billing_start_date'] }}</div>
                                                    <div class="col-md-3"><strong>Đến ngày:</strong> {{ $item['billing_end_date'] }}</div>
                                                  <div class="col-md-3">
                                                    <strong>Số ngày:</strong> {{ $item['billing_days'] }}/{{ $item['total_days_in_month'] }} ngày
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Tỷ lệ tính tiền:</strong> 
                                                    @if($item['billing_ratio'] == 1.0)
                                                        100%
                                                    @else
                                                        {{ number_format($item['billing_ratio'] * 100, 2) }}%
                                                    @endif
                                                </div>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-info-circle"></i>
                                                        @switch($item['billing_reason'])
                                                            @case('contract_start_in_month')
                                                                Hợp đồng bắt đầu trong tháng này - tiền phòng và dịch vụ tính theo tỷ lệ ngày ở thực tế.
                                                                @break
                                                            @case('contract_end_in_month') 
                                                                Hợp đồng kết thúc trong tháng này - tiền phòng và dịch vụ tính theo tỷ lệ ngày ở thực tế.
                                                                @break
                                                            @default
                                                                Tiền phòng, dịch vụ tính theo người và chi phí phát sinh sẽ được tính theo tỷ lệ ngày ở thực tế.
                                                        @endswitch
                                                        Tiền điện và nước (tính theo m³) giữ nguyên theo chỉ số sử dụng.
                                                    </small>
                                                </div>
                                            </div>
                                        @elseif($item['billing_reason'] == 'outside_contract_period')
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Tháng này nằm ngoài thời gian hợp đồng thuê. Không tính tiền.
                                            </div>
                                        @endif

                                        <!-- Main Form -->
                                        <form action="{{ route('landlords.staff.payment.store', $item['room_id']) }}" 
                                              method="POST" class="bill-form" data-room-id="{{ $item['room_id'] }}"
                                              enctype="multipart/form-data">
                                            @csrf
                                            
                                            <!-- Hidden Inputs -->
                                            
                                            <input type="hidden" name="data[month]" value="{{ $item['month'] }}">
                                            <input type="hidden" name="data[tenant_name]" value="{{ $item['tenant_name'] }}">
                                            <input type="hidden" name="data[area]" value="{{ $item['area'] }}">
                                            <input type="hidden" name="data[rent_price]" value="{{ $item['rent_price'] }}">
                                            <input type="hidden" name="data[original_rent_price]" value="{{ $item['original_rent_price'] ?? $item['rent_price'] }}">
                                            <input type="hidden" name="data[billing_days]" value="{{ $item['billing_days'] }}">
                                            <input type="hidden" name="data[total_days_in_month]" value="{{ $item['total_days_in_month'] }}">
                                            <input type="hidden" name="data[billing_ratio]" value="{{ $item['billing_ratio'] }}">
                                            <input type="hidden" name="data[electric_price]" class="electric-price" value="{{ $item['electric_price'] ?? 3000 }}">
                                            <input type="hidden" name="data[water_price]" class="water-price" value="{{ $item['water_price'] ?? 20000 }}">
                                            <input type="hidden" name="data[water_unit]" class="water-unit" value="{{ $item['water_unit'] ?? 'per_m3' }}">
                                            <input type="hidden" class="total-after-complaint" value="{{ $item['total_after_complaint'] ?? 0 }}">
                                            <input type="hidden" name="data[complaint_user_cost]" value="{{ $item['complaint_user_cost'] ?? 0 }}">
                                            <input type="hidden" name="data[complaint_landlord_cost]" value="{{ $item['complaint_landlord_cost'] ?? 0 }}">

                                            <!-- Room Information -->
                                            <div class="form-section">
                                                <h5>Thông tin phòng</h5>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="form-label">Phòng</label>
                                                        <input type="text" class="form-control" value="{{ $item['room_name'] }}" >
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Khách thuê</label>
                                                        <input type="text" class="form-control" value="{{ $item['tenant_name'] }}" >
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Diện tích (m²)</label>
                                                        <input type="text" class="form-control" value="{{ $item['area'] }}" >
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Tháng</label>
                                                        <input type="text" class="form-control" value="{{ $item['month'] }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Tiền thuê (VND)</label>
                                                        <input type="text" class="form-control" value="{{ number_format($item['rent_price']) }}" readonly>
                                                        @if($item['should_show_billing_info'] && $item['billing_ratio'] < 1)
                                                            <div class="price-breakdown">
                                                                <span class="original-price">{{ number_format($item['original_rent_price'] ? $item['original_rent_price'] : $item['rent_price']) }} VND</span>
                                                                × {{ number_format($item['billing_ratio'] * 100, 1) }}% = 
                                                                <span class="actual-price">{{ number_format($item['rent_price']) }} VND</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Trạng thái thanh toán</label>
                                                        <div class="input-group">
                                                            @if (!empty($item['bill']))
                                                                <select class="form-control status-select" data-id="{{ $item['id_bill'] }}">
                                                                    <option value="unpaid" {{ $item['bill']->status == 'unpaid' ? 'selected' : '' }}>⏳ Chưa thanh toán</option>
                                                                    <option value="pending" {{ $item['bill']->status == 'pending' ? 'selected' : '' }}>🔄 Đang xử lý</option>
                                                                    <option value="paid" {{ $item['bill']->status == 'paid' ? 'selected' : '' }}>✅ Đã thanh toán</option>
                                                                </select>
                                                            @else
                                                                <input type="text" class="form-control text-muted bg-light" value="Chưa tạo hóa đơn" disabled>
                                                            @endif
                                                        </div>
                                                        <span class="status-msg text-success small mt-1 d-block" id="status-msg-{{ $item['id_bill'] ?? 'no-bill' }}"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Electric Information -->
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
                                                    <div class="col-md-2">
                                                        <label class="form-label">Chỉ số đầu (kWh)</label>
                                                        <input type="number" class="form-control electric-start" data-room-id="{{ $item['room_id'] }}" name="data[electric_start]" value="{{ $item['electric_start'] ?? 0 }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Chỉ số cuối (kWh)</label>
                                                        <input type="number" class="form-control electric-end" data-room-id="{{ $item['room_id'] }}" name="data[electric_end]" value="{{ $item['electric_end'] ?? '' }}" placeholder="0" min="0">
                                                        <div class="error-message electric-end-error"></div>
                                                    </div>
                                                    <div class="col-md-2">
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

                                            <!-- Water Information -->
                                            <div class="form-section">
                                                <h5>Thông tin nước</h5>
                                                @if ($item['is_bill_locked'])
                                                    <p class="locked-message">Hóa đơn đã được lưu, không thể chỉnh sửa thông tin nước.</p>
                                                @endif
                                                <div class="row">
                                                    @if ($item['water_unit'] == 'per_person')
                                                        <div class="col-md-4">
                                                            <label class="form-label">Đơn giá (VND/người)</label>
                                                            <input type="text" class="form-control water-price-display" value="{{ number_format($item['water_price'] ?? 20000) }}" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Số người</label>
                                                            <input type="number" class="form-control water-occupants" data-room-id="{{ $item['room_id'] }}" name="data[water_occupants]" value="{{ $item['water_occupants'] }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                            <div class="error-message water-occupants-error"></div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Tiền nước (VND)</label>
                                                            <input type="text" class="form-control water-total" data-room-id="{{ $item['room_id'] }}" name="data[water_total]" value="{{ number_format($item['water_total']) }}" readonly>
                                                            <input type="hidden" name="data[water_m3]" class="water-m3" value="{{ $item['water_m3'] }}">
                                                            @if ($item['billing_ratio'] < 1)
                                                                <div class="price-breakdown">
                                                                    <small>Tính theo tỷ lệ {{ number_format($item['billing_ratio'] * 100, 1) }}%</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="col-md-3">
                                                            <label class="form-label">Đơn giá (VND/m³)</label>
                                                            <input type="text" class="form-control water-price-display" value="{{ number_format($item['water_price'] ?? 20000) }}" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label">Chỉ số đầu (m³)</label>
                                                            <input type="number" class="form-control water-start" data-room-id="{{ $item['room_id'] }}" name="data[water_start]" value="{{ $item['water_start'] ?? 0 }}" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label">Chỉ số cuối (m³)</label>
                                                            <input type="number" class="form-control water-end" data-room-id="{{ $item['room_id'] }}" name="data[water_end]" value="{{ $item['water_m3'] ? $item['water_start'] + $item['water_m3'] : '' }}" placeholder="0" min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                            <div class="error-message water-end-error"></div>
                                                        </div>
                                                        <div class="col-md-2">
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

                                            <!-- Services -->
                                            <div class="form-section">
                                                <h5>Dịch vụ phụ</h5>
                                                @if (!empty($item['services']))
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên dịch vụ</th>
                                                                <th>Đơn giá</th>
                                                                <th>Số lượng</th>
                                                                @if($item['should_show_billing_info'])
                                                                    <th>Tổng gốc</th>
                                                                @endif
                                                                <th>Tổng thực tế</th>
                                                                <th>Loại</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($item['services'] as $serviceIndex => $service)
                                                                <tr>
                                                                    <td>
                                                                        {{ $service['name'] }}
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][name]" value="{{ $service['name'] }}">
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][service_id]" value="{{ $service['service_id'] }}">
                                                                    </td>
                                                                    <td>
                                                                        {{ $service['price'] == 0 ? 'Miễn phí' : number_format($service['price']) }}
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][price]" value="{{ $service['price'] }}">
                                                                    </td>
                                                                    <td>
                                                                        {{ $service['qty'] }}
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][qty]" value="{{ $service['qty'] }}">
                                                                    </td>
                                                                    @if($item['should_show_billing_info'])
                                                                        <td>
                                                                            @if(isset($service['original_total']) && $service['billing_ratio'] < 1)
                                                                                <span class="original-price">{{ $service['original_total'] == 0 ? 'Miễn phí' : number_format($service['original_total']) }}</span>
                                                                            @else
                                                                                {{ $service['actual_total'] == 0 ? 'Miễn phí' : number_format($service['actual_total']) }}
                                                                            @endif
                                                                        </td>
                                                                    @endif
                                                                    <td>
                                                                        <span class="actual-price">{{ $service['actual_total'] == 0 ? 'Miễn phí' : number_format($service['actual_total']) }}</span>
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][actual_total]" value="{{ $service['actual_total'] }}">
                                                                        @if($item['should_show_billing_info'] && isset($service['billing_ratio']) && $service['billing_ratio'] < 1 && $service['actual_total'] != ($service['original_total'] ?? $service['actual_total']))
                                                                            <div class="price-breakdown">
                                                                                <small>× {{ number_format($service['billing_ratio'] * 100, 1) }}%</small>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        {{ $service['type_display'] }}
                                                                        <input type="hidden" name="data[services][{{ $serviceIndex }}][type_display]" value="{{ $service['type_display'] }}">
                                                                    </td>
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

                                            <!-- Complaints -->
                                          <!-- Complaints Section - KHÔNG tính theo tỷ lệ -->
                                        @if (!empty($item['complaints']))
                                            <div class="form-section">
                                                <h5>Chi phí sau khiếu nại</h5>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Vấn đề khiếu nại</th>
                                                            <th>Giá người thuê chịu</th>
                                                            <th>Giá chủ trọ chịu</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($item['complaints'] as $complaint)
                                                            <tr>
                                                                <td>{{ $complaint['detail'] }}</td>
                                                                <td>{{ $complaint['user_cost'] ? number_format($complaint['user_cost']) : 0 }}</td>
                                                                <td>{{ $complaint['landlord_cost'] ? number_format($complaint['landlord_cost']) : 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="text-end">
                                                    <strong>Chi phí người thuê sau khiếu nại: <span class="service-total">{{ number_format($item['total_after_complaint']) }}</span> VND</strong>
                                                    <div class="mt-1">
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle"></i>
                                                            Chi phí khiếu nại không tính theo tỷ lệ ngày - áp dụng nguyên giá.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Additional Fees Section - KHÔNG tính theo tỷ lệ -->
                                        <div class="form-section">
                                            <h5>Chi phí phát sinh</h5>
                                            <div id="additional-fees-{{ $item['room_id'] }}">
                                                @foreach ($item['additional_fees'] as $feeIndex => $fee)
                                                    <div class="row additional-fee-row">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Tên chi phí</label>
                                                            <input type="text" class="form-control additional-fee-name"
                                                                name="data[additional_fees][{{ $feeIndex }}][name]"
                                                                value="{{ $fee['name'] ?? '' }}"
                                                                placeholder="Nhập tên chi phí"
                                                                {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                            <div class="error-message additional-fee-name-error"></div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label">Giá (VND)</label>
                                                            <input type="number" class="form-control additional-fee-price"
                                                                name="data[additional_fees][{{ $feeIndex }}][price]"
                                                                value="{{ $fee['price'] ?? '' }}" placeholder="0"
                                                                min="0" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                            <div class="error-message additional-fee-price-error"></div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <label class="form-label">SL</label>
                                                            <input type="number" class="form-control additional-fee-qty"
                                                                name="data[additional_fees][{{ $feeIndex }}][qty]"
                                                                value="{{ $fee['qty'] ?? 1 }}" placeholder="1"
                                                                min="1" {{ $item['is_bill_locked'] ? 'readonly' : '' }}>
                                                            <div class="error-message additional-fee-qty-error"></div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Tổng tiền</label>
                                                            <input type="text" class="form-control additional-fee-total"
                                                                name="data[additional_fees][{{ $feeIndex }}][total]"
                                                                value="{{ isset($fee['total']) ? number_format($fee['total']) : (isset($fee['total']) ? number_format($fee['total']) : '') }}" readonly>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label"> </label>
                                                            <button type="button" class="btn btn-danger btn-sm remove-fee"
                                                                {{ $item['is_bill_locked'] ? 'disabled' : '' }}>Xóa</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-info mt-2 add-fee"
                                                data-room-id="{{ $item['room_id'] }}"
                                                {{ $item['is_bill_locked'] ? 'disabled' : '' }}>Thêm chi phí</button>
                                            <div class="text-end mt-3">
                                                <strong>Tổng chi phí phát sinh: <span class="additional-fees-total">{{ number_format($item['additional_fees_total'] ?? 0) }}</span> VND</strong>
                                                <input type="hidden" name="data[additional_fees_total]" class="additional-fees-total-input" value="{{ $item['additional_fees_total'] ?? 0 }}">
                                                <div class="mt-1">
                                                    <small class="text-info">
                                                        <i class="fas fa-info-circle"></i>
                                                        Chi phí phát sinh không tính theo tỷ lệ ngày - áp dụng nguyên giá.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                            <!-- Total -->
                                            <div class="form-section">
                                                <h5>Tổng tiền</h5>
                                                <div class="row">
                                                    {{-- <div class="col-md-6">
                                                        <label class="form-label">Tổng tiền (VND)</label>
                                                        <input type="text" class="form-control total" data-room-id="{{ $item['room_id'] }}" name="data[total]" value="{{ number_format($item['total']) }}" data-original-total="{{ $item['total'] }}" readonly>
                                                    </div> --}}
                                                     <div class="col-md-6">
                                                        <label class="form-label">Tổng tiền (VND)</label>
                                                        <input type="text" class="form-control total" data-room-id="{{ $item['room_id'] }}" name="data[total]" value="{{$item['total']}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Buttons -->
                                            <div class="btn-group">
                                                <button type="submit" class="btn btn-success" {{ $item['is_bill_locked'] ? 'disabled' : '' }}>
                                                    Lưu hóa đơn phòng {{ $item['room_name'] }}
                                                </button>
                                                <a href="{{ route('landlords.staff.payment.exportExcel', [$item['room_id'], 'month' => $item['month']]) }}" class="btn btn-primary">
                                                    Xuất Excel
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJ+YxkTn6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script>
        $(document).ready(function() {
            // Status update functionality
            $('.status-select').on('change', function() {
                const selectBox = $(this);
                const billId = selectBox.data('id');
                const newStatus = selectBox.val();
                const msgSpan = $('#status-msg-' + billId);

                // if (newStatus === 'pending' || newStatus === 'paid') {
                //     msgSpan.text('⚠️ Trạng thái này không thể chỉnh sửa.')
                //         .removeClass('text-success').addClass('text-warning');
                //     selectBox.prop('disabled', true);
                //     return;
                // }

                $.ajax({
                    url: '/landlords/staff/payment/room-bills/' + billId + '/update-status',
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        msgSpan.text('✅ Trạng thái đã được cập nhật thành công!')
                            .removeClass('text-danger text-warning')
                            .addClass('text-success');

                        if (['pending', 'paid'].includes(newStatus)) {
                            selectBox.prop('disabled', true);
                        }
                    },
                    error: function() {
                        msgSpan.text('❌ Cập nhật thất bại!')
                            .removeClass('text-success')
                            .addClass('text-danger');
                    }
                });
            });

            // Thêm function để format số ngày trong JavaScript
                function formatBillingDays(days) {
                    return Math.round(days); // Làm tròn số ngày
                }
                function formatBillingRatio(ratio) {
                    if (ratio >= 0.999) {
                        return 100;
                    }
                    return Math.round(ratio * 10000) / 100; // Làm tròn đến 2 chữ số thập phân
                }
            // Bill calculation functionality
            function updateBill(roomId) {
                const form = document.querySelector(`form[data-room-id="${roomId}"]`);
                if (!form) return;

                const electricStartInput = form.querySelector('.electric-start');
                const electricEndInput = form.querySelector('.electric-end');
                const waterStartInput = form.querySelector('.water-start');
                const waterEndInput = form.querySelector('.water-end');
                const waterOccupantsInput = form.querySelector('.water-occupants');
                const additionalFeeRows = form.querySelectorAll('.additional-fee-row');

                // Get values
                const electricPriceInput = form.querySelector('input[name="data[electric_price]"]');
                const waterPriceInput = form.querySelector('input[name="data[water_price]"]');
                const waterUnitInput = form.querySelector('input[name="data[water_unit]"]');
                const billingRatioInput = form.querySelector('input[name="data[billing_ratio]"]');

                const electricStart = parseFloat(electricStartInput.value) || 0;
                const electricEnd = parseFloat(electricEndInput?.value) || 0;
                const electricPrice = parseFloat(electricPriceInput.value) || 3000;
                const waterPrice = parseFloat(waterPriceInput.value) || 20000;
                const waterUnit = waterUnitInput.value || 'per_m3';
                const billingRatio = parseFloat(billingRatioInput?.value) || 1.0;
                const rentPrice = parseFloat(form.querySelector('input[name="data[rent_price]"]').value) || 0;
                const serviceTotal = parseFloat(form.querySelector('.service-total')?.textContent.replace(/[^0-9]/g, '') || 0);
                const complaintCost = parseFloat(form.querySelector('.total-after-complaint')?.value || 0);

                console.log('rentPrice:', rentPrice);
                
                // Update price displays
                const electricPriceDisplay = form.querySelector('.electric-price-display');
                const waterPriceDisplay = form.querySelector('.water-price-display');
                if (electricPriceDisplay) electricPriceDisplay.value = new Intl.NumberFormat('vi-VN').format(electricPrice);
                if (waterPriceDisplay) waterPriceDisplay.value = new Intl.NumberFormat('vi-VN').format(waterPrice);

                let electricKwh = 0, electricTotal = 0, waterM3 = 0, waterTotal = 0, additionalFeesTotal = 0;
                let isValid = true;

                // Calculate electric
                if (electricEndInput && !electricEndInput.readOnly) {
                    electricEndInput.classList.remove('invalid-input');
                    form.querySelector('.electric-end-error').textContent = '';
                    if (electricEnd <= electricStart) {
                        electricEndInput.classList.add('invalid-input');
                        form.querySelector('.electric-end-error').textContent = 'Chỉ số cuối phải lớn hơn chỉ số đầu';
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
                    electricTotal = parseFloat(form.querySelector('input[name="data[electric_total]"]').value.toString().replace(/[^0-9]/g, '')) || 0;
                }

                // Calculate water
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
                            const originalWaterTotal = waterOccupants * waterPrice;
                            waterTotal = originalWaterTotal * billingRatio;
                            waterM3 = waterOccupants;
                            form.querySelector('.water-m3').value = waterM3.toFixed(2);
                            form.querySelector('.water-total').value = new Intl.NumberFormat('vi-VN').format(waterTotal);
                            form.querySelector('input[name="data[water_m3]"]').value = waterM3;
                            form.querySelector('input[name="data[water_total]"]').value = waterTotal;
                            form.querySelector('input[name="data[water_occupants]"]').value = waterOccupants;
                        }
                    } else {
                        waterM3 = parseFloat(form.querySelector('.water-m3').value) || 0;
                        waterTotal = parseFloat(form.querySelector('input[name="data[water_total]"]').value.toString().replace(/[^0-9]/g, '')) || 0;
                    }
                } else {
                    const waterStart = parseFloat(waterStartInput?.value) || 0;
                    const waterEnd = parseFloat(waterEndInput?.value) || 0;
                    if (waterEndInput && !waterEndInput.readOnly) {
                        waterEndInput.classList.remove('invalid-input');
                        form.querySelector('.water-end-error').textContent = '';
                        if (waterEnd <= waterStart) {
                            waterEndInput.classList.add('invalid-input');
                            form.querySelector('.water-end-error').textContent = 'Chỉ số cuối phải lớn hơn chỉ số đầu';
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
                        waterTotal = parseFloat(form.querySelector('input[name="data[water_total]"]').value.toString().replace(/[^0-9]/g, '')) || 0;
                    }
                }

                // Calculate additional fees
                additionalFeesTotal = 0;
                additionalFeeRows.forEach((row, index) => {
                    const nameInput = row.querySelector('.additional-fee-name');
                    const priceInput = row.querySelector('.additional-fee-price');
                    const qtyInput = row.querySelector('.additional-fee-qty');
                    const originalTotalInput = row.querySelector('.additional-fee-original-total');
                    const totalInput = row.querySelector('.additional-fee-total');

                    // Clear previous errors
                    nameInput.classList.remove('invalid-input');
                    priceInput.classList.remove('invalid-input');
                    qtyInput.classList.remove('invalid-input');
                    row.querySelector('.additional-fee-name-error').textContent = '';
                    row.querySelector('.additional-fee-price-error').textContent = '';
                    row.querySelector('.additional-fee-qty-error').textContent = '';

                    const name = nameInput.value.trim();
                    const price = parseFloat(priceInput.value) || 0;
                    const qty = parseInt(qtyInput.value) || 1;

                    // Validation
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

                    const actualTotal = price * qty;
                    
                    if (originalTotalInput) {
                        originalTotalInput.value = originalTotal;
                    }
                    totalInput.value = actualTotal;
                    additionalFeesTotal += actualTotal;

                    // Update names for form submission
                    nameInput.name = `data[additional_fees][${index}][name]`;
                    priceInput.name = `data[additional_fees][${index}][price]`;
                    qtyInput.name = `data[additional_fees][${index}][qty]`;
                    totalInput.name = `data[additional_fees][${index}][total]`;
                });

                // Update totals
                form.querySelector('.additional-fees-total').textContent = new Intl.NumberFormat('vi-VN').format(additionalFeesTotal);
                form.querySelector('.additional-fees-total-input').value = additionalFeesTotal;

                // Calculate grand total
                const total = isValid 
                    ? (rentPrice + electricTotal + waterTotal + serviceTotal + complaintCost + additionalFeesTotal) 
                    : 0;
                    console.log(`Rent: ${rentPrice}, Electric: ${electricTotal}, Water: ${waterTotal}, Service: ${serviceTotal}, Complaint: ${complaintCost}, Additional Fees: ${additionalFeesTotal}`);
                    

                // // Chỉ cập nhật total khi không có bill hoặc khi đang chỉnh sửa
                // const existingTotal = parseFloat(form.querySelector('input[name="data[total]"]').getAttribute('data-original-total') || 0);
                // const finalTotal = existingTotal > 0 ? existingTotal : total;

                // form.querySelector('.total').value = isValid ? new Intl.NumberFormat('vi-VN').format(finalTotal) : 'Lỗi tính toán';
                // form.querySelector('input[name="data[total]"]').value = finalTotal;

                 form.querySelector('.total').value = isValid ? total : 'Lỗi tính toán';
                form.querySelector('input[name="data[total]"]').value = total;
                console.log(`Total for room ${roomId}:`, total);
                



                // Enable/disable submit button
                const submitButton = form.querySelector('.btn-success');
                if (submitButton) submitButton.disabled = !isValid;
            }

            // Add fee functionality
            $(document).on('click', '.add-fee', function() {
    const roomId = $(this).data('room-id');
    const container = $(`#additional-fees-${roomId}`);
    const index = container.find('.additional-fee-row').length;
    
    const row = $(`
        <div class="row additional-fee-row">
            <div class="col-md-4">
                <label class="form-label">Tên chi phí</label>
                <input type="text" class="form-control additional-fee-name" name="data[additional_fees][${index}][name]" placeholder="Nhập tên chi phí">
                <div class="error-message additional-fee-name-error"></div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Giá (VND)</label>
                <input type="number" class="form-control additional-fee-price" name="data[additional_fees][${index}][price]" placeholder="0" min="0">
                <div class="error-message additional-fee-price-error"></div>
            </div>
            <div class="col-md-1">
                <label class="form-label">SL</label>
                <input type="number" class="form-control additional-fee-qty" name="data[additional_fees][${index}][qty]" value="1" min="1">
                <div class="error-message additional-fee-qty-error"></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tổng tiền</label>
                <input type="text" class="form-control additional-fee-total" name="data[additional_fees][${index}][total]" value="0" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label"> </label>
                <button type="button" class="btn btn-danger btn-sm remove-fee">Xóa</button>
            </div>
        </div>
    `);
    
    container.append(row);

    // Attach event listeners to new inputs
    row.find('.additional-fee-name, .additional-fee-price, .additional-fee-qty').on('input', function() {
        updateBill(roomId);
    });

    updateBill(roomId);
});

            // Remove fee functionality
            $(document).on('click', '.remove-fee', function() {
                const row = $(this).closest('.additional-fee-row');
                const roomId = row.closest('form').data('room-id');
                row.remove();
                updateBill(roomId);
            });

            // Input event listeners
            $(document).on('input', '.electric-end, .water-end, .water-occupants, .additional-fee-name, .additional-fee-price, .additional-fee-qty', function() {
                const roomId = $(this).closest('form').data('room-id');
                updateBill(roomId);
            });

            // Photo preview functionality
            $(document).on('change', '.electric-photos, .water-photos', function(e) {
                const container = $(this).parent().find('.photo-container');
                container.empty();
                Array.from(e.target.files).forEach(file => {
                    const preview = $('<img class="img-preview">').attr('src', URL.createObjectURL(file));
                    container.append(preview);
                });
            });

            // Initialize calculations on page load
            $('.bill-form').each(function() {
                const roomId = $(this).data('room-id');
                updateBill(roomId);
            });
        });
    </script>
@endsection