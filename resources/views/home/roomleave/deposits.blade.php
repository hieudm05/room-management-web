@extends('home.layouts.app')

@section('title', 'Lịch sử tiền cọc')

@section('content')
<div class="container mt-4">
    <h3>💰 Lịch sử hoàn cọc</h3>

    @if($refunds->isEmpty())
        <div class="alert alert-info">Chưa có lịch sử hoàn cọc nào.</div>
    @else
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Phòng</th>
                    <th>Tòa nhà</th>
                    <th>Số tiền</th>
                    <th>Ngày hoàn</th>
                    <th>Trạng thái</th>
                    <th>Ảnh bằng chứng</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($refunds as $refund)
                    <tr>
                        <td>{{ optional(optional($refund->rental)->room)->room_number ?? 'N/A' }}</td>
                        <td>{{ optional(optional(optional($refund->rental)->room)->property)->name ?? 'N/A' }}</td>
                        <td>{{ number_format($refund->amount ?? 0) }} VNĐ</td>
                        <td>{{ $refund->refund_date ? \Carbon\Carbon::parse($refund->refund_date)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if ($refund->status === 'pending')
                                <span class="text-warning">⏳ Chờ xử lý</span>
                            @elseif ($refund->status === 'refunded' || $refund->status === 'completed')
                                <span class="text-success">✅ Đã hoàn</span>
                            @elseif ($refund->status === 'not_refunded')
                                <span class="text-danger">❌ Không hoàn</span>
                            @else
                                <span class="text-secondary">❔ Không rõ</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($refund->proof_image))
                                <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">
                                    <img src="{{ Storage::url($refund->proof_image) }}" 
                                         alt="Proof" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px;">
                                </a>
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                        <td>
                            {{ $refund->reason ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection