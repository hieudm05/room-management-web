@extends('landlord.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Tổng quan bất động sản</h2>
            <p class="text-muted">Thống kê tổng hợp các tòa nhà bạn đang quản lý. Lọc dữ liệu theo tháng, quý, năm hoặc chọn tòa nhà để xem chi tiết.</p>
        </div>
    </div>

    <!-- Bộ lọc chung -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5>Bộ lọc thống kê</h5>
        </div>
        <div class="card-body">
            <form id="propertyFilterForm" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-4">
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
                <div class="col-md-2">
                    <label for="quarter">Quý</label>
                    <select class="form-select" name="quarter">
                        <option value="">-- Quý --</option>
                        <option value="1">Quý 1</option>
                        <option value="2">Quý 2</option>
                        <option value="3">Quý 3</option>
                        <option value="4">Quý 4</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year">Năm</label>
                    <input type="number" min="2000" max="2100" class="form-control" name="year" value="{{ now()->format('Y') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê tổng hợp -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <span>Thống kê tổng hợp</span>
        </div>
        <div class="card-body">
            <div id="summaryMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
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

    <!-- Biểu đồ phân tán -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <span>Biểu đồ phân tán: Doanh thu vs Lợi nhuận</span>
        </div>
        <div class="card-body">
            <div id="scatterChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị. Vui lòng chọn ít nhất một tòa nhà.</div>
            <canvas id="scatterChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ cột nhóm -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <span>Biểu đồ cột: So sánh Doanh thu và Lợi nhuận</span>
        </div>
        <div class="card-body">
            <div id="groupedBarChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị. Vui lòng chọn ít nhất một tòa nhà.</div>
            <canvas id="groupedBarChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ xu hướng doanh thu và lợi nhuận -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <span>Biểu đồ xu hướng: Doanh thu và Lợi nhuận theo thời gian</span>
        </div>
        <div class="card-body">
            <div id="trendChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Biểu đồ tổng quan phòng + khiếu nại -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <span>Biểu đồ tổng quan phòng</span>
                </div>
                <div class="card-body">
                    <div id="roomOverviewChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
                    <canvas id="roomOverviewChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <span>Biểu đồ khiếu nại</span>
                </div>
                <div class="card-body">
                    <div id="roomChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
                    <canvas id="roomChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ tỉ lệ lấp đầy -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <span>Biểu đồ tỉ lệ lấp đầy các tòa nhà</span>
        </div>
        <div class="card-body">
            <div id="occupancyChartMessage" class="alert alert-warning d-none">Không có dữ liệu để hiển thị.</div>
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
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

    let scatterChart, groupedBarChart, trendChart, occupancyChart, roomChart, roomOverviewChart;

    // Biểu đồ phân tán
    function initScatterChart(properties) {
        const ctxScatter = document.getElementById('scatterChart').getContext('2d');
        if (scatterChart) scatterChart.destroy();
        const data = properties.map(property => ({
            x: property.revenue,
            y: property.profit,
            r: Math.max(5, property.total_rooms / 10),
            name: property.name,
            occupancy_rate: property.total_rooms > 0 ? (property.rented_rooms / property.total_rooms) * 100 : 0,
        }));

        scatterChart = new Chart(ctxScatter, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Tòa nhà',
                    data: data,
                    backgroundColor: data.map(d => d.occupancy_rate > 70 ? '#28a745' : '#dc3545'),
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: { display: true, text: 'Doanh thu (VND)' },
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    },
                    y: {
                        title: { display: true, text: 'Lợi nhuận (VND)' },
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const d = context.raw;
                                return `${d.name}: Doanh thu ${new Intl.NumberFormat('vi-VN').format(d.x)}, Lợi nhuận ${new Intl.NumberFormat('vi-VN').format(d.y)}, Tỷ lệ lấp đầy: ${d.occupancy_rate.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ cột nhóm
    function initGroupedBarChart(properties) {
        const ctxBar = document.getElementById('groupedBarChart').getContext('2d');
        if (groupedBarChart) groupedBarChart.destroy();
        const labels = properties.map(p => p.name);
        const revenueData = properties.map(p => p.revenue || 0);
        const profitData = properties.map(p => p.profit || 0);

        groupedBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: revenueData,
                        backgroundColor: '#ffca07',
                    },
                    {
                        label: 'Lợi nhuận',
                        data: profitData,
                        backgroundColor: '#28a745',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Giá trị (VND)' },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${new Intl.NumberFormat('vi-VN').format(context.raw)}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ xu hướng (Line Chart)
    function initTrendChart(labels, revenueData, profitData) {
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        if (trendChart) trendChart.destroy();
        trendChart = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: revenueData,
                        borderColor: '#0d6efd',
                        fill: false,
                    },
                    {
                        label: 'Lợi nhuận',
                        data: profitData,
                        borderColor: '#28a745',
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Giá trị (VND)' },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    },
                    x: {
                        title: { display: true, text: 'Thời gian' }
                    }
                },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${new Intl.NumberFormat('vi-VN').format(context.raw)}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ tỉ lệ lấp đầy
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Tỉ lệ lấp đầy: ${context.raw}%`;
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

    // Biểu đồ khiếu nại
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
                                return `Khiếu nại: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ tròn tổng quan phòng
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
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Xử lý form lọc
    $('#propertyFilterForm').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.ajax({
            url: '{{ route("landlords.filter-stats") }}',
            method: 'GET',
            data: data,
            success: function(response) {
                // Ẩn tất cả thông báo lỗi
                $('#summaryMessage, #scatterChartMessage, #groupedBarChartMessage, #trendChartMessage, #occupancyChartMessage, #roomChartMessage, #roomOverviewChartMessage').addClass('d-none');

                if (!response.propertyStats || response.propertyStats.length === 0) {
                    $('#scatterChartMessage, #groupedBarChartMessage, #trendChartMessage, #occupancyChartMessage, #roomChartMessage').removeClass('d-none');
                    if (scatterChart) scatterChart.destroy();
                    if (groupedBarChart) groupedBarChart.destroy();
                    if (trendChart) trendChart.destroy();
                    if (occupancyChart) occupancyChart.destroy();
                    if (roomChart) roomChart.destroy();
                    return;
                }

                // Thống kê tổng hợp
                if (response.summary) {
                    $('.summary-total-rooms').text(response.summary.total_rooms || 0);
                    $('.summary-total-rented').text(response.summary.total_rented || 0);
                    $('.summary-total-empty').text(response.summary.total_empty || 0);
                    $('.summary-total-revenue').text(new Intl.NumberFormat('vi-VN').format(response.summary.total_revenue || 0));
                    $('.summary-total-profit').text(new Intl.NumberFormat('vi-VN').format(response.summary.total_profit || 0));
                    $('.summary-total-complaints').text(response.summary.total_complaints || 0);
                } else {
                    $('#summaryMessage').removeClass('d-none');
                }

                // Biểu đồ phân tán
                initScatterChart(response.propertyStats);

                // Biểu đồ cột nhóm
                initGroupedBarChart(response.propertyStats);

                // Biểu đồ xu hướng
                const labels = response.propertyStats.map(stat => stat.name);
                const revenueData = response.propertyStats.map(stat => stat.revenue || 0);
                const profitData = response.propertyStats.map(stat => stat.profit || 0);
                initTrendChart(labels, revenueData, profitData);

                // Biểu đồ tỉ lệ lấp đầy
                const occupancyData = response.propertyStats.map(stat => {
                    return stat.total_rooms > 0 ? (stat.rented_rooms / stat.total_rooms * 100).toFixed(2) : 0;
                });
                initOccupancyChart(labels, occupancyData);

                // Biểu đồ khiếu nại
                const complaintData = response.propertyStats.map(stat => stat.complaints || 0);
                initRoomChart(labels, complaintData);

                // Biểu đồ tròn
                initRoomOverviewChart(response.summary.total_rented || 0, response.summary.total_empty || 0);
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra khi tải dữ liệu: ' + xhr.responseText);
            }
        });
    });

    // Khởi tạo Select2
    $('.select2').select2({
        placeholder: 'Chọn tối đa 10 tòa nhà',
        maximumSelectionLength: 10,
    });

    // Tải dữ liệu ban đầu
    const initialPropertyStats = @json($propertyStats->toArray());
    if (initialPropertyStats.length > 0) {
        initScatterChart(initialPropertyStats);
        initGroupedBarChart(initialPropertyStats);
        initTrendChart(
            initialPropertyStats.map(stat => stat.name),
            initialPropertyStats.map(stat => stat.revenue || 0),
            initialPropertyStats.map(stat => stat.profit || 0)
        );
        initOccupancyChart(
            initialPropertyStats.map(stat => stat.name),
            initialPropertyStats.map(stat => stat.total_rooms > 0 ? (stat.rented_rooms / stat.total_rooms * 100).toFixed(2) : 0)
        );
        initRoomChart(
            initialPropertyStats.map(stat => stat.name),
            initialPropertyStats.map(stat => stat.complaints || 0)
        );
        initRoomOverviewChart(@json($total_rented), @json($total_empty));
    } else {
        $('#scatterChartMessage, #groupedBarChartMessage, #trendChartMessage, #occupancyChartMessage, #roomChartMessage').removeClass('d-none');
    }

    // Tự động submit form khi tải trang
    $('#propertyFilterForm').trigger('submit');
});
</script>
@endpush