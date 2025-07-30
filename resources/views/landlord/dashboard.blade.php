@extends('landlord.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Tổng quan bất động sản</h2>
            <p class="text-muted">Thông kê tổng hợp các tòa nhà bên đang quản lý. Mỗi biểu đồ và thông kê đều có thể lọc theo tháng, quý hoặc so sánh giữa các tòa nhà.</p>
        </div>
    </div>

    <!-- Thống kê tổng hợp -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span>Thống kê tổng hợp</span>
            <form class="row g-2 align-items-center filter-form" data-target="summary" style="--bs-gutter-x: 0.5rem;">
                <div class="col-auto">
                    <input type="month" class="form-control form-control-sm" name="summary_month" value="{{ request('summary_month') }}">
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="summary_quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="summary_year" value="{{ request('summary_year', now()->format('Y')) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light btn-sm">Lọc</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-primary summary-total-rooms">{{ $total_rooms }}</div>
                    <div class="text-muted">Tổng số Phòng</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-success summary-total-rented">{{ $total_rented }}</div>
                    <div class="text-muted">Đã thuê</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-warning summary-total-empty">{{ $total_empty }}</div>
                    <div class="text-muted">Phòng trống</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold summary-total-revenue">{{ number_format($total_revenue) }}</div>
                    <div class="text-muted">Doanh thu</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold summary-total-profit">{{ number_format($total_profit) }}</div>
                    <div class="text-muted">Lợi nhuận</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-danger summary-total-complaints">{{ $total_complaints }}</div>
                    <div class="text-muted">Khiếu nại</div>
                </div>
            </div>
        </div>
    </div>

    <!-- So sánh các tòa nhà -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <span>So sánh các tòa nhà</span>
            <form class="row g-2 align-items-center filter-form" data-target="compare" style="--bs-gutter-x: 0.5rem;">
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="compareA">
                        <option value="">Tòa nhà A</option>
                        @foreach ($propertyStats as $property)
                            <option value="{{ $property['name'] }}">{{ $property['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="compareB">
                        <option value="">Tòa nhà B</option>
                        @foreach ($propertyStats as $property)
                            <option value="{{ $property['name'] }}">{{ $property['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="month" class="form-control form-control-sm" name="compare_month" value="{{ request('compare_month', now()->format('Y-m')) }}">
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="compare_quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="compare_year" value="{{ request('compare_year', now()->format('Y')) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light btn-sm">So sánh</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div id="compare-results"></div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu & lợi nhuận -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white text-center">
            <span>Biểu đồ doanh thu & lợi nhuận các tòa nhà</span>
        </div>
        <div class="card-body">
            <form class="row g-2 align-items-center filter-form" data-target="revenue-chart" style="--bs-gutter-x: 0.5rem;">
                <div class="col-auto">
                    <input type="month" class="form-control form-control-sm" name="revenue_chart_month" value="{{ request('revenue_chart_month', now()->format('Y-m')) }}">
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="revenue_chart_quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="revenue_chart_year" value="{{ request('revenue_chart_year', now()->format('Y')) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light btn-sm">Lọc</button>
                </div>
            </form>
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>

   

   <!-- Biểu đồ tổng quan phòng + Biểu đồ khiếu nại (cùng một dòng) -->
<div class="row g-3">
    <!-- Biểu đồ tổng quan phòng (Pie Chart) -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4 shadow-sm h-100">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span>Biểu đồ tổng quan phòng</span>
                <form class="row g-2 align-items-center filter-form" data-target="room-overview-chart" style="--bs-gutter-x: 0.5rem;">
                    <div class="col-auto">
                        <input type="month" class="form-control form-control-sm" name="room_overview_month" value="{{ request('room_overview_month') }}">
                    </div>
                    <div class="col-auto">
                        <select class="form-select form-select-sm" name="room_overview_quarter">
                            <option value="">-- Quý --</option>
                            <option value="1">Quý 1</option>
                            <option value="2">Quý 2</option>
                            <option value="3">Quý 3</option>
                            <option value="4">Quý 4</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="room_overview_year" value="{{ request('room_overview_year', now()->format('Y')) }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-light btn-sm">Lọc</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <canvas id="roomOverviewChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Biểu đồ khiếu nại -->
    <div class="col-12 col-lg-6">
        <div class="card mb-4 shadow-sm h-100">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span>Biểu đồ khiếu nại</span>
                <form class="row g-2 align-items-center filter-form" data-target="complaint-chart" style="--bs-gutter-x: 0.5rem;">
                    <div class="col-auto">
                        <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="complain_chart_year" value="{{ request('complain_chart_year', now()->format('Y')) }}">
                    </div>
                    <div class="col-auto">
                        <input type="month" class="form-control form-control-sm" name="complain_chart_month" value="{{ request('complain_chart_month', now()->format('Y-m')) }}">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-light btn-sm">Lọc</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <canvas id="roomChart"></canvas>
            </div>
        </div>
    </div>
</div>


    <!-- Biểu đồ tỉ lệ lấp đầy -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <span>Biểu đồ tỉ lệ lấp đầy các tòa nhà</span>
            <form class="row g-2 align-items-center filter-form" data-target="occupancy-chart" style="--bs-gutter-x: 0.5rem;">
                <div class="col-auto">
                    <input type="month" class="form-control form-control-sm" name="occupancy_chart_month" value="{{ request('occupancy_chart_month', now()->format('Y-m')) }}">
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" name="occupancy_chart_quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="number" min="2000" max="2100" class="form-control form-control-sm" name="occupancy_chart_year" value="{{ request('occupancy_chart_year', now()->format('Y')) }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light btn-sm">Lọc</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let incomeExpenseChart, occupancyChart, roomChart, roomOverviewChart;

        // Khởi tạo biểu đồ doanh thu & chi phí
        function initIncomeExpenseChart(labels, incomeData, expenseData) {
            const ctxIncomeExpense = document.getElementById('incomeExpenseChart').getContext('2d');
            if (incomeExpenseChart) incomeExpenseChart.destroy();
            incomeExpenseChart = new Chart(ctxIncomeExpense, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Doanh thu',
                            data: incomeData,
                            backgroundColor: '#0d6efd'
                        },
                        {
                            label: 'Tổng chi phí',
                            data: expenseData,
                            backgroundColor: '#fd7e14'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.formattedValue;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ tỉ lệ lấp đầy
        function initOccupancyChart(labels, data) {
            const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
            if (occupancyChart) occupancyChart.destroy();
            occupancyChart = new Chart(ctxOccupancy, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Tỉ lệ lấp đầy',
                        data: data,
                        backgroundColor: '#ffca07'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.formattedValue + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ khiếu nại
        function initRoomChart(labels, data) {
            const ctxRoom = document.getElementById('roomChart').getContext('2d');
            if (roomChart) roomChart.destroy();
            roomChart = new Chart(ctxRoom, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Khiếu nại',
                        data: data,
                        backgroundColor: '#dc3545'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.formattedValue;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ tròn tổng quan phòng
        function initRoomOverviewChart(rented, empty) {
            const ctxRoomOverview = document.getElementById('roomOverviewChart').getContext('2d');
            if (roomOverviewChart) roomOverviewChart.destroy();
            roomOverviewChart = new Chart(ctxRoomOverview, {
                type: 'pie',
                data: {
                    labels: ['Phòng đã thuê', 'Phòng trống'],
                    datasets: [{
                        data: [rented, empty],
                        backgroundColor: ['#28a745', '#ffc107']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.formattedValue;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Xử lý sự kiện lọc
        $('.filter-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const target = form.data('target');
            const data = {
                month: form.find('input[name$="_month"]').val(),
                quarter: form.find('select[name$="_quarter"]').val(),
                year: form.find('input[name$="_year"]').val(),
                compareA: form.find('select[name="compareA"]').val(),
                compareB: form.find('select[name="compareB"]').val()
            };

            $.ajax({
                url: '{{ route("landlords.filter-stats") }}',
                method: 'GET',
                data: data,
                success: function(response) {
                    if (target === 'summary') {
                        $('.summary-total-rooms').text(response.summary.total_rooms || 0);
                        $('.summary-total-rented').text(response.summary.total_rented || 0);
                        $('.summary-total-empty').text(response.summary.total_empty || 0);
                        $('.summary-total-revenue').text(new Intl.NumberFormat().format(response.summary.total_revenue || 0));
                        $('.summary-total-profit').text(new Intl.NumberFormat().format(response.summary.total_profit || 0));
                        $('.summary-total-complaints').text(response.summary.total_complaints || 0);
                    } else if (target === 'revenue-chart') {
                        const labels = response.propertyStats.map(stat => stat.name);
                        const incomeData = response.propertyStats.map(stat => stat.revenue || 0);
                        const expenseData = response.propertyStats.map(stat => (stat.electric_cost || 0) + (stat.water_cost || 0) + (stat.other_cost || 0));
                        initIncomeExpenseChart(labels, incomeData, expenseData);
                    } else if (target === 'occupancy-chart') {
                        const labels = response.propertyStats.map(stat => stat.name);
                        const occupancyData = response.propertyStats.map(stat => {
                            return stat.total_rooms > 0 ? (stat.rented_rooms / stat.total_rooms * 100).toFixed(2) : 0;
                        });
                        initOccupancyChart(labels, occupancyData);
                    } else if (target === 'complaint-chart') {
                        const labels = response.propertyStats.map(stat => stat.name);
                        const complaintData = response.propertyStats.map(stat => stat.complaints || 0);
                        initRoomChart(labels, complaintData);
                    } else if (target === 'compare') {
                         const leftName  = data.compareA;  // cột trái
  const rightName = data.compareB;  // cột phải

  // Map theo tên
  const statsByName = {};
  response.propertyStats.forEach(s => { statsByName[s.name] = s; });

  const left  = statsByName[leftName]  || {};
  const right = statsByName[rightName] || {};

  // Danh sách chỉ số + quy tắc lợi thế
  const metrics = [
    { key: 'total_rooms',   label: 'Tổng phòng',         mode: 'neutral' }, // trung tính
    { key: 'rented_rooms',  label: 'Đã thuê',             mode: 'higher'  }, // lớn hơn có lợi
    { key: 'empty_rooms',   label: 'Phòng trống',         mode: 'lower'   }, // nhỏ hơn có lợi
    { key: 'revenue',       label: 'Doanh thu',           mode: 'higher', money: true },
    { key: 'profit',        label: 'Lợi nhuận',           mode: 'higher', money: true },
    { key: 'electric_cost', label: 'Tiền điện',           mode: 'lower',  money: true },
    { key: 'water_cost',    label: 'Tiền nước',           mode: 'lower',  money: true },
    { key: 'other_cost',    label: 'Chi phí phát sinh',   mode: 'lower',  money: true },
    { key: 'complaints',    label: 'Khiếu nại',           mode: 'lower'   },
  ];

  const fmt   = n => new Intl.NumberFormat('vi-VN').format(Number(n) || 0);
  const money = n => fmt(n) + ' đ';

  // So sánh “có lợi hơn” theo mode
  function result(a, b, mode) {
    if (mode === 'neutral') return 0;
    a = Number(a) || 0; b = Number(b) || 0;
    if (mode === 'higher') return a > b ? -1 : (a < b ? 1 : 0); // -1: trái có lợi, 1: phải có lợi
    if (mode === 'lower')  return a < b ? -1 : (a > b ? 1 : 0);
    return 0;
  }

  function cell(value, side, r, isMoney) {
    // side L/R; r = -1 trái lợi, 1 phải lợi, 0 trung tính
    const base = side === 'L' ? 'text-end' : 'text-start';
    let cls = 'cmp-neutral';
    if (r === -1) cls = (side === 'L') ? 'cmp-better' : 'cmp-worse';
    if (r ===  1) cls = (side === 'R') ? 'cmp-better' : 'cmp-worse';
    const text = isMoney ? money(value) : fmt(value);
    return `<td class="${base} ${cls}"><span>${text}</span></td>`;
  }

  const rows = metrics.map(m => {
    const a = left[m.key]  ?? 0;
    const b = right[m.key] ?? 0;
    const r = result(a, b, m.mode);
    return `
      <tr>
        ${cell(a, 'L', r, !!m.money)}
        <th class="text-center align-middle cmp-label">${m.label}</th>
        ${cell(b, 'R', r, !!m.money)}
      </tr>
    `;
  }).join('');

  const html = `
    <style>
      /* Màu lợi thế/không lợi/trung tính */
      #compare-results .cmp-better  { background: rgba(25,135,84,.12);  color:#198754; font-weight:600; } /* có lợi hơn */
      #compare-results .cmp-worse   { background: rgba(220,53,69,.12);  color:#dc3545; font-weight:500; } /* không có lợi */
      #compare-results .cmp-neutral { background: rgba(108,117,125,.08); color:#6c757d; }
      #compare-results .cmp-label   { background:#f8f9fa; white-space:nowrap; }
      #compare-results table { border-collapse: separate; border-spacing: 0 6px; }
      #compare-results td, #compare-results th { border:1px solid #dee2e6; }
    </style>

    <!-- Badge tên tòa (nếu không muốn hiện, xoá 2 dòng <span> dưới) -->
    <div class="d-flex justify-content-between mb-2">
      <span class="badge bg-secondary-subtle text-secondary">${leftName}</span>
      <span class="badge bg-secondary-subtle text-secondary">${rightName}</span>
    </div>

    <div class="table-responsive">
      <table class="table align-middle text-nowrap">
        <tbody>
          ${rows}
        </tbody>
      </table>
    </div>
  `;

  $('#compare-results').html(html);
                    } else if (target === 'room-overview-chart') {
                        initRoomOverviewChart(response.summary.total_rented || 0, response.summary.total_empty || 0);
                    }
                },
                error: function(xhr) {
                    alert('Có lỗi xảy ra khi lọc dữ liệu: ' + xhr.responseText);
                }
            });
        });

        // Khởi tạo biểu đồ ban đầu
        const initialLabels = @json($propertyStats->pluck('name')->toArray());
        const initialIncomeData = @json($propertyStats->map(function($p) {
            return $p['revenue'] || 0;
        })->toArray());
        const initialExpenseData = @json($propertyStats->map(function($p) {
            return ($p['electric_cost'] || 0) + ($p['water_cost'] || 0) + ($p['other_cost'] || 0);
        })->toArray());
       const initialOccupancyData = @json($propertyStats->map(function($p) {
    return $p['total_rooms'] > 0
        ? round($p['rented_rooms'] / $p['total_rooms'] * 100, 2)  // ✅ PHP: làm tròn 2 số lẻ
        : 0;
})->toArray());
        const initialComplaintData = @json($propertyStats->map(function($p) {
            return $p['complaints'] || 0;
        })->toArray());
        const initialRentedRooms = @json($total_rented);
        const initialEmptyRooms = @json($total_empty);

        if (initialLabels.length > 0) {
            initIncomeExpenseChart(initialLabels, initialIncomeData, initialExpenseData);
            initOccupancyChart(initialLabels, initialOccupancyData);
            initRoomChart(initialLabels, initialComplaintData);
            initRoomOverviewChart(initialRentedRooms, initialEmptyRooms);
        }
        // --- TỰ ĐỘNG TẢI DỮ LIỆU MẶC ĐỊNH KHI MỞ TRANG ---
(function autoLoadDefaultSections() {
  // Helper: YYYY-MM theo chuẩn để gửi/đặt vào <input type="month">
  const now = new Date();
  const ym  = now.toLocaleDateString('sv-SE', { year: 'numeric', month: '2-digit' }); // "YYYY-MM"
  const yyyy = String(now.getFullYear());

  // 1) Doanh thu & lợi nhuận (revenue-chart) — quan trọng nhất theo yêu cầu của bạn
  const $rev = $('.filter-form[data-target="revenue-chart"]');
  if ($rev.length) {
    const $m = $rev.find('input[name="revenue_chart_month"]');
    const $y = $rev.find('input[name="revenue_chart_year"]');
    if (!$m.val()) $m.val(ym);
    if (!$y.val()) $y.val(yyyy);
    // Tự submit để gọi AJAX fill chart
    if (typeof $rev[0].requestSubmit === 'function') $rev[0].requestSubmit();
    else $rev.trigger('submit');
  }

  // (Tuỳ chọn) Auto-load các phần khác nếu bạn muốn có dữ liệu ngay khi mở trang:
  const targets = ['summary','occupancy-chart','complaint-chart','room-overview-chart'];
  targets.forEach(t => {
    const $f = $(`.filter-form[data-target="${t}"]`);
    if (!$f.length) return;

    // Gán mặc định cho input nếu để trống
    const $m = $f.find('input[name$="_month"]');
    const $y = $f.find('input[name$="_year"]');
    if ($m.length && !$m.val()) $m.val(ym);
    if ($y.length && !$y.val()) $y.val(yyyy);

    if (typeof $f[0].requestSubmit === 'function') $f[0].requestSubmit();
    else $f.trigger('submit');
  });
})();

    });
</script>
@endpush