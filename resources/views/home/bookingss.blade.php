@extends('home.layouts.app')

@section('title', '📅 Lịch hẹn của tôi')

@section('content')
    <div class="container py-4">
        <h2 class="fw-bold text-primary mb-3">📅 Danh sách lịch hẹn</h2>

        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-bordered table-hover text-center bg-white">
                <thead class="table-primary">
                    <tr>
                        <th>STT</th>
                        <th>Phòng</th>
                        <th>Ngày hẹn</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $index => $booking)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $booking->room->room_number ?? '—' }}</td>
                            <td>{{ $booking->check_in->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'pending' => '⏳ Đang chờ',
                                        'approved' => '✅ Đã duyệt',
                                        'waiting' => '🕓 Đang đợi gặp',
                                        'completed' => '🏁 Hoàn thành',
                                        'rejected' => '❌ Từ chối',
                                    ];
                                @endphp
                                <span class="badge bg-info">{{ $statusLabels[$booking->status] ?? $booking->status }}</span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-3">Chưa có lịch hẹn nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
