@extends('home.layouts.app')
@section('title', 'Chi tiết yêu cầu rời phòng')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">📄 Chi tiết yêu cầu rời phòng</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Ngày gửi yêu cầu --}}
            <p>
                <strong>📨 Ngày gửi yêu cầu:</strong>
                {{ $request->created_at->format('d/m/Y H:i') }}
            </p>

            {{-- Ngày áp dụng --}}
            <p>
                <strong>📅 Ngày áp dụng:</strong>
                {{ \Carbon\Carbon::parse($request->leave_date)->format('d/m/Y') }}
            </p>

            {{-- Phòng và Tòa --}}
            <p>
                <strong>🏢 Phòng:</strong>
                {{ $request->room->room_number ?? 'Không rõ' }}
                @if(optional($request->room->property)->name)
                    – {{ $request->room->property->name }}
                @endif
            </p>

            {{-- Lý do --}}
            <p>
                <strong>📝 Lý do:</strong>
                {{ $request->note ?: 'Không có' }}
            </p>

            {{-- Loại hành động --}}
            <p>
                <strong>⚙️ Loại hành động:</strong>
                @switch($request->action_type)
                    @case('transfer')
                        🔄 Nhượng hợp đồng cho: {{ $request->newRenter->name ?? '[Không xác định]' }}
                        @break

                    @case('leave')
                        🚪 Rời khỏi phòng
                        @break

                    @default
                        ❓ Không rõ loại hành động
                @endswitch
            </p>

            {{-- Trạng thái --}}
            <p>
                <strong>🕒 Trạng thái:</strong>
                @switch(strtolower($request->status))
                    @case('pending')  <span class="text-warning">⏳ Đang chờ duyệt</span> @break
                    @case('approved') <span class="text-success">✅ Đã được duyệt</span> @break
                    @case('rejected') <span class="text-danger">❌ Bị từ chối</span> @break
                    @default           <span class="text-muted">Không xác định</span>
                @endswitch
            </p>
        </div>
    </div>

    {{-- Nút hủy nếu đang chờ --}}
    @if ($request->status === 'Pending')
        <form method="POST" action="{{ route('home.roomleave.cancelRequest', $request->id) }}" class="mt-3">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">❌ Huỷ yêu cầu</button>
        </form>
    @endif

    {{-- Quay lại --}}
    <a href="{{ route('home.roomleave.stopRentForm') }}" class="btn btn-secondary mt-3">⬅️ Quay lại danh sách</a>
</div>
@endsection