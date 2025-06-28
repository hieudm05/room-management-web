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
            <p><strong>Trạng thái:</strong> {{ $room->status == 1 ? 'Đang cho thuê' : 'Ngừng hoạt động' }}</p>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel{{ $bill->id }}">🧾 QR Thanh Toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tên tài khoản:</strong> NGUYEN TRONG MINH</p>
                <p><strong>Số tài khoản:</strong> 17777711112005</p>
                <p><strong>Ngân hàng:</strong> MB Bank</p>
                <p><strong>Số tiền:</strong> {{ number_format($bill->total) }} đ</p>
                <img src="https://img.vietqr.io/image/970422-17777711112005-compact2.png?amount={{ $bill->total }}&addInfo=Thanh+toan+hoa+don+{{ $bill->month }}&accountName=NGUYEN+TRONG+MINH" alt="QR Code" class="img-fluid rounded shadow">
                <p class="mt-2 text-muted"><small>Quét mã để thanh toán tự động</small></p>

                <!-- Nút Tôi đã thanh toán -->
                <form action="{{ route('bills.markPending', $bill->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary mt-3">Tôi đã thanh toán</button>
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
