@extends('home.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <h2 class="text-3xl font-bold mb-6">📊 Dashboard người thuê phòng</h2>

    {{-- Bộ lọc --}}
    <form method="GET" action="{{ route('home.profile.tenants.dashboard') }}" class="row g-3 mb-5">
        <div class="col-md-3">
            <label class="form-label">Kiểu thời gian</label>
            <select name="type" id="typeSelect" class="form-select" onchange="changePeriodInput()">
                <option value="month" {{ request('type') === 'month' ? 'selected' : '' }}>Tháng</option>
                <option value="year" {{ request('type') === 'year' ? 'selected' : '' }}>Năm</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Mốc thời gian</label>
            <input
                type="month"
                id="periodInput"
                name="period"
                value="{{ request('type') === 'month' ? request('period') : '' }}"
                class="form-control"
                placeholder="Chọn tháng"
                autocomplete="off"
            >
            <input
                type="number"
                id="yearInput"
                name="period"
                value="{{ request('type') === 'year' ? request('period') : '' }}"
                min="2000"
                max="{{ date('Y') + 10 }}"
                class="form-control"
                placeholder="Chọn năm"
                autocomplete="off"
                style="display:none;"
            >
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
        </div>
    </form>

    {{-- Thống kê nhanh --}}
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card bg-info text-white mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">💰 Tổng chi phí</h5>
                    <p class="card-text fs-4">{{ number_format($totalCost) }} đ</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">🛠 Tổng khiếu nại</h5>
                    <p class="card-text fs-4">{{ $complaintCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title">📦 Tổng dịch vụ</h5>
                    <ul class="list-unstyled mb-0">
                        @forelse($serviceTotals as $service => $amount)
                            <li>
                                <span class="fw-bold">{{ ucfirst($service) }}:</span>
                                <span class="float-end">{{ number_format($amount) }} đ</span>
                            </li>
                        @empty
                            <li>Không có dữ liệu</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ chi phí chính --}}
    {{-- <pre>{{ dd($serviceTotals) }}</pre> --}}
    <div class="card shadow mb-5">
        <div class="card-body">
            <h5 class="card-title">📈 Biểu đồ chi phí</h5>
            <div>
                <canvas id="costChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Biểu đồ so sánh nếu có --}}
<div class="card shadow">
    <div class="card-body">
        <h5 class="card-title">📊 So sánh giữa 2 mốc thời gian</h5>

        <form method="GET" action="{{ route('home.profile.tenants.dashboard') }}" class="row g-3 mb-5">
           
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="compare" id="compareCheckbox" value="1" {{ request('compare') ? 'checked' : '' }}>
                    <label class="form-check-label" for="compareCheckbox">So sánh 2 mốc thời gian</label>
                </div>
            </div>
            <div class="col-md-3" id="comparePeriods" style="{{ request('compare') ? '' : 'display:none;' }}">
                <input type="month" name="period1" value="{{ request('period1') }}" class="form-control mb-2" placeholder="Chọn mốc 1">
                <input type="month" name="period2" value="{{ request('period2') }}" class="form-control" placeholder="Chọn mốc 2">
            </div>
            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Xem thống kê</button>
            </div>
        </form>

        @isset($bills1, $bills2)
            <div class="mt-4">
                <canvas id="compareChart" height="100"></canvas>
            </div>

            <div class="row mt-5">
                {{-- Bảng chi tiết mốc thời gian 1 --}}
                <div class="col-md-6">
                    <h6 class="text-center mb-3">Chi tiết {{ $label1 ?? 'Mốc thời gian 1' }}</h6>
                    <table class="table table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Số tiền (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bills1 as $service => $amount)
                                <tr>
                                    <td>{{ ucfirst($service) }}</td>
                                    <td class="text-end">{{ number_format($amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Bảng chi tiết mốc thời gian 2 --}}
                <div class="col-md-6">
                    <h6 class="text-center mb-3">Chi tiết {{ $label2 ?? 'Mốc thời gian 2' }}</h6>
                    <table class="table table-bordered table-striped">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Số tiền (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bills2 as $service => $amount)
                                <tr>
                                    <td>{{ ucfirst($service) }}</td>
                                    <td class="text-end">{{ number_format($amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">
                <h6 class="text-center mb-3">Bảng so sánh chênh lệch</h6>
                <table class="table table-bordered table-striped">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Dịch vụ</th>
                            <th>{{ $label1 ?? 'Mốc thời gian 1' }}</th>
                            <th>{{ $label2 ?? 'Mốc thời gian 2' }}</th>
                            <th>Chênh lệch (đ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allServices = collect(array_unique(array_merge(
                                $bills1->keys()->toArray(),
                                $bills2->keys()->toArray()
                            )))->sort()->values();
                        @endphp

                        @forelse($allServices as $service)
                            @php
                                $amount1 = $bills1->get($service, 0);
                                $amount2 = $bills2->get($service, 0);
                                $diff = $amount2 - $amount1;
                            @endphp
                            <tr class="{{ $diff > 0 ? 'table-success' : ($diff < 0 ? 'table-danger' : '') }}">
                                <td>{{ ucfirst($service) }}</td>
                                <td class="text-end">{{ number_format($amount1) }}</td>
                                <td class="text-end">{{ number_format($amount2) }}</td>
                                <td class="text-end">{{ number_format($diff) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Không có dữ liệu để so sánh</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endisset
    </div>
</div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function changePeriodInput() {
        const type = document.getElementById('typeSelect').value;
        const periodInput = document.getElementById('periodInput');
        const yearInput = document.getElementById('yearInput');

        if (type === 'month') {
            periodInput.style.display = 'block';
            periodInput.name = 'period';
            yearInput.style.display = 'none';
            yearInput.name = '';
            if (!/^\d{4}-\d{2}$/.test(periodInput.value)) {
                periodInput.value = '';
            }
        } else {
            yearInput.style.display = 'block';
            yearInput.name = 'period';
            periodInput.style.display = 'none';
            periodInput.name = '';
            if (!/^\d{4}$/.test(yearInput.value)) {
                yearInput.value = new Date().getFullYear();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        changePeriodInput();

        // Bật/tắt vùng chọn 2 mốc so sánh
        const compareCheckbox = document.getElementById('compareCheckbox');
        const comparePeriods = document.getElementById('comparePeriods');

        if (compareCheckbox && comparePeriods) {
            // Lúc đầu nếu checkbox được tick thì hiển thị, không thì ẩn
            comparePeriods.style.display = compareCheckbox.checked ? 'block' : 'none';

            compareCheckbox.addEventListener('change', () => {
                if (compareCheckbox.checked) {
                    comparePeriods.style.display = 'block';
                } else {
                    comparePeriods.style.display = 'none';
                }
            });
        }
    });

    // Biểu đồ chính
    const type = "{{ $type }}";
    const costCtx = document.getElementById('costChart').getContext('2d');

    @if($type === 'year')
        const months = {!! json_encode(array_keys($lineData->toArray())) !!};
        const totals = {!! json_encode(array_values($lineData->toArray())) !!};

        new Chart(costCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Tổng chi phí (VNĐ)',
                    data: totals,
                    fill: false,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Tổng chi phí theo từng tháng' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value.toLocaleString() + ' đ' }
                    }
                }
            }
        });
    @elseif($type === 'month')
        const services = {!! json_encode(array_keys($serviceTotals->toArray())) !!};
        const amounts = {!! json_encode(array_values($serviceTotals->toArray())) !!};

        new Chart(costCtx, {
            type: 'bar',
            data: {
                labels: services,
                datasets: [{
                    label: 'Tổng chi phí dịch vụ (VNĐ)',
                    data: amounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Tổng chi phí dịch vụ trong tháng' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value.toLocaleString() + ' đ' }
                    }
                }
            }
        });
    @endif

    @isset($bills1, $bills2)
        const compareLabels = {!! json_encode(array_unique(array_merge(array_keys($bills1->toArray()), array_keys($bills2->toArray())))) !!};

        new Chart(document.getElementById('compareChart'), {
            type: 'bar',
            data: {
                labels: compareLabels,
                datasets: [
                    {
                        label: '{{ $label1 ?? "" }}',
                        data: compareLabels.map(l => {{ Js::from($bills1 ?? collect()) }}[l] ?? 0),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    },
                    {
                        label: '{{ $label2 ?? "" }}',
                        data: compareLabels.map(l => {{ Js::from($bills2 ?? collect()) }}[l] ?? 0),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'So sánh dịch vụ giữa 2 mốc thời gian' }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    @endisset
</script>

@endsection
