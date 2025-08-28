@extends('home.layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <h2 class="text-3xl font-bold mb-6">📊 Dashboard người thuê phòng</h2>

    {{-- Bộ lọc thời gian --}}
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
            <input type="month" id="periodInput" name="period" value="{{ request('type') === 'month' ? request('period') : '' }}" class="form-control" autocomplete="off">
            <input type="number" id="yearInput" name="period" value="{{ request('type') === 'year' ? request('period') : '' }}" min="2000" max="{{ date('Y')+10 }}" class="form-control" style="display:none;">
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
                            <li><span class="fw-bold">{{ ucfirst($service) }}:</span> <span class="float-end">{{ number_format($amount) }} đ</span></li>
                        @empty
                            <li>Không có dữ liệu</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ chi tiết --}}
    <div class="card mb-5 shadow">
        <div class="card-body">
<h5 class="card-title">📊 Biểu đồ chi tiết dịch vụ</h5>
            <canvas id="detailChart" height="100"></canvas>
        </div>
    </div>

    {{-- So sánh mốc thời gian --}}
    @isset($bills1, $bills2)
    <div class="card shadow mb-5">
        <div class="card-body">
            <h5 class="card-title">📊 So sánh giữa 2 mốc thời gian</h5>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-center mb-3">{{ $label1 }}</h6>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Số tiền (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bills1 as $service => $amount)
                                <tr><td>{{ ucfirst($service) }}</td><td class="text-end">{{ number_format($amount) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-center">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-center mb-3">{{ $label2 }}</h6>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">Số tiền (đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bills2 as $service => $amount)
                                <tr><td>{{ ucfirst($service) }}</td><td class="text-end">{{ number_format($amount) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-center">Không có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <canvas id="compareChart" height="100"></canvas>
            </div>
        </div>
    </div>
    @endisset

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function changePeriodInput() {
        const type = document.getElementById('typeSelect').value;
        const periodInput = document.getElementById('periodInput');
        const yearInput = document.getElementById('yearInput');
        if(type==='month'){ periodInput.style.display='block'; yearInput.style.display='none'; }
        else{ periodInput.style.display='none'; yearInput.style.display='block'; }
    }

    // Biểu đồ chi phí chi tiết
const ctxDetail = document.getElementById('detailChart').getContext('2d');
    new Chart(ctxDetail, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($serviceTotals->toArray())) !!},
            datasets: [{ label: 'Chi phí theo dịch vụ', data: {!! json_encode(array_values($serviceTotals->toArray())) !!}, backgroundColor: 'rgba(54, 162, 235, 0.7)' }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    @isset($bills1, $bills2)
        const compareLabels = {!! json_encode(array_unique(array_merge(array_keys($bills1->toArray()), array_keys($bills2->toArray())))) !!};
        new Chart(document.getElementById('compareChart'), {
            type: 'bar',
            data: {
                labels: compareLabels,
                datasets: [
                    { label: '{{ $label1 }}', data: compareLabels.map(l => {{ Js::from($bills1) }}[l] ?? 0), backgroundColor: 'rgba(54, 162, 235, 0.6)' },
                    { label: '{{ $label2 }}', data: compareLabels.map(l => {{ Js::from($bills2) }}[l] ?? 0), backgroundColor: 'rgba(255, 99, 132, 0.6)' }
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    @endisset
</script>
@endsection