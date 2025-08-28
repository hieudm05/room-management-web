@extends('home.layouts.app')

@section('title', 'Phòng của tôi')

@section('content')
    <div class="container mt-4 content-wrapper">
        <style>
            .content-wrapper {
                min-height: 100%;
            }
        </style>

        @if ($hasLeftRoom)
            <div class="alert alert-info">
                ⚠️ Bạn đã rời khỏi phòng này. Bạn vẫn có thể xem lại các hóa đơn cũ.
            </div>
        @else
            @if ($room)
                <h2>🏡 Thông tin phòng của bạn</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><strong>Địa chỉ:</strong> {{ $room->property->address ?? 'Không rõ địa chỉ' }}</h5>
                        <p><strong>Số người ở:</strong> {{ $room->currentUserInfos->count() }} người</p>
                        <p><strong>Diện tích:</strong> {{ $room->area }} m²</p>
                        <p><strong>Trạng thái:</strong>
                            {{ $room->status === 'Rented' ? 'Đang cho thuê' : 'Ngừng hoạt động' }}</p>
                    </div>
                </div>
                <a href="{{ route('home.roomleave.stopRentForm', ['room_id' => $room->room_id]) }}"
                    class="btn btn-outline-primary mb-3">
                    👥 Xem thành viên phòng
                </a>
            @endif
            @if ($hasRenewalPending)
                <div class="alert alert-info">
                    🔁 Đang chờ quản lý để tái ký hợp đồng.
                </div>
            @elseif ($alert)
                <div class="alert alert-{{ $alertType ?? 'warning' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>{{ $alert }}</div>
                    </div>
                </div>
            @endif

            {{-- Cảnh báo đóng tiền hóa đơn nếu chưa đóng tiền --}}
            @if ($showBillReminder)
                <div class="alert alert-{{ $billReminderType }}">
                    @if ($billReminderType === 'danger')
                        😠 <strong>Lưu ý:</strong> Bạn chưa thanh toán hóa đơn tháng này. Vui lòng thanh toán sớm!
                    @else
                        ⚠️ <strong>Nhắc nhở:</strong> Hóa đơn tháng này chưa được thanh toán.
                    @endif
                </div>
            @endif

            @if (!empty($showRenewButtons))
                <div class="mt-3 d-flex">
                    <form method="POST" action="{{ route('client.contract.renew', ['room' => $room->room_id]) }}"
                        class="me-2">
                        @csrf
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn btn-success btn-sm">🔁 Tái ký hợp đồng</button>
                    </form>
                    <form method="POST" action="{{ route('client.contract.renew', ['room' => $room->room_id]) }}">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-danger btn-sm">❌ Từ chối</button>
                    </form>
                </div>
            @endif
        @endif


        <h4>📄 Hóa đơn</h4>

        @if ($bills->isEmpty())
            <p class="text-muted">Chưa có hóa đơn nào.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Tiền phòng</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                            <tr>
                                <td>{{ $bill->month->format('m/Y')  }}</td>
                                <td>{{ number_format($bill->total) }} đ</td>
                                <td>
                                    @php
                                        $statusLabel = match ($bill->status) {
                                            'paid' => ['text' => 'Đã thanh toán', 'class' => 'bg-success'],
                                            'pending' => ['text' => 'Chờ xác nhận', 'class' => 'bg-info'],
                                            default => ['text' => 'Chưa thanh toán', 'class' => 'bg-warning'],
                                        };
                                    @endphp
                                    <span class="badge {{ $statusLabel['class'] }}">{{ $statusLabel['text'] }}</span>
                                </td>
                                <td>{{ $bill->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if (!$bill->is_paid)
                                        <button class="btn btn-sm btn-outline-primary mb-1" data-bs-toggle="modal"
                                            data-bs-target="#qrModal{{ $bill->id }}">
                                            Thanh toán
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $bill->id }}">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal QR --}}
                            <div class="modal fade" id="qrModal{{ $bill->id }}" tabindex="-1"
                                aria-labelledby="qrModalLabel{{ $bill->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content shadow-lg border-0">
                                        <div class="modal-header text-white">
                                            <h5 class="modal-title d-flex align-items-center"
                                                id="qrModalLabel{{ $bill->id }}">
                                                🧾 Thanh Toán Hóa Đơn Tháng {{ $bill->month }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <h6><strong>Tên tài khoản:</strong></h6>
                                                        <p>{{ $bill->bankAccount->bank_account_name ?? '---' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6><strong>Số tài khoản:</strong></h6>
                                                        <p>{{ $bill->bankAccount->bank_account_number ?? '---' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6><strong>Ngân hàng:</strong></h6>
                                                        <p>{{ $bill->bankAccount->bank_name ?? '---' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6><strong>Số tiền:</strong></h6>
                                                        <p class="text-danger fs-5 fw-bold">
                                                            {{ number_format($bill->total) }} đ
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    @if ($bill->bankAccount)
                                                        @php
                                                            $bankCode = $bill->bankAccount->bank_code;
                                                            $accountNumber = $bill->bankAccount->bank_account_number;
                                                            $accountName = urlencode(
                                                                $bill->bankAccount->bank_account_name,
                                                            );
                                                            $amount = number_format($bill->total, 2, '.', '');
                                                            $addInfo = urlencode('Thanh toan hoa don ' . $bill->month);
                                                        @endphp
                                                        <img src="https://img.vietqr.io/image/{{ $bankCode }}-{{ $accountNumber }}-compact2.png?amount={{ $amount }}&addInfo={{ $addInfo }}&accountName={{ $accountName }}"
                                                            alt="QR Code" class="img-fluid rounded shadow border">
                                                        <p class="mt-2 text-muted"><small>📷 Quét mã để thanh toán tự
                                                                động</small></p>
                                                    @endif
                                                </div>
                                            </div>
                                            <form action="{{ route('bills.markPending', $bill->id) }}" method="POST"
                                                enctype="multipart/form-data" class="mt-4">
                                                @csrf
                                                <input type="datetime-local" id="payment_time_{{ $bill->id }}" hidden
                                                    name="payment_time" class="form-control" required>
                                                <div class="col-md-12 mb-3">
                                                    <label for="receipt_image_{{ $bill->id }}" class="form-label">📎
                                                        Ảnh chụp biên lai</label>
                                                    <input type="file" id="receipt_image_{{ $bill->id }}"
                                                        name="receipt_image" class="form-control" accept="image/*" required>
                                                </div>
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">Tôi đã thanh
                                                        toán</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Chi tiết --}}
                            <div class="modal fade" id="detailModal{{ $bill->id }}" tabindex="-1"
                                aria-labelledby="detailModalLabel{{ $bill->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                                        <div class="modal-header bg-gradient text-white"
                                            style="background: linear-gradient(135deg, #3b82f6, #06b6d4);">
                                            <div>
                                                <h5 class="modal-title fw-bold" id="detailModalLabel{{ $bill->id }}">🧾
                                                    Hóa Đơn Tháng {{ $bill->month->format('m/Y') }}</h5>
                                                <small class="text-light">Mã HĐ: #{{ $bill->id }}</small>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div
                                                        class="p-4 bg-white rounded-4 shadow-sm h-100 border border-light-subtle">
                                                        <h6 class="text-muted mb-3">📋 Thông tin chi tiết</h6>
                                                        @php
                                                            $items = [
                                                                '📌 Tiền phòng' => $bill->rent_price,
                                                                '⚡ Điện' => $bill->electric_total,
                                                                '🚿 Nước' => $bill->water_total,
                                                                '🛠️ Dịch vụ' => $bill->complaint_landlord_cost,
                                                            ];
                                                        @endphp
                                                        @foreach ($items as $label => $value)
                                                            <div
                                                                class="d-flex justify-content-between border-bottom border-dashed py-2">
                                                                <span>{{ $label }}</span>
                                                                <span
                                                                    class="fw-semibold text-dark">{{ number_format($value ?? 0) }}
                                                                    đ</span>
                                                            </div>
                                                        @endforeach
                                                        <div class="d-flex justify-content-between border-top pt-3 mt-3">
                                                            <span class="fw-bold fs-5 text-danger">💰 Tổng cộng:</span>
                                                            <span
                                                                class="fw-bold fs-5 text-danger">{{ number_format($bill->total ?? 0) }}
                                                                đ</span>
                                                        </div>
                                                        @if ($bill->utilityPhotos && $bill->utilityPhotos->isNotEmpty())
                                                            <div class="mt-4">
                                                                <strong>🖼️ Biên lai điện nước:</strong>
                                                                <div class="row">
                                                                    @foreach ($bill->utilityPhotos as $photo)
                                                                        <div class="col-md-6 mt-2">
                                                                            <div
                                                                                class="ratio ratio-4x3 rounded border shadow-sm overflow-hidden">
                                                                                <img src="{{ asset('storage/' . $photo->image_path) }}"
                                                                                    alt="Biên lai"
                                                                                    class="w-100 h-100 object-fit-cover">
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div
                                                        class="p-4 bg-white rounded-4 shadow-sm h-100 border border-light-subtle">
                                                        <h6 class="text-muted mb-3">📆 Trạng thái thanh toán</h6>
                                                        <p class="mb-2">
                                                            <strong>Khoảng ngày:</strong><br>{{ $bill->created_at->format('d/m/Y') }} - {{ $bill->created_at->format('d/m/Y') }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Trạng thái:</strong><br>
                                                            @php
                                                                $status = match ($bill->status) {
                                                                    'paid' => ['Đã thanh toán ✅', 'bg-success'],
                                                                    'pending' => [
                                                                        'Chờ xác nhận ⏳',
                                                                        'bg-warning text-dark',
                                                                    ],
                                                                    default => ['Chưa thanh toán ❌', 'bg-secondary'],
                                                                };
                                                            @endphp
                                                            <span
                                                                class="badge {{ $status[1] }} rounded-pill px-3 py-2">{{ $status[0] }}</span>
                                                        </p>
                                                        @if ($bill->payment_time)
                                                            <p class="mb-2">
                                                                <strong>🕒 Thời gian
                                                                    TT:</strong><br>{{ \Carbon\Carbon::parse($bill->payment_time)->format('d/m/Y H:i') }}
                                                            </p>
                                                        @endif
                                                        @if ($bill->receipt_image)
                                                            <div class="mt-4">
                                                                <strong>🖼️ Biên lai thanh toán:</strong>
                                                                <div
                                                                    class="ratio ratio-4x3 rounded border shadow-sm mt-2 overflow-hidden">
                                                                    <img src="{{ asset('storage/' . $bill->receipt_image) }}"
                                                                        alt="Ảnh biên lai"
                                                                        class="w-100 h-100 object-fit-cover">
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="text-muted mt-4">⚠️ Chưa có biên lai thanh toán.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modals = document.querySelectorAll('[id^="qrModal"]');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function() {
                    const billId = this.id.replace('qrModal', '');
                    const input = document.querySelector(`#payment_time_${billId}`);
                    if (input) {
                        const now = new Date();
                        const formatted = now.toISOString().slice(0, 16);
                        input.value = formatted;
                    }
                });
            });
        });
    </script>
@endsection
