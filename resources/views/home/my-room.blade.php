@extends('home.layouts.app')

@section('title', 'Phòng của tôi')

@section('content')
<div class="container mt-4">

    <h3 class="mb-3">🏠 Thông tin phòng của bạn</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5><strong>Địa chỉ:</strong> {{ $room->property->address ?? 'Không rõ địa chỉ' }}</h5>
            <p><strong>Số người ở:</strong> {{ $room->people_renter }}</p>
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

       <!-- Modal QR -->
<div class="modal fade" id="qrModal{{ $bill->id }}" tabindex="-1" aria-labelledby="qrModalLabel{{ $bill->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header text-white">
                <h5 class="modal-title d-flex align-items-center" id="qrModalLabel{{ $bill->id }}">
                    🧾 Thanh Toán Hóa Đơn Tháng {{ $bill->month }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body">
                <div class="row align-items-center">
                    <!-- Bên trái: Thông tin ngân hàng -->
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
                            <p class="text-danger fs-5 fw-bold">{{ number_format($bill->total) }} đ</p>
                        </div>
                    </div>

                    <!-- Bên phải: Mã QR -->
                    <div class="col-md-6 text-center">
                      @if ($bill->bankAccount)
                @php
                    $bankCode = $bill->bankAccount->bank_code; // Ví dụ: TPB, VCB, TCB...
                    $accountNumber = $bill->bankAccount->bank_account_number;
                    $accountName = urlencode($bill->bankAccount->bank_account_name);
                    $amount = number_format($bill->total, 2, '.', '');
                    $addInfo = urlencode('Thanh toan hoa don ' . $bill->month);
                @endphp

                <img src="https://img.vietqr.io/image/{{ $bankCode }}-{{ $accountNumber }}-compact2.png?amount={{ $amount }}&addInfo={{ $addInfo }}&accountName={{ $accountName }}"
                    alt="QR Code" class="img-fluid rounded shadow border">

                <p class="mt-2 text-muted"><small>📷 Quét mã để thanh toán tự động</small></p>
            @endif

                    </div>
                </div>

                <!-- Form xác nhận thanh toán -->
                <form action="{{ route('bills.markPending', $bill->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            {{-- <label for="payment_time_{{ $bill->id }}" class="form-label">🕒 Thời gian thanh toán</label> --}}
                            <input type="datetime-local" id="payment_time_{{ $bill->id }}" hidden name="payment_time" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="receipt_image_{{ $bill->id }}" class="form-label">📎 Ảnh chụp biên lai</label>
                            <input type="file" id="receipt_image_{{ $bill->id }}" name="receipt_image" class="form-control" accept="image/*" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                             Tôi đã thanh toán
                        </button>
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

</div>
@endsection
