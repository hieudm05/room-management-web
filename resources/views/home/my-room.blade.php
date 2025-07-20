@extends('home.layouts.app')

@section('title', 'Phòng của tôi')

@section('content')
<div class="container mt-4">

    <h3 class="mb-3">🏠 Thông tin phòng của bạn</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5><strong>Địa chỉ:</strong> {{ $room->property->address ?? 'Không rõ địa chỉ' }}</h5>
            <p><strong>Số người ở:</strong> {{ $room->occupants }}</p>
            <p><strong>Diện tích:</strong> {{ $room->area }} m²</p>
            <p><strong>Trạng thái:</strong> {{ $room->status === "Rented" ? 'Đang cho thuê' : 'Ngừng hoạt động' }}</p>
        </div>
    </div>

    <h4>📄 Hóa đơn</h4>

    @if($bills->isEmpty())
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
                    @foreach($bills as $bill)
                        <tr>
                            <td>{{ $bill->month }}</td>
                            <td>{{ number_format($bill->total) }} đ</td>
                            <td>
                                @php
                                    $statusLabel = match($bill->status) {
                                        'paid' => ['text' => 'Đã thanh toán', 'class' => 'bg-success'],
                                        'pending' => ['text' => 'Chờ xác nhận', 'class' => 'bg-info'],
                                        default => ['text' => 'Chưa thanh toán', 'class' => 'bg-warning'],
                                    };
                                @endphp

                                <span class="badge {{ $statusLabel['class'] }}">
                                    {{ $statusLabel['text'] }}
                                </span>
                            </td>
                            <td>{{ $bill->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if (!$bill->is_paid)
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $bill->id }}">
                                        Thanh toán
                                    </button>

                                    {{-- Modal QR --}}
                                    <div class="modal fade" id="qrModal{{ $bill->id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $bill->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content shadow-lg border-0">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title" id="qrModalLabel{{ $bill->id }}">
                                                        🧾 Thanh Toán Hóa Đơn Tháng {{ $bill->month }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row align-items-center">
                                                        {{-- Thông tin ngân hàng --}}
                                                        <div class="col-md-6">
                                                            <p><strong>Tên tài khoản:</strong><br> {{ $bill->bankAccount->bank_account_name ?? '---' }}</p>
                                                            <p><strong>Số tài khoản:</strong><br> {{ $bill->bankAccount->bank_account_number ?? '---' }}</p>
                                                            <p><strong>Ngân hàng:</strong><br> {{ $bill->bankAccount->bank_name ?? '---' }}</p>
                                                            <p><strong>Số tiền:</strong><br> <span class="text-danger fs-5 fw-bold">{{ number_format($bill->total) }} đ</span></p>
                                                        </div>

                                                        {{-- QR --}}
                                                        <div class="col-md-6 text-center">
                                                            @if ($bill->bankAccount)
                                                                <img src="https://img.vietqr.io/image/{{ urlencode($bill->bankAccount->bank_name) }}-{{ $bill->bankAccount->bank_account_number }}-compact2.png?amount={{ $bill->total }}&addInfo=Thanh+toan+hoa+don+{{ $bill->month }}&accountName={{ urlencode($bill->bankAccount->bank_account_name) }}"
                                                                     alt="QR Code" class="img-fluid rounded shadow border">
                                                                <p class="mt-2 text-muted"><small>📷 Quét mã để thanh toán tự động</small></p>
                                                            @else
                                                                <p class="text-danger">⚠️ Chưa cấu hình tài khoản ngân hàng</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Form xác nhận --}}
                                                    <form action="{{ route('bills.markPending', $bill->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="payment_time_{{ $bill->id }}">🕒 Thời gian thanh toán</label>
                                                                <input type="datetime-local" id="payment_time_{{ $bill->id }}" name="payment_time" class="form-control" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="receipt_image_{{ $bill->id }}">📎 Ảnh chụp biên lai</label>
                                                                <input type="file" id="receipt_image_{{ $bill->id }}" name="receipt_image" class="form-control" accept="image/*" required>
                                                            </div>
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-primary">Tôi đã thanh toán</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-success">✔</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <hr class="my-4">

    <h4>👥 Danh sách người đang ở phòng</h4>

    @forelse ($room->roomUsers as $roomUser)
        <div class="border rounded p-3 mb-3 shadow-sm">
            <p><strong>👤 Họ tên:</strong> {{ $roomUser->name }}</p>
            <p><strong>📧 Email:</strong> {{ $roomUser->email }}</p>
            <p><strong>📱 SĐT:</strong> {{ $roomUser->phone }}</p>
            <p><strong>🆔 CCCD:</strong> {{ $roomUser->cccd }}</p>

            @if ($roomUser->is_active)
                <form method="POST" action="{{ route('room-users.stop', $roomUser->id) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Số tiền khấu trừ (VNĐ)</label>
                            <input type="number" name="deduction_amount" class="form-control" min="0" placeholder="VD: 500000">
                        </div>
                        <div class="col-md-8 mb-2">
                            <label>Lý do khấu trừ</label>
                            <input type="text" name="deduction_reason" class="form-control" placeholder="VD: Làm hỏng đồ đạc...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger mt-2" onclick="return confirm('Xác nhận dừng thuê người này?')">
                        Dừng thuê
                    </button>
                </form>
            @else
                <p class="text-danger"><strong>Đã dừng thuê lúc:</strong> {{ $roomUser->stopped_at }}</p>
                @if ($roomUser->deduction_amount > 0)
                    <p><strong>Khấu trừ:</strong> {{ number_format($roomUser->deduction_amount) }} đ</p>
                    <p><strong>Lý do:</strong> {{ $roomUser->deduction_reason }}</p>
                @endif
                <p><strong>Số tiền hoàn lại:</strong> {{ number_format($roomUser->returned_amount) }} đ</p>
            @endif
        </div>
    @empty
        <p class="text-muted">Chưa có người thuê nào được ghi nhận trong phòng này.</p>
    @endforelse

</div>
@endsection
