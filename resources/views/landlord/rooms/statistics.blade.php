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

        <div class="card-body">
            @if ($monthlyTotals->sum() === 0)
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bar-chart-fill fs-1"></i>
                    <p class="mt-2">Không có dữ liệu thanh toán trong năm nay.</p>
                </div>
            @else
                <canvas id="revenueChart" height="100"></canvas>
                <canvas id="quarterChart" height="100" class="mt-4 d-none"></canvas>
            @endif
        </div>
    </div>
</div>

<!-- Modal so sánh tháng -->
<div class="modal fade" id="compareModal" tabindex="-1" aria-labelledby="compareModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">So sánh chi tiết theo tháng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <form id="compareForm">
          <div class="row mb-3">
            <div class="col">
              <label for="month1" class="form-label">Chọn tháng</label>
              <select id="month1" class="form-select" required>
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}">Tháng {{ $i }}</option>
                @endfor
              </select>
            </div>
            <div class="col">
              <label for="month2" class="form-label">Chọn tháng</label>
              <select id="month2" class="form-select" required>
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ $i }}">Tháng {{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Xem so sánh</button>
        </form>
        <div id="compareResult" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal xem chi tiết tháng -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết thanh toán tháng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <form id="detailForm">
          <div class="mb-3">
            <label for="detailMonth" class="form-label">Chọn tháng</label>
            <select id="detailMonth" class="form-select" required>
              @for ($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}">Tháng {{ $i }}</option>
              @endfor
            </select>
          </div>
          <button type="submit" class="btn btn-warning">Xem chi tiết</button>
        </form>
        <div id="detailResult" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal so sánh quý -->
<div class="modal fade" id="compareQuarterModal" tabindex="-1" aria-labelledby="compareQuarterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">So sánh chi tiết theo quý</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <form id="compareQuarterForm">
          <div class="row mb-3">
            <div class="col">
              <label for="quarter1" class="form-label">Chọn quý</label>
              <select id="quarter1" class="form-select" required>
                @for ($i = 1; $i <= 4; $i++)
                  <option value="{{ $i }}">Quý {{ $i }}</option>
                @endfor
              </select>
            </div>
            <div class="col">
              <label for="quarter2" class="form-label">Chọn quý</label>
              <select id="quarter2" class="form-select" required>
                @for ($i = 1; $i <= 4; $i++)
                  <option value="{{ $i }}">Quý {{ $i }}</option>
                @endfor
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-success">Xem so sánh</button>
        </form>
        <div id="compareQuarterResult" class="mt-4"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal chi tiết quý -->
<div class="modal fade" id="detailQuarterModal" tabindex="-1" aria-labelledby="detailQuarterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết thanh toán quý</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <form id="detailQuarterForm">
          <div class="mb-3">
            <label for="detailQuarter" class="form-label">Chọn quý</label>
            <select id="detailQuarter" class="form-select" required>
              @for ($i = 1; $i <= 4; $i++)
                <option value="{{ $i }}">Quý {{ $i }}</option>
              @endfor
            </select>
          </div>
          <button type="submit" class="btn btn-warning">Xem chi tiết</button>
        </form>
        <div id="detailQuarterResult" class="mt-4"></div>
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
}

function openCompareModal() {
    new bootstrap.Modal(document.getElementById('compareModal')).show();
}

document.getElementById('month2').addEventListener('change', function () {
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

    fetch(`/landlords/rooms/${roomId}/compare-months?m1=${m1}&m2=${m2}`)
        .then(res => res.json())
        .then(data => {
            if (!data.month1 || !data.month2) {
                document.getElementById('compareResult').innerHTML = `<div class="alert alert-warning">Không có dữ liệu cho một hoặc cả hai tháng đã chọn.</div>`;
                return;
            }

            let html = `<table class="table table-bordered text-center">
                <thead><tr><th>Chỉ số</th><th>Tháng ${m1}</th><th>Tháng ${m2}</th><th>Chênh lệch</th></tr></thead>
                <tbody>`;

            for (let key in data.labels) {
                let v1 = data.month1[key] ?? 0;
                let v2 = data.month2[key] ?? 0;
                let diff = v2 - v1;
                let diffText = diff > 0 ? `+${diff.toLocaleString('vi-VN')}` : diff.toLocaleString('vi-VN');
                html += `<tr>
                    <td>${data.labels[key]}</td>
                    <td>${v1.toLocaleString('vi-VN')}</td>
                    <td>${v2.toLocaleString('vi-VN')}</td>
                    <td style="color:${diff > 0 ? 'green' : diff < 0 ? 'red' : 'gray'}">${diffText}</td>
                </tr>`;
            }

            html += '</tbody></table>';

            const resultContainer = document.getElementById('compareResult');
            resultContainer.innerHTML = ''; // Clear old result
            resultContainer.innerHTML = html;
            resultContainer.style.display = 'none';
            void resultContainer.offsetHeight; // Trigger reflow
            resultContainer.style.display = 'block';
        });
});

