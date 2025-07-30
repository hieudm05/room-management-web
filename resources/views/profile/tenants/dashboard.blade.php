@extends('profile.tenants.layouts.app')

{{-- Fake dữ liệu demo nếu chưa có --}}
@php
    // Dữ liệu từng tháng cho mỗi dịch vụ
    $monthsQ1 = [
        ['electric' => 300000, 'water' => 150000, 'wifi' => 100000, 'parking' => 40000],
        ['electric' => 320000, 'water' => 160000, 'wifi' => 100000, 'parking' => 40000],
        ['electric' => 310000, 'water' => 155000, 'wifi' => 100000, 'parking' => 40000],
    ];
    $monthsQ2 = [
        ['electric' => 350000, 'water' => 170000, 'wifi' => 100000, 'parking' => 50000],
        ['electric' => 360000, 'water' => 180000, 'wifi' => 100000, 'parking' => 50000],
        ['electric' => 340000, 'water' => 175000, 'wifi' => 100000, 'parking' => 50000],
    ];

    // Tổng từng dịch vụ của mỗi quý
    $billsQ1 = collect(['electric' => 0, 'water' => 0, 'wifi' => 0, 'parking' => 0]);
    foreach ($monthsQ1 as $m) {
        foreach ($billsQ1 as $k => $v) $billsQ1[$k] += $m[$k];
    }
    $billsQ2 = collect(['electric' => 0, 'water' => 0, 'wifi' => 0, 'parking' => 0]);
    foreach ($monthsQ2 as $m) {
        foreach ($billsQ2 as $k => $v) $billsQ2[$k] += $m[$k];
    }

    // Gán vào biến dùng cho view
    $bills1 = $billsQ1;
    $bills2 = $billsQ2;
    $label1 = 'Quý 1/2025';
    $label2 = 'Quý 2/2025';
@endphp

@section('content')
<div class="container-fluid px-4 py-6">
    <h2 class="text-3xl font-bold mb-6">📊 Dashboard người thuê phòng</h2>

    {{-- Bộ lọc theo thời gian --}}
    <form method="GET" action="{{ route('home.profile.tenants.dashboard') }}" class="row g-3 mb-5">
        <div class="col-md-3">
            <label class="form-label">Kiểu thời gian</label>
            <select name="type" class="form-select">
                <option value="month" {{ request('type') === 'month' ? 'selected' : '' }}>Tháng</option>
                <option value="quarter" {{ request('type') === 'quarter' ? 'selected' : '' }}>Quý</option>
                <option value="year" {{ request('type') === 'year' ? 'selected' : '' }}>Năm</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Mốc thời gian</label>
            <input type="text" name="period" value="{{ request('period') }}" required class="form-control" placeholder="VD: 2025 hoặc 2025-03 hoặc 2025-Q2">
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

    {{-- Biểu đồ chi tiết dịch vụ --}}
    <div class="card mb-5 shadow">
        <div class="card-body">
            <h5 class="card-title">📊 Biểu đồ chi tiết dịch vụ</h5>
            <canvas id="detailChart" height="100"></canvas>
        </div>
    </div>

    {{-- So sánh chi tiết --}}
    <div class="card shadow">
        <div class="card-body">
            <h5 class="card-title">📈 So sánh giữa 2 mốc thời gian</h5>
            <form method="GET" action="{{ route('home.profile.tenants.dashboard') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Kiểu so sánh</label>
                    <select name="compare_type" class="form-select">
                        <option value="month" {{ request('compare_type') === 'month' ? 'selected' : '' }}>Tháng</option>
                        <option value="quarter" {{ request('compare_type') === 'quarter' ? 'selected' : '' }}>Quý</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mốc 1</label>
                    <input type="text" name="period1" value="{{ request('period1') }}" class="form-control" placeholder="2025-05 hoặc 2025-Q2">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mốc 2</label>
                    <input type="text" name="period2" value="{{ request('period2') }}" class="form-control" placeholder="2025-06 hoặc 2025-Q3">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" type="submit">So sánh</button>
                </div>
            </form>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary">📅 {{ $label1 }}</h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Chi phí (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills1 as $service => $amount)
                                <tr>
                                    <td>{{ ucfirst($service) }}</td>
                                    <td class="text-end">{{ number_format($amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-danger">📅 {{ $label2 }}</h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Chi phí (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills2 as $service => $amount)
                                <tr>
                                    <td>{{ ucfirst($service) }}</td>
                                    <td class="text-end">{{ number_format($amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <canvas id="compareChart" height="100"></canvas>
            </div>

            <script>
                const compareLabels = {!! json_encode(array_unique(array_merge(array_keys($bills1->toArray()), array_keys($bills2->toArray()))) ) !!};
                const compareChart = new Chart(document.getElementById('compareChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: compareLabels,
                        datasets: [
                            {
                                label: '{{ $label1 }}',
                                data: compareLabels.map(l => {{ Js::from($bills1) }}[l] ?? 0),
                                backgroundColor: 'rgba(54, 162, 235, 0.6)'
                            },
                            {
                                label: '{{ $label2 }}',
                                data: compareLabels.map(l => {{ Js::from($bills2) }}[l] ?? 0),
                                backgroundColor: 'rgba(255, 99, 132, 0.6)'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Biểu đồ so sánh dịch vụ giữa 2 mốc thời gian'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'VNĐ'
                                }
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxDetail = document.getElementById('detailChart').getContext('2d');
    const chartDetail = new Chart(ctxDetail, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($serviceTotals->toArray())) !!},
            datasets: [{
                label: 'Chi phí theo dịch vụ',
                data: {!! json_encode(array_values($serviceTotals->toArray())) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Chi phí dịch vụ theo mốc thời gian chọn'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'VNĐ'
                    }
                }
            }
        }
    });
</script>
@endsection