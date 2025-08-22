@extends('profile.tenants.layouts.app')

{{-- Fake dữ liệu demo nếu chưa có --}}

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
