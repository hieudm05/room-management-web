@extends('landlord.layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media (max-width: 768px) {

            table.table td,
            table.table th {
                font-size: 0.875rem;
                padding: 0.5rem;
            }
        }

        .table thead th {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.85rem;
        }

        .action-buttons .btn {
            margin: 2px 0;
        }

        .modal .form-label {
            font-weight: 600;
        }

        .modal .modal-header {
            background-color: #0d6efd;
            color: white;
        }

        .modal .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-footer .btn {
            min-width: 100px;
        }

        .btn i {
            margin-right: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-primary fw-bold">
            <i class="bi bi-calendar2-check me-2"></i>📋 Danh sách đặt phòng
        </h2>

        <div class="table-responsive">
            <table
                class="table table-bordered align-middle text-center shadow-sm bg-white table-hover rounded-3 overflow-hidden">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Người đặt</th>
                        <th>Số điện thoại</th>
                        <th>Phòng</th>
                        <th>Ngày nhận</th>
                        <th>Hình ảnh</th>
                        <th>Ghi chú</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $index => $booking)
                        <tr id="booking-row-{{ $booking->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $booking->user->name ?? $booking->guest_name }}</td>
                            <td>
                                {{ $booking->user->info->phone ?? ($booking->phone ?? 'Không xác định') }}
                            </td>
                            <td>{{ $booking->room->name ?? 'Không xác định' }}</td>
                            <td>{{ $booking->check_in->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($booking->proof_image)
                                    <a href="{{ Storage::url($booking->proof_image) }}" target="_blank">
                                        <img src="{{ Storage::url($booking->proof_image) }}" class="rounded shadow-sm"
                                            width="60" height="60" style="object-fit: cover;">
                                    </a>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>{{ $booking->note ?? '---' }}</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'pending' => '⏳ Đang chờ duyệt',
                                        'approved' => '✅ Đã duyệt',
                                        'rejected' => '❌ Đã từ chối',
                                        'no-cancel' => '🚫 Không đến',
                                        'completed' => '🏁 Hoàn thành',
                                        'waiting' => '🕓 Đang đợi',
                                    ];

                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        'no-cancel' => 'bg-secondary text-white',
                                        'completed' => 'bg-primary text-white',
                                        'waiting' => 'bg-info text-white',
                                    ];
                                @endphp
                                <span
                                    class="badge px-3 py-2 rounded-pill {{ $statusClasses[$booking->status] ?? 'bg-light' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td class="action-buttons">
                                @if ($booking->status === 'approved')
                                    <button class="btn btn-warning btn-sm btn-wait" data-id="{{ $booking->id }}">
                                        <i class="bi bi-hourglass-split"></i> Đợi
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-reject" data-id="{{ $booking->id }}">
                                        <i class="bi bi-x-circle"></i> Từ chối
                                    </button>
                                @elseif ($booking->status === 'waiting')
                                    <button class="btn btn-success btn-sm btn-done" data-id="{{ $booking->id }}">
                                        <i class="bi bi-check-circle"></i> Xác nhận
                                    </button>
                                    <button class="btn btn-secondary btn-sm btn-no-cancel" data-id="{{ $booking->id }}">
                                        <i class="bi bi-person-x"></i> Không gặp
                                    </button>
                                @else
                                    <em class="text-muted">—</em>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4"></i> Không có đơn đặt phòng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Upload -->
        <div class="modal fade" id="uploadProofModal" tabindex="-1" aria-labelledby="uploadProofLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="proofForm" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" name="booking_id" id="proofBookingId">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-upload"></i> Tải lên hình xác nhận</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="proofImage" class="form-label">Chọn ảnh xác nhận</label>
                                <input type="file" class="form-control" id="proofImage" name="proof_image"
                                    accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">✔ Xác nhận hoàn thành</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';

            const actionMap = {
                '.btn-reject': '/landlord/bookings/{id}/reject',
                '.btn-wait': '/staff/bookings/{id}/wait',
                '.btn-no-cancel': '/staff/bookings/{id}/no-cancel',
            };

            Object.entries(actionMap).forEach(([selector, endpoint]) => {
                document.querySelectorAll(selector).forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const url = endpoint.replace('{id}', id);

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('❌ Có lỗi xảy ra!');
                                }
                            })
                            .catch(err => {
                                console.error('Lỗi gửi request:', err);
                                alert('⚠ Không thể gửi yêu cầu!');
                            });
                    });
                });
            });

            document.querySelectorAll('.btn-done').forEach(btn => {
                btn.addEventListener('click', function() {
                    const bookingId = this.dataset.id;
                    document.getElementById('proofBookingId').value = bookingId;
                    new bootstrap.Modal(document.getElementById('uploadProofModal')).show();
                });
            });

            document.getElementById('proofForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const bookingId = formData.get('booking_id');
                const url = `/staff/bookings/${bookingId}/done-with-image`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('❌ Có lỗi khi xác nhận!');
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi khi gửi form:', err);
                        alert('⚠ Không thể gửi form!');
                    });
            });
        });
    </script>
@endpush
