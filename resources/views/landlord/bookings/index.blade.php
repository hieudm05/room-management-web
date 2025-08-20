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

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f2f9ff;
        }

        .btn-approve:hover {
            box-shadow: 0 0 8px rgba(25, 135, 84, 0.6);
        }

        .btn-reject:hover {
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.6);
        }

        .badge {
            font-size: 0.85rem;
        }

        .filter-form .form-control {
            min-width: 150px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <h2 class="text-primary fw-bold mb-4">
            <i class="bi bi-journal-check me-2"></i>📋 Danh sách đặt phòng
        </h2>

        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-bordered table-hover align-middle text-center bg-white">
                <thead class="table-primary text-dark">
                    <tr>
                        <th>STT</th>
                        <th>Người đặt</th>
                        <th>📞 SĐT</th>
                        <th>📧 Email</th>
                        <th>🏠 Phòng</th>
                        <th>📅 Ngày nhận</th>
                        <th>🖼️ Minh chứng</th>
                        <th>📝 Ghi chú</th>
                        <th>📌 Trạng thái</th>
                        <th>⚙️ Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $index => $booking)
                        <tr id="booking-row-{{ $booking->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $booking->user->name ?? $booking->guest_name }}</td>
                            <td>
                                @if ($booking->user)
                                    {{ $booking->user->phone_number ?? 'Không xác định' }}
                                @else
                                    {{ $booking->phone ?? 'Không xác định' }}
                                @endif
                            </td>
                            <td>
                                @if ($booking->user)
                                    {{ $booking->user->email ?? 'Không xác định' }}
                                @else
                                    {{ $booking->email ?? 'Không xác định' }}
                                @endif
                            </td>
                            <td>{{ $booking->room->room_number ?? 'Không xác định' }}</td>
                            <td>{{ $booking->check_in->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($booking->proof_image)
                                    <a href="{{ Storage::url($booking->proof_image) }}" target="_blank">
                                        <img src="{{ Storage::url($booking->proof_image) }}" class="rounded-3 shadow-sm"
                                            width="60" height="60" style="object-fit: cover;">
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $booking->note ?? '—' }}</td>
                            <td>
                                @php
                                    $isLandlordPost = $booking->post && $booking->post->post_by == auth()->id();

                                    $statusLabels = [
                                        'pending' => '⏳ Đang chờ',
                                        'approved' => '✅ Đã duyệt',
                                        'rejected' => '❌ Từ chối',
                                        'no-cancel' => '🚫 Không đến',
                                        'completed' => '🏁 Hoàn thành',
                                        'waiting' => '🕓 Đang đợi',
                                        'done-with-image' => '📷 Đã nhận phòng (Có ảnh)',
                                    ];
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        'no-cancel' => 'bg-secondary text-white',
                                        'completed' => 'bg-primary text-white',
                                        'waiting' => 'bg-info text-white',
                                        'done-with-image' => 'bg-dark text-white',
                                    ];
                                @endphp
                                <span
                                    class="badge px-3 py-2 rounded-pill {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td>
                                @if ($booking->status === 'pending')
                                    <div class="d-flex flex-column gap-1">

                                        <button class="btn btn-success btn-sm btn-approve" data-id="{{ $booking->id }}">
                                            <i class="bi bi-check-circle"></i> Duyệt
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-reject" data-id="{{ $booking->id }}">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>
                                    </div>
                                @elseif ($isLandlordPost && $booking->status === 'approved')
                                    <div class="d-flex flex-column gap-1">
                                        <button class="btn btn-warning btn-sm btn-wait" data-id="{{ $booking->id }}">
                                            <i class="bi bi-hourglass-split"></i> Đợi
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-reject" data-id="{{ $booking->id }}">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>
                                    </div>
                                @elseif ($isLandlordPost && $booking->status === 'waiting')
                                    <div class="d-flex flex-column gap-1">
                                        <button class="btn btn-success btn-sm btn-done" data-id="{{ $booking->id }}">
                                            <i class="bi bi-check-circle"></i> Xác nhận
                                        </button>
                                        <button class="btn btn-secondary btn-sm btn-no-cancel"
                                            data-id="{{ $booking->id }}">
                                            <i class="bi bi-person-x"></i> Không gặp
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted text-center py-4">
                                <i class="bi bi-inbox fs-3"></i> <br> Không có đơn đặt phòng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Modal Upload -->
            <div class="modal fade" id="uploadProofModal" tabindex="-1" aria-labelledby="uploadProofLabel"
                aria-hidden="true">
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
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadProofModal'));
            const proofForm = document.getElementById('proofForm');
            const proofBookingId = document.getElementById('proofBookingId');

            // Xử lý hành động thường (không upload ảnh)
            function handleBookingAction(id, action) {
                fetch(`/landlord/bookings/${id}/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert(data.message || 'Có lỗi xảy ra.');
                        }
                    })
                    .catch(() => alert('Không thể kết nối máy chủ.'));
            }


            // Xử lý mở modal upload ảnh
            function openUploadModal(id) {
                proofBookingId.value = id;
                proofForm.reset();
                uploadModal.show();
            }

            // Submit form upload ảnh
            proofForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const bookingId = proofBookingId.value;
                const formData = new FormData(proofForm);

                fetch(`/staff/bookings/${bookingId}/done-with-image`, {
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
                            uploadModal.hide();
                            setTimeout(() => location.reload(), 800);
                        } else {
                            alert(data.message || '❌ Có lỗi khi xác nhận!');
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi khi gửi form:', err);
                        alert('⚠ Không thể gửi form!');
                    });
            });

            // Gán sự kiện cho tất cả nút
            // Nút "Đợi"
            // Nút duyệt
            document.querySelectorAll('.btn-approve').forEach(btn => {
                btn.addEventListener('click', () => {
                    handleBookingAction(btn.dataset.id, 'approve');
                });
            });
            document.querySelectorAll('.btn-wait').forEach(button => {
                button.addEventListener('click', function() {
                    handleBookingAction(this.dataset.id, 'waiting');
                });
            });
            document.querySelectorAll('.btn-wait').forEach(button => {
                button.addEventListener('click', function() {
                    handleBookingAction(this.dataset.id, 'waiting');
                });
            });

            // Nút "Từ chối"
            document.querySelectorAll('.btn-reject').forEach(button => {
                button.addEventListener('click', function() {
                    handleBookingAction(this.dataset.id, 'reject');
                });
            });

            // Nút "Hoàn thành"
            // Nút "Xác nhận"
            document.querySelectorAll('.btn-done').forEach(button => {
                button.addEventListener('click', function() {
                    openUploadModal(this.dataset.id); // Mở modal upload ảnh
                });
            });


            // Nút "Không gặp"
            document.querySelectorAll('.btn-no-cancel').forEach(button => {
                button.addEventListener('click', function() {
                    handleBookingAction(this.dataset.id, 'no-cancel');
                });
            });

        });
    </script>
@endpush
