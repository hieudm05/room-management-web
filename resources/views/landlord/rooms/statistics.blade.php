@extends('landlord.layouts.app')

@section('title', 'Thống kê tổng tiền phòng ' . $room->room_number)

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">💰 Thống kê tổng tiền - Phòng <strong>{{ $room->room_number }}</strong></h5>
            <div class="d-flex align-items-center">
                <!-- Nút chọn loại -->
                <div class="btn-group me-3">
                    <button class="btn btn-outline-primary btn-sm" onclick="toggleChart('month')">Tháng</button>
                    <button class="btn btn-outline-danger btn-sm" onclick="toggleChart('quarter')">Quý</button>
                </div>

                <!-- Các nút theo tháng -->
                <div id="monthActions" class="d-flex">
                    <button class="btn btn-outline-success btn-sm me-2" onclick="openCompareModal()">🔍 So sánh chi tiết tháng</button>
                    <button class="btn btn-outline-warning btn-sm" onclick="openDetailModal()">📄 Xem chi tiết tháng</button>
                </div>

                <!-- Các nút theo quý -->
                <div id="quarterActions" class="d-none d-flex">
                    <button class="btn btn-outline-success btn-sm me-2" onclick="openCompareQuarterModal()">📊 So sánh chi tiết quý</button>
                    <button class="btn btn-outline-warning btn-sm" onclick="openDetailQuarterModal()">📄 Xem chi tiết quý</button>
                </div>
            </div>
        </div>

        <div class="card-body position-relative">
            @if ($monthlyTotals->sum() === 0)
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bar-chart-fill fs-1"></i>
                    <p class="mt-2">Không có dữ liệu thanh toán trong năm nay.</p>
                </div>
            @else
                <!-- Container cho biểu đồ và overlay -->
                <div id="chartContainer" class="position-relative">
                    <canvas id="revenueChart" height="100"></canvas>
                    <canvas id="quarterChart" height="100" class="mt-4 d-none"></canvas>
                    
                    <!-- Overlay cho bảng so sánh -->
                    <div id="compareOverlay" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 10;">
                        <div class="card shadow-lg border-0" style="min-width: 600px; max-width: 800px;">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-white">📊 Kết quả so sánh</h6>
                                <button type="button" class="btn btn-sm btn-outline-light" onclick="closeOverlay()">
                                    <i class="bi bi-x"></i> Đóng
</button>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div id="overlayCompareResult"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Overlay cho chi tiết -->
                    <div id="detailOverlay" class="position-absolute top-50 start-50 translate-middle d-none" style="z-index: 10;">
                        <div class="card shadow-lg border-0" style="min-width: 600px; max-width: 800px;">
                            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">📄 Chi tiết thanh toán</h6>
                                <button type="button" class="btn btn-sm btn-outline-dark" onclick="closeOverlay()">
                                    <i class="bi bi-x"></i> Đóng
                                </button>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <div id="overlayDetailResult"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal so sánh tháng (chỉ chọn tháng, kết quả hiển thị ở overlay) -->
