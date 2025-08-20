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
                                    {{ $booking->user->info->phone ?? 'Không xác định' }}
                                @else
                                    {{ $booking->phone ?? 'Không xác định' }}
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
                                    $statusLabels = [
                                        'pending' => '⏳ Đang chờ',
                                        'approved' => '✅ Đã duyệt',
                                        'rejected' => '❌ Từ chối',
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
                                    class="badge px-3 py-2 rounded-pill {{ $statusClasses[$booking->status] ?? 'bg-light text-dark' }}">
                                    {{ $statusLabels[$booking->status] ?? $booking->status }}
                                </span>
                            </td>
                            <td>
                                @if ($booking->status === 'pending')
                                    <div class="d-flex flex-column gap-1">
                                        <button class="btn btn-sm btn-success btn-approve" data-id="{{ $booking->id }}">
                                            <i class="bi bi-check-circle me-1"></i> Duyệt
                                        </button>
                                        @if (!$booking->wasReturnedFromStaff)
                                            <button class="btn btn-sm btn-outline-danger btn-reject"
                                                data-id="{{ $booking->id }}">
                                                <i class="bi bi-x-circle me-1"></i> Từ chối
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';

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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`booking-row-${id}`);
                            row.classList.add('table-success');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            alert(data.message || 'Có lỗi xảy ra.');
                        }
                    })
                    .catch(() => {
                        alert('Không thể kết nối đến máy chủ.');
                    });
            }

            document.querySelectorAll('.btn-approve, .btn-reject').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';
                    handleBookingAction(id, action);
                });
            });
        });
    </script>
@endpush
