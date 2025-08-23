@extends('landlord.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user(); 
@endphp
<style>
    /* Áp dụng cho tất cả biểu đồ */
    .chart-container {
        height: 350px; /* chỉnh chiều cao mong muốn */
    }
    .chart-container canvas {
        max-height: 100%;
    }
    
    /* Custom styles for better visual */
    .stats-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s ease-in-out;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .chart-card {
        transition: box-shadow 0.3s ease;
    }
    
    .chart-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
</style>

<div class="container-fluid">
    @if($user->role === 'Staff')
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="fw-bold">Chào mừng, {{ $user->name }}!</h1>
                <p class="text-muted">Quản lý các tòa nhà và theo dõi thống kê một cách hiệu quả.</p>
            </div>
        </div>
        
    @else
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">📊 Tổng quan bất động sản</h2>
            <p class="text-muted">Thống kê tổng hợp các tòa nhà bạn đang quản lý. Lọc dữ liệu theo tháng, quý, năm hoặc khoảng năm để xem chi tiết.</p>
        </div>
    </div>
    
    <!-- Bộ lọc chung -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5>🔍 Bộ lọc thống kê</h5>
        </div>
        <div class="card-body">
            <form id="propertyFilterForm" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-3">
                    <label for="properties">Chọn tòa nhà</label>
                    <select class="form-select select2" name="properties[]" multiple>
                        @foreach ($properties as $property)
                            <option value="{{ $property->name }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="month">Tháng</label>
                    <input type="month" class="form-control" name="month" value="{{ now()->format('Y-m') }}">
                </div>
                <div class="col-md-1">
                    <label for="quarter">Quý</label>
                    <select class="form-select" name="quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Q1</option>
                        <option value="2">Q2</option>
                        <option value="3">Q3</option>
                        <option value="4">Q4</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="year">Năm</label>
                    <input type="number" min="2000" max="2100" class="form-control" name="year" value="{{ now()->format('Y') }}">
                </div>
                <div class="col-md-2">
                    <label for="year_range">Khoảng năm</label>
                    <div class="d-flex">
                        <input type="number" min="2000" max="2100" class="form-control me-1" name="year_from" placeholder="Từ năm">
                        <input type="number" min="2000" max="2100" class="form-control" name="year_to" placeholder="Đến năm">
                    </div>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" id="resetFilter" class="btn btn-secondary w-100">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê tổng hợp -->
    <div class="card mb-4 shadow-sm stats-card">
        <div class="card-header bg-primary text-white">
            <span>📈 Thống kê tổng hợp</span>
        </div>
        <div class="card-body">
            <div id="summaryMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
            <div class="row text-center">
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-primary summary-total-rooms">{{ $total_rooms }}</div>
                    <div class="text-muted">🏠 Tổng số Phòng</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-success summary-total-rented">{{ $total_rented }}</div>
                    <div class="text-muted">✅ Đã thuê</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-warning summary-total-empty">{{ $total_empty }}</div>
                    <div class="text-muted">🏷️ Phòng trống</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-danger summary-total-complaints">{{ $total_complaints }}</div>
                    <div class="text-muted">⚠️ Khiếu nại</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold summary-total-revenue">{{ number_format($total_revenue) }}</div>
                    <div class="text-muted">💰 Doanh thu</div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="fs-2 fw-bold text-info summary-total-bookings">0</div>
                    <div class="text-muted">📅 Lịch hẹn</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu và Tổng quan phòng -->
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card mb-4 shadow-sm chart-card h-100">
                <div class="card-header bg-dark text-white">
                    <span>📊 Biểu đồ doanh thu theo tháng</span>
                </div>
                <div class="card-body chart-container">
                    <canvas id="tiktokRevenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card mb-4 shadow-sm chart-card h-100">
                <div class="card-header bg-success text-white">
                    <span>🏠 Tổng quan phòng</span>
                </div>
                <div class="card-body chart-container">
                    <div id="roomOverviewChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
                    <canvas id="roomOverviewChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ cột nhóm Thu Chi theo tháng -->
    <div class="card mb-4 shadow-sm chart-card">
        <div class="card-header bg-info text-white">
            <span>💹 Biểu đồ Thu - Chi theo tháng</span>
        </div>
        <div class="card-body chart-container">
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>

    <!-- Khiếu nại và Bookings -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card mb-4 shadow-sm chart-card h-100">
                <div class="card-header bg-warning text-dark">
                    <span>⚠️ Trạng thái khiếu nại</span>
                </div>
                <div class="card-body chart-container">
                    <div id="complaintsChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
                    <canvas id="complaintsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card mb-4 shadow-sm chart-card h-100">
                <div class="card-header bg-purple text-white" style="background-color: #6f42c1;">
                    <span>📅 Trạng thái lịch hẹn xem phòng</span>
                </div>
                <div class="card-body chart-container">
                    <div id="bookingsChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tỉ lệ lấp đầy theo tháng -->
    <div class="card mb-4 shadow-sm chart-card">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(45deg, #ff6b6b, #4ecdc4);">
            <span>📈 Biểu đồ tỉ lệ lấp đầy theo tháng</span>
        </div>
        <div class="card-body chart-container">
            <div id="occupancyChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let tiktokRevenueChart, incomeExpenseChart, occupancyChart, roomOverviewChart, complaintsChart, bookingsChart;

    // Biểu đồ doanh thu
    function initTiktokRevenueChart(data) {
        const ctx = document.getElementById('tiktokRevenueChart').getContext('2d');

        const labels = (data && data.labels) ? data.labels : [];
        const revenue = (data && data.revenue) ? data.revenue.map(v => Number(v) || 0) : [];

        if (tiktokRevenueChart) {
            tiktokRevenueChart.destroy();
        }

        let maxVal = 0, minVal = 0;
        if (revenue.length > 0) {
            maxVal = Math.max(...revenue);
            minVal = Math.min(...revenue);
        }

        tiktokRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: revenue,
                    fill: true,
                    backgroundColor: 'rgba(76, 192, 192, 0.1)',
                    borderColor: '#4bc0c0',
                    tension: 0.4,
                    pointBackgroundColor: revenue.map((v) => {
                        if (v === maxVal) return '#ff6b6b';
                        if (v === minVal) return '#4dabf7';
                        return '#4bc0c0';
                    }),
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `💰 Doanh thu: ${new Intl.NumberFormat('vi-VN').format(context.parsed.y)} VND`;
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ thu chi theo tháng
    function initIncomeExpenseChart(labels, incomeData, expenseData) {
        const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        if (incomeExpenseChart) incomeExpenseChart.destroy();

        incomeExpenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '💰 Thu nhập',
                        data: incomeData,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    },
                    {
                        label: '💸 Chi phí',
                        data: expenseData,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: '#dc3545',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${new Intl.NumberFormat('vi-VN').format(context.parsed.y)} VND`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ tỉ lệ lấp đầy theo tháng
    function initOccupancyChart(labels, data) {
        const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
        if (occupancyChart) occupancyChart.destroy();
        
        occupancyChart = new Chart(ctxOccupancy, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tỉ lệ lấp đầy (%)',
                    data: data,
                    backgroundColor: 'rgba(255, 202, 7, 0.2)',
                    borderColor: '#ffca07',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#ffca07',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `📊 Tỉ lệ lấp đầy: ${context.raw}%`;
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

    // Biểu đồ tròn tổng quan phòng với tỉ lệ chi tiết
    function initRoomOverviewChart(rented, empty) {
        const ctxRoomOverview = document.getElementById('roomOverviewChart').getContext('2d');
        if (roomOverviewChart) roomOverviewChart.destroy();
        
        const total = rented + empty;
        
        roomOverviewChart = new Chart(ctxRoomOverview, {
            type: 'doughnut',
            data: {
                labels: ['Phòng đã thuê', 'Phòng trống'],
                datasets: [{
                    data: [rented, empty],
                    backgroundColor: ['#28a745', '#ffc107'],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 3,
                    hoverBackgroundColor: ['#34ce57', '#ffcd39'],
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: { 
                        display: true, 
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} phòng (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ khiếu nại theo trạng thái
    function initComplaintsChart(complaintsData) {
        const ctx = document.getElementById('complaintsChart').getContext('2d');
        if (complaintsChart) complaintsChart.destroy();

        // Dữ liệu mẫu nếu không có dữ liệu từ server
        const defaultData = {
            pending: 5,
            in_progress: 3,
            resolved: 12,
            reject: 2
        };

        const data = complaintsData || defaultData;
        const labels = ['Đang chờ', 'Đang xử lý', 'Đã giải quyết', 'Từ chối'];
        const values = [data.pending || 0, data.in_progress || 0, data.resolved || 0, data.reject || 0];
        const colors = ['#ffc107', '#17a2b8', '#28a745', '#dc3545'];

        complaintsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '40%',
                plugins: {
                    legend: { 
                        display: true, 
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = values.reduce((sum, val) => sum + val, 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} khiếu nại (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ bookings theo trạng thái
    function initBookingsChart(bookingsData, propertyDetails) {
        const ctx = document.getElementById('bookingsChart').getContext('2d');
        if (bookingsChart) bookingsChart.destroy();

        // Dữ liệu mẫu
        const defaultData = {
            pending: 8,
            approved: 15,
            rejected: 3,
            waiting: 6
        };

        const data = bookingsData || defaultData;
        const labels = ['Đang chờ', 'Đã duyệt', 'Từ chối', 'Đang đợi'];
        const values = [data.pending || 0, data.approved || 0, data.rejected || 0, data.waiting || 0];
        const colors = ['#ffc107', '#28a745', '#dc3545', '#6c757d'];

        bookingsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Số lượng lịch hẹn',
                    data: values,
                    backgroundColor: colors.map(color => color + '80'), // Thêm độ trong suốt
                    borderColor: colors,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                // Thông tin chi tiết về tòa nhà có lịch hẹn
                                const buildingInfo = propertyDetails ? 
                                    `\nTòa nhà có lịch: ${propertyDetails.join(', ')}` : 
                                    '\nTòa nhà có lịch: A1, B2, C3';
                                return `${context.dataset.label}: ${context.raw}${buildingInfo}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#propertyFilterForm')[0].reset();
        $('.select2').val(null).trigger('change');
        $('input[name="month"]').val('{{ now()->format("Y-m") }}');
        $('input[name="year"]').val('{{ now()->format("Y") }}');
        $('#propertyFilterForm').trigger('submit');
    });

    // Xử lý form lọc
    $('#propertyFilterForm').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize();
        
        $.ajax({
            url: '{{ route("landlords.filter-stats") }}',
            method: 'GET',
            data: data,
            success: function(response) {
                console.log('Response:', response);
                
                // Ẩn tất cả thông báo lỗi
                $('#summaryMessage, #occupancyChartMessage, #roomOverviewChartMessage, #complaintsChartMessage, #bookingsChartMessage').addClass('d-none');

                // Thống kê tổng hợp
                if (response.summary) {
                    $('.summary-total-rooms').text(response.summary.total_rooms || 0);
                    $('.summary-total-rented').text(response.summary.total_rented || 0);
                    $('.summary-total-empty').text(response.summary.total_empty || 0);
                    $('.summary-total-revenue').text(new Intl.NumberFormat('vi-VN').format(response.summary.total_revenue || 0));
                    $('.summary-total-complaints').text(response.summary.total_complaints || 0);
                    $('.summary-total-bookings').text(response.summary.total_bookings || 0); // Giả định
                } else {
                    $('#summaryMessage').removeClass('d-none');
                }

                // Cập nhật các biểu đồ
                initTiktokRevenueChart(response.revenueChartData || { labels: [], revenue: [] });

                const incomeExpenseStats = response.incomeExpenseStats || { labels: [], income: [], expense: [] };
                initIncomeExpenseChart(incomeExpenseStats.labels, incomeExpenseStats.income, incomeExpenseStats.expense);

                const occupancyChartData = response.occupancyChartData || { labels: [], occupancy: [] };
                initOccupancyChart(occupancyChartData.labels, occupancyChartData.occupancy);

                initRoomOverviewChart(response.summary.total_rented || 0, response.summary.total_empty || 0);
                
                // Khiếu nại và bookings với dữ liệu thực từ server
                initComplaintsChart(response.complaintsStats);
                initBookingsChart(response.bookingsStats, response.propertiesWithBookings || []);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Có lỗi xảy ra khi tải dữ liệu: ' + error);
            }
        });
    });

    // Khởi tạo Select2
    $('.select2').select2({
        placeholder: 'Chọn tối đa 10 tòa nhà',
        maximumSelectionLength: 10,
        allowClear: true
    });

    // Tải dữ liệu ban đầu
    const initialPropertyStats = @json($propertyStats->toArray());
    
    if (initialPropertyStats.length >= 0) {
        initTiktokRevenueChart(@json($revenueChartData));
        
        initIncomeExpenseChart(
            @json($incomeExpenseStats['labels']),
            @json($incomeExpenseStats['income']),
            @json($incomeExpenseStats['expense'])
        );

        initOccupancyChart(
            @json($occupancyChartData['labels']),
            @json($occupancyChartData['occupancy'])
        );

        initRoomOverviewChart(@json($total_rented), @json($total_empty));
        initComplaintsChart(@json($complaintsStats ?? null));
        initBookingsChart(@json($bookingsStats ?? null), @json($propertyStats->where('bookings', '>', 0)->pluck('name')->toArray()));
    }

    // Tự động submit form khi tải trang
    setTimeout(function() {
        $('#propertyFilterForm').trigger('submit');
    }, 500);
});
</script>
@endpush