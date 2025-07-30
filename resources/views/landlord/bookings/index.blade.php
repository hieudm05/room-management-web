@extends('landlord.layouts.app')

@push('styles')
    <!-- Bootstrap Icons -->
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



        {{-- Bảng --}}
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
                            <td>{{ $booking->room->name ?? 'Không xác định' }}</td>
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

    {{-- Modal xác nhận --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-question-circle-fill me-2"></i> Xác nhận</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body fw-semibold" id="confirmMessage">
                    Bạn chắc chắn muốn thực hiện thao tác này?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-danger" id="confirmAction">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';
            let actionType = null;
            let currentId = null;

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            const confirmBtn = document.getElementById('confirmAction');
            const confirmMessage = document.getElementById('confirmMessage');

            document.querySelectorAll('.btn-approve, .btn-reject').forEach(btn => {
                btn.addEventListener('click', function() {
                    actionType = this.classList.contains('btn-approve') ? 'approve' : 'reject';
                    currentId = this.dataset.id;
                    confirmMessage.innerHTML = actionType === 'approve' ?
                        'Bạn có chắc chắn muốn <strong class="text-success">DUYỆT</strong> đơn này không?' :
                        'Bạn có chắc chắn muốn <strong class="text-danger">TỪ CHỐI</strong> đơn này không?';
                    modal.show();
                });
            });

            confirmBtn.addEventListener('click', function() {
                if (!currentId || !actionType) return;
                fetch(`/landlord/bookings/${currentId}/${actionType}`, {
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
                            location.reload();
                        }
                    })
                    .catch(console.error);
                modal.hide();
            });
        });
    </script>
@endpush