<div class="modal fade" id="compareModal" tabindex="-1" aria-labelledby="compareModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">So sánh chi tiết theo tháng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="compareForm">
          <div class="row mb-3">
            <div class="col">
              <label for="month1" class="form-label">Chọn tháng đầu</label>
              <select id="month1" class="form-select" required>
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}">Tháng {{ $i }}</option>
                @endfor
              </select>
            </div>
            <div class="col">
              <label for="month2" class="form-label">Chọn tháng thứ hai</label>
              <select id="month2" class="form-select" required>
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>Tháng {{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Xem so sánh</button>
          </div>
        </form>
</div>
    </div>
  </div>
</div>

<!-- Modal xem chi tiết tháng (chỉ chọn tháng, kết quả hiển thị ở overlay) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết thanh toán tháng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="detailForm">
          <div class="mb-3">
            <label for="detailMonth" class="form-label">Chọn tháng</label>
            <select id="detailMonth" class="form-select" required>
              @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}">Tháng {{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-warning">Xem chi tiết</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal so sánh quý -->
<div class="modal fade" id="compareQuarterModal" tabindex="-1" aria-labelledby="compareQuarterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">So sánh chi tiết theo quý</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="compareQuarterForm">
          <div class="row mb-3">
            <div class="col">
              <label for="quarter1" class="form-label">Chọn quý đầu</label>
              <select id="quarter1" class="form-select" required>
                @for ($i = 1; $i <= 4; $i++)
                  <option value="{{ $i }}">Quý {{ $i }}</option>
                @endfor
              </select>
            </div>
            <div class="col">
              <label for="quarter2" class="form-label">Chọn quý thứ hai</label>
              <select id="quarter2" class="form-select" required>
                @for ($i = 1; $i <= 4; $i++)
                  <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>Quý {{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Xem so sánh</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal chi tiết quý (chỉ chọn quý, kết quả hiển thị ở overlay) -->
<div class="modal fade" id="detailQuarterModal" tabindex="-1" aria-labelledby="detailQuarterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết thanh toán quý</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="detailQuarterForm">
          <div class="mb-3">
            <label for="detailQuarter" class="form-label">Chọn quý</label>
            <select id="detailQuarter" class="form-select" required>
              @for ($i = 1; $i <= 4; $i++)
                <option value="{{ $i }}">Quý {{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-warning">Xem chi tiết</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const roomId = {{ $room->room_id }};

        function toggleChart(type) {
            document.getElementById('revenueChart').classList.toggle('d-none', type !== 'month');
            document.getElementById('quarterChart').classList.toggle('d-none', type !== 'quarter');
            document.getElementById('monthActions').classList.toggle('d-none', type !== 'month');
            document.getElementById('quarterActions').classList.toggle('d-none', type !== 'quarter');

            // Ẩn overlay khi chuyển biểu đồ
            closeOverlay();
        }

        function openCompareModal() {
            new bootstrap.Modal(document.getElementById('compareModal')).show();
        }

        function openDetailModal() {
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        }

        function closeOverlay() {
            // Ẩn tất cả overlay
            document.getElementById('compareOverlay').classList.add('d-none');
            document.getElementById('detailOverlay').classList.add('d-none');

            // Xóa class overlay-active để bỏ hiệu ứng mờ
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.classList.remove('overlay-active');
        }

        // Thêm backdrop mờ khi hiển thị overlay so sánh
        function showCompareOverlay() {
            const overlay = document.getElementById('compareOverlay');
            overlay.classList.remove('d-none');

            // Ẩn overlay chi tiết nếu đang hiển thị
            document.getElementById('detailOverlay').classList.add('d-none');

            // Thêm backdrop mờ
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.classList.add('overlay-active');
        }

        // Thêm backdrop mờ khi hiển thị overlay chi tiết
        function showDetailOverlay() {
            const overlay = document.getElementById('detailOverlay');
            overlay.classList.remove('d-none');

            // Ẩn overlay so sánh nếu đang hiển thị
            document.getElementById('compareOverlay').classList.add('d-none');

            // Thêm backdrop mờ
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.classList.add('overlay-active');
        }

        document.getElementById('month2').addEventListener('change', function() {
            const m2 = parseInt(this.value);
            const month1 = document.getElementById('month1');
            if (parseInt(month1.value) === m2) {
                for (let option of month1.options) {
                    if (parseInt(option.value) !== m2) {
                        month1.value = option.value;
                        break;
                    }
                }
            }
        });
document.getElementById('compareForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const m1 = parseInt(document.getElementById('month1').value);
            const m2 = parseInt(document.getElementById('month2').value);

            if (m1 === m2) {
                alert('Vui lòng chọn 2 tháng khác nhau để so sánh!');
                return;
            }

            // Đóng modal và xóa hiệu ứng mờ
            bootstrap.Modal.getInstance(document.getElementById('compareModal')).hide();
            document.getElementById('chartContainer').classList.remove('overlay-active');

            fetch(`/landlords/rooms/${roomId}/compare-months?m1=${m1}&m2=${m2}`)
                .then(res => res.json())
                .then(data => {
                    // Xử lý trường hợp cả hai tháng không có dữ liệu
                    if (!data.success && data.both_no_data) {
                        document.getElementById('overlayCompareResult').innerHTML = `
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Không có dữ liệu để so sánh giữa tháng ${m1} và tháng ${m2}
                        </div>
                    </div>`;
                        showCompareOverlay();
                        return;
                    }

                    // Xử lý lỗi khác
                    if (!data.success || !data.labels) {
                        document.getElementById('overlayCompareResult').innerHTML = `
                    <div class="alert alert-danger">Lỗi khi tải dữ liệu so sánh.</div>`;
                        showCompareOverlay();
                        return;
                    }

                    // Lấy dữ liệu từ response (backend đã xử lý default values)
                    const month1Data = data.month1;
                    const month2Data = data.month2;

                    // Tạo thông báo nếu có tháng nào không có dữ liệu
                    let warningHtml = '';
                    if (!data.has_month1_data && data.has_month2_data) {
                        warningHtml = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Tháng ${m1} không có dữ liệu, hiển thị giá trị mặc định (0)
                    </div>`;
                    } else if (data.has_month1_data && !data.has_month2_data) {
                        warningHtml = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Tháng ${m2} không có dữ liệu, hiển thị giá trị mặc định (0)
                    </div>`;
                    }

                    // Tạo bảng so sánh
                    let html = `
                ${warningHtml}
<div class="mb-3 text-center">
                    <h6 class="text-primary">So sánh Tháng ${m1} và Tháng ${m2}</h6>
                </div>
                <table class="table table-bordered text-center table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Chỉ số</th>
                            <th>Tháng ${m1}</th>
                            <th>Tháng ${m2}</th>
                            <th>Chênh lệch</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    // Tạo các hàng dữ liệu
                    for (let key in data.labels) {
                        let v1 = month1Data[key];
                        let v2 = month2Data[key];

                        let diff, diffText, diffClass;

                        // Xử lý trường hợp status (không tính chênh lệch)
                        if (key === 'status') {
                            diffText = '-';
                            diffClass = 'text-muted';
                        } else {
                            diff = parseFloat(v2) - parseFloat(v1);
                            if (diff > 0) {
                                diffText = `+${diff.toLocaleString('vi-VN')}`;
                                diffClass = 'text-success fw-bold';
                            } else if (diff < 0) {
                                diffText = diff.toLocaleString('vi-VN');
                                diffClass = 'text-danger fw-bold';
                            } else {
                                diffText = '0';
                                diffClass = 'text-muted';
                            }
                        }

                        // Định dạng hiển thị cho các giá trị
                        let displayV1 = (key === 'status') ? v1 :
                            (typeof v1 === 'number' ? v1.toLocaleString('vi-VN') : v1);
                        let displayV2 = (key === 'status') ? v2 :
                            (typeof v2 === 'number' ? v2.toLocaleString('vi-VN') : v2);

                        html += `
                    <tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${displayV1}</td>
                        <td>${displayV2}</td>
                        <td class="${diffClass}">${diffText}</td>
                    </tr>`;
                    }

                    html += '</tbody></table>';

                    document.getElementById('overlayCompareResult').innerHTML = html;
                    showCompareOverlay();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('overlayCompareResult').innerHTML = `
                <div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu so sánh.</div>`;
                    showCompareOverlay();
});
        });

        // Xử lý form chi tiết tháng - hiển thị overlay
        document.getElementById('detailForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const month = parseInt(document.getElementById('detailMonth').value);

            // Đóng modal
            bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();

            fetch(`/landlords/rooms/${roomId}/month-detail?month=${month}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        document.getElementById('overlayDetailResult').innerHTML =
                            `<div class="alert alert-warning">${data.message}</div>`;
                        showDetailOverlay();
                        return;
                    }

                    let html = `<div class="mb-3 text-center">
                <h6 class="text-warning">Chi tiết Tháng ${month}</h6>
            </div>
            <table class="table table-bordered text-center table-striped">
                <thead class="table-warning">
                    <tr>
                        <th>Thông tin</th>
                        <th>Giá trị</th>
                    </tr>
                </thead>
                <tbody>`;

                    for (let key in data.labels) {
                        const value = data.values[key];

                        if (key === 'status') {
                            html += `<tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${value ?? '-'}</td>
                    </tr>`;
                        } else {
                            html += `<tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${(value ?? 0).toLocaleString('vi-VN')}</td>
                    </tr>`;
                        }
                    }

                    html += '</tbody></table>';

                    document.getElementById('overlayDetailResult').innerHTML = html;
                    showDetailOverlay();
                });
        });

        function openCompareQuarterModal() {
            toggleChart('quarter');
            new bootstrap.Modal(document.getElementById('compareQuarterModal')).show();
        }

        function openDetailQuarterModal() {
            toggleChart('quarter');
            new bootstrap.Modal(document.getElementById('detailQuarterModal')).show();
        }

        document.getElementById('compareQuarterForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const q1 = parseInt(document.getElementById('quarter1').value);
            const q2 = parseInt(document.getElementById('quarter2').value);

            if (q1 === q2) {
                alert('Vui lòng chọn 2 quý khác nhau để so sánh!');
                return;
            }
// Đóng modal và xóa hiệu ứng mờ
            bootstrap.Modal.getInstance(document.getElementById('compareQuarterModal')).hide();
            document.getElementById('chartContainer').classList.remove('overlay-active');

            fetch(`/landlords/rooms/${roomId}/compare-quarters?q1=${q1}&q2=${q2}`)
                .then(res => res.json())
                .then(data => {
                    // Xử lý trường hợp cả hai quý không có dữ liệu
                    if (!data.success && data.both_no_data) {
                        document.getElementById('overlayCompareResult').innerHTML = `
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Không có dữ liệu để so sánh giữa quý ${q1} và quý ${q2}
                        </div>
                    </div>`;
                        showCompareOverlay();
                        return;
                    }

                    // Xử lý lỗi khác
                    if (!data.success || !data.labels) {
                        document.getElementById('overlayCompareResult').innerHTML = `
                    <div class="alert alert-danger">Lỗi khi tải dữ liệu so sánh.</div>`;
                        showCompareOverlay();
                        return;
                    }

                    // Lấy dữ liệu từ response (backend đã xử lý default values)
                    const quarter1Data = data.quarter1;
                    const quarter2Data = data.quarter2;

                    // Tạo thông báo nếu có quý nào không có dữ liệu
                    let warningHtml = '';
                    if (!data.has_quarter1_data && data.has_quarter2_data) {
                        warningHtml = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Quý ${q1} không có dữ liệu, hiển thị giá trị mặc định (0)
                    </div>`;
                    } else if (data.has_quarter1_data && !data.has_quarter2_data) {
                        warningHtml = `
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Quý ${q2} không có dữ liệu, hiển thị giá trị mặc định (0)
                    </div>`;
                    }

                    let html = `
                ${warningHtml}
                <div class="mb-3 text-center">
                    <h6 class="text-primary">So sánh Quý ${q1} và Quý ${q2}</h6>
                </div>
                <table class="table table-bordered text-center table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Chỉ số</th>
                            <th>Quý ${q1}</th>
                            <th>Quý ${q2}</th>
<th>Chênh lệch</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    for (let key in data.labels) {
                        let v1 = quarter1Data[key];
                        let v2 = quarter2Data[key];

                        let diff, diffText, diffClass;

                        // Xử lý trường hợp status (không tính chênh lệch)
                        if (key === 'status') {
                            diffText = '-';
                            diffClass = 'text-muted';
                        } else {
                            diff = parseFloat(v2) - parseFloat(v1);
                            if (diff > 0) {
                                diffText = `+${diff.toLocaleString('vi-VN')}`;
                                diffClass = 'text-success fw-bold';
                            } else if (diff < 0) {
                                diffText = diff.toLocaleString('vi-VN');
                                diffClass = 'text-danger fw-bold';
                            } else {
                                diffText = '0';
                                diffClass = 'text-muted';
                            }
                        }

                        // Định dạng hiển thị cho các giá trị
                        let displayV1 = (key === 'status') ? v1 :
                            (typeof v1 === 'number' ? v1.toLocaleString('vi-VN') : v1);
                        let displayV2 = (key === 'status') ? v2 :
                            (typeof v2 === 'number' ? v2.toLocaleString('vi-VN') : v2);

                        html += `
                    <tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${displayV1}</td>
                        <td>${displayV2}</td>
                        <td class="${diffClass}">${diffText}</td>
                    </tr>`;
                    }

                    html += '</tbody></table>';

                    document.getElementById('overlayCompareResult').innerHTML = html;
                    showCompareOverlay();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('overlayCompareResult').innerHTML = `
                <div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu so sánh.</div>`;
                    showCompareOverlay();
                });
        });

        // Xử lý form chi tiết quý - hiển thị overlay
        document.getElementById('detailQuarterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const quarter = parseInt(document.getElementById('detailQuarter').value);

            // Đóng modal
            bootstrap.Modal.getInstance(document.getElementById('detailQuarterModal')).hide();

            fetch(`/landlords/rooms/${roomId}/quarter-detail?quarter=${quarter}`)
.then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        document.getElementById('overlayDetailResult').innerHTML =
                            `<div class="alert alert-warning">${data.message}</div>`;
                        showDetailOverlay();
                        return;
                    }

                    let html = `<div class="mb-3 text-center">
                <h6 class="text-warning">Chi tiết Quý ${quarter}</h6>
            </div>
            <table class="table table-bordered text-center table-striped">
                <thead class="table-warning">
                    <tr>
                        <th>Thông tin</th>
                        <th>Giá trị</th>
                    </tr>
                </thead>
                <tbody>`;

                    for (let key in data.labels) {
                        const value = data.values[key];

                        if (key === 'status') {
                            html += `<tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${value ?? '-'}</td>
                    </tr>`;
                        } else {
                            html += `<tr>
                        <td class="fw-semibold">${data.labels[key]}</td>
                        <td>${(value ?? 0).toLocaleString('vi-VN')}</td>
                    </tr>`;
                        }
                    }

                    html += '</tbody></table>';

                    document.getElementById('overlayDetailResult').innerHTML = html;
                    showDetailOverlay();
                });
        });

        // Đảm bảo overlay được ẩn khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            const compareOverlay = document.getElementById('compareOverlay');
            const detailOverlay = document.getElementById('detailOverlay');
            const chartContainer = document.getElementById('chartContainer');

            if (compareOverlay) {
                compareOverlay.classList.add('d-none');
            }

            if (detailOverlay) {
                detailOverlay.classList.add('d-none');
            }

            if (chartContainer) {
                chartContainer.classList.remove('overlay-active');
            }
        });

        // Biểu đồ tháng
        const ctx = document.getElementById('revenueChart')?.getContext('2d');
        if (ctx) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(0, 200, 255, 0.7)');
            gradient.addColorStop(1, 'rgba(0, 200, 255, 0.1)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                    ],
                    datasets: [{
                        label: 'Tổng tiền (VNĐ)',
                        data: {!! json_encode($monthlyTotals->values()) !!},
                        backgroundColor: gradient,
                        borderColor: 'rgba(0, 200, 255, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(0, 200, 255, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tổng tiền thuê phòng theo tháng trong năm {{ now()->year }}',
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + ' VNĐ'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => value.toLocaleString('vi-VN')
                            }
                        }
                    }
                }
            });
        }

        // Biểu đồ quý
        const ctxQ = document.getElementById('quarterChart')?.getContext('2d');
        if (ctxQ) {
            const gradientQ = ctxQ.createLinearGradient(0, 0, 0, 300);
            gradientQ.addColorStop(0, 'rgba(255, 99, 132, 0.7)');
            gradientQ.addColorStop(1, 'rgba(255, 99, 132, 0.2)');

            new Chart(ctxQ, {
                type: 'bar',
                data: {
                    labels: ['Quý 1', 'Quý 2', 'Quý 3', 'Quý 4'],
                    datasets: [{
                        label: 'Tổng tiền theo quý (VNĐ)',
                        data: {!! json_encode($quarterTotals->values()) !!},
                        backgroundColor: gradientQ,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: 'rgba(255, 99, 132, 0.9)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Thống kê tổng tiền theo quý',
                            font: {
                                size: 16
                            }
},
                        tooltip: {
                            callbacks: {
                                label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + ' VNĐ'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => value.toLocaleString('vi-VN')
                            }
                        }
                    }
                }
            });
        }
    </script>

    <style>
        #chartContainer {
            position: relative;
        }

        #chartContainer.overlay-active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 5;
        }

        #compareOverlay,
        #detailOverlay {
            backdrop-filter: blur(2px);
        }

        #compareOverlay .card,
        #detailOverlay .card {
            border: 2px solid #dee2e6;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .table-primary th {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .table-warning th {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
    </style>
@endsection