function openDetailModal() {
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

function openCompareQuarterModal() {
    toggleChart('quarter');
    new bootstrap.Modal(document.getElementById('compareQuarterModal')).show();
}

function openDetailQuarterModal() {
    toggleChart('quarter');
    new bootstrap.Modal(document.getElementById('detailQuarterModal')).show();
}

document.getElementById('detailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const month = parseInt(document.getElementById('detailMonth').value);

    fetch(`/landlords/rooms/${roomId}/month-detail?month=${month}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('detailResult').innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                return;
            }

            let html = `<table class="table table-bordered text-center">
                <thead><tr><th>Thông tin</th><th>Giá trị</th></tr></thead><tbody>`;

            for (let key in data.labels) {
                html += `<tr>
                    <td>${data.labels[key]}</td>
                    <td>${data.values[key] ?? '-'}</td>
                </tr>`;
            }

            html += '</tbody></table>';

            const container = document.getElementById('detailResult');
            container.innerHTML = html;
            container.style.display = 'none';
            void container.offsetHeight;
            container.style.display = 'block';
        });
});

document.getElementById('compareQuarterForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const q1 = parseInt(document.getElementById('quarter1').value);
    const q2 = parseInt(document.getElementById('quarter2').value);

    if (q1 === q2) {
        alert('Vui lòng chọn 2 quý khác nhau để so sánh!');
        return;
    }

    fetch(`/landlords/rooms/${roomId}/compare-quarters?q1=${q1}&q2=${q2}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.quarter1 || !data.quarter2) {
                document.getElementById('compareQuarterResult').innerHTML = `<div class="alert alert-warning">Không có dữ liệu cho một hoặc cả hai quý đã chọn.</div>`;
                return;
            }

            let html = `<table class="table table-bordered text-center">
                <thead><tr><th>Chỉ số</th><th>Quý ${q1}</th><th>Quý ${q2}</th><th>Chênh lệch</th></tr></thead>
                <tbody>`;

            for (let key in data.labels) {
                const v1 = data.quarter1[key] ?? 0;
                const v2 = data.quarter2[key] ?? 0;

                // Nếu là status (chuỗi), chỉ hiển thị, không tính chênh lệch
                if (key === 'status') {
                    html += `<tr>
                        <td>${data.labels[key]}</td>
                        <td>${v1}</td>
                        <td>${v2}</td>
                        <td>-</td>
                    </tr>`;
                } else {
                    const diff = v2 - v1;
                    const diffText = diff > 0 ? `+${diff.toLocaleString('vi-VN')}` : diff.toLocaleString('vi-VN');
                    html += `<tr>
                        <td>${data.labels[key]}</td>
                        <td>${v1.toLocaleString('vi-VN')}</td>
                        <td>${v2.toLocaleString('vi-VN')}</td>
                        <td style="color:${diff > 0 ? 'green' : diff < 0 ? 'red' : 'gray'}">${diffText}</td>
                    </tr>`;
                }
            }

            html += '</tbody></table>';
            const resultContainer = document.getElementById('compareQuarterResult');
            resultContainer.innerHTML = '';
            resultContainer.innerHTML = html;
            resultContainer.style.display = 'none';
            void resultContainer.offsetHeight;
            resultContainer.style.display = 'block';
        });
});

document.getElementById('detailQuarterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const quarter = parseInt(document.getElementById('detailQuarter').value);

    fetch(`/landlords/rooms/${roomId}/quarter-detail?quarter=${quarter}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                document.getElementById('detailQuarterResult').innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                return;
            }

            let html = `<table class="table table-bordered text-center">
                <thead><tr><th>Thông tin</th><th>Giá trị</th></tr></thead><tbody>`;

            for (let key in data.labels) {
                const value = data.values[key];

                if (key === 'status') {
                    html += `<tr>
                        <td>${data.labels[key]}</td>
                        <td>${value ?? '-'}</td>
                    </tr>`;
                } else {
                    html += `<tr>
                        <td>${data.labels[key]}</td>
                        <td>${(value ?? 0).toLocaleString('vi-VN')}</td>
                    </tr>`;
                }
            }

            html += '</tbody></table>';

            const container = document.getElementById('detailQuarterResult');
            container.innerHTML = html;
            container.style.display = 'none';
            void container.offsetHeight;
            container.style.display = 'block';
        });
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
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + ' VNĐ'
                    }
                },
                legend: { display: false }
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
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + ' VNĐ'
                    }
                },
                legend: { display: false }
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
@endsection
