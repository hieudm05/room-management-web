@extends('landlord.layouts.app')
@section('title', 'Thống kê')
@section('content')
    <div class="container">
        <h2 class="mb-4">Thống kê Khiếu nại và Đặt phòng theo từng Tòa</h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="month_complaint">Chọn tháng khiếu nại:</label>
                <input type="month" id="month_complaint" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="month_booking">Chọn tháng đặt phòng:</label>
                <input type="month" id="month_booking" class="form-control">
            </div>
        </div>
        <div id="chart-container"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            let complaintData = null; // Lưu trữ dữ liệu khiếu nại
            let bookingData = null; // Lưu trữ dữ liệu đặt phòng
            let complaintCharts = []; // Lưu trữ instance của biểu đồ khiếu nại
            let bookingCharts = []; // Lưu trữ instance của biểu đồ đặt phòng

            // Hàm khởi tạo container cho các biểu đồ
            function initChartContainer(data) {
                const container = document.getElementById('chart-container');
                container.innerHTML = '';
                data.forEach((building, index) => {
                    const block = document.createElement('div');
                    block.classList.add('mb-5');
                    block.innerHTML = `
                        <h4 class="mb-3 text-primary">🏢 ${building.building_name}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="complaintsChart_${index}" height="250"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="bookingChart_${index}" height="250"></canvas>
                            </div>
                        </div>
                    `;
                    container.appendChild(block);
                });
            }

            // Hàm render biểu đồ khiếu nại
            function renderComplaintCharts(data, monthComplaint) {
                complaintData = data; // Lưu dữ liệu khiếu nại
                complaintCharts.forEach(chart => chart.destroy()); // Xóa các biểu đồ cũ
                complaintCharts = [];

                data.forEach((building, index) => {
                    const ctx = document.getElementById(`complaintsChart_${index}`).getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: building.complaints.labels,
                            datasets: [{
                                    label: 'Đã xử lý',
                                    data: building.complaints.resolved,
                                    backgroundColor: '#2196f3',
                                    stack: 'stack1'
                                },
                                {
                                    label: 'Từ chối xử lý',
                                    data: building.complaints.rejected,
                                    backgroundColor: '#e91e63',
                                    stack: 'stack1'
                                },
                                {
                                    label: 'Chủ Trọ Hủy',
                                    data: building.complaints.cancelled,
                                    backgroundColor: '#cc99ff',
                                    stack: 'stack1'
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Khiếu nại - ' + monthComplaint
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    complaintCharts.push(chart);
                });
            }

            // Hàm render biểu đồ đặt phòng
            function renderBookingCharts(data, monthBooking) {
                bookingData = data; // Lưu dữ liệu đặt phòng
                bookingCharts.forEach(chart => chart.destroy()); // Xóa các biểu đồ cũ
                bookingCharts = [];

                data.forEach((building, index) => {
                    const ctx = document.getElementById(`bookingChart_${index}`).getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: building.bookings.labels,
                            datasets: [{
                                    label: 'Gặp',
                                    data: building.bookings.completed,
                                    backgroundColor: '#4caf50',
                                    stack: 'stack1'
                                },
                                {
                                    label: 'Từ chối gặp',
                                    data: building.bookings["no-cancel"],
                                    backgroundColor: '#f44336',
                                    stack: 'stack1'
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Đặt phòng - ' + monthBooking
                                }
                            },
                            responsive: true,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    bookingCharts.push(chart);
                });
            }

            // Hàm tải dữ liệu khiếu nại
            function loadComplaintChart() {
                const monthComplaint = $('#month_complaint').val();
                $.ajax({
                    url: '{{ route('landlord.staff.chart.index') }}',
                    type: 'GET',
                    data: {
                        month_complaint: monthComplaint
                    },
                    success: function(data) {
                        if (!complaintData && !bookingData) {
                            initChartContainer(data); // Chỉ khởi tạo container lần đầu
                        }
                        renderComplaintCharts(data, monthComplaint);
                    },
                    error: function(xhr) {
                        alert('Lỗi khi tải biểu đồ khiếu nại');
                        console.error(xhr.responseText);
                    }
                });
            }

            // Hàm tải dữ liệu đặt phòng
            function loadBookingChart() {
                const monthBooking = $('#month_booking').val();
                $.ajax({
                    url: '{{ route('landlord.staff.chart.index') }}',
                    type: 'GET',
                    data: {
                        month_booking: monthBooking
                    },
                    success: function(data) {
                        if (!complaintData && !bookingData) {
                            initChartContainer(data); // Chỉ khởi tạo container lần đầu
                        }
                        renderBookingCharts(data, monthBooking);
                    },
                    error: function(xhr) {
                        alert('Lỗi khi tải biểu đồ đặt phòng');
                        console.error(xhr.responseText);
                    }
                });
            }

            // Gắn sự kiện thay đổi input
            $('#month_complaint').on('change', function() {
                loadComplaintChart();
            });

            $('#month_booking').on('change', function() {
                loadBookingChart();
            });

            // Tải dữ liệu ban đầu
            loadComplaintChart();
            loadBookingChart();
        });
    </script>
@endsection
