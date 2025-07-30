@extends('home.layouts.app')
@section('title', 'Chi tiết yêu cầu rời phòng')

@section('content')
<div class="container mt-4">
    <h3>📄 Chi tiết yêu cầu rời phòng</h3>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <p><strong>📅 Ngày rời:</strong> {{ $request->leave_date }}</p>
            <p><strong>📝 Lý do:</strong> {{ $request->reason ?? 'Không có' }}</p>
            <p><strong>⚙️ Loại hành động:</strong> 
                @if ($request->action_type == 'transfer')
                    Nhượng quyền hợp đồng cho: {{ $request->newRenter->name ?? '[Không xác định]' }}
                @elseif ($request->action_type == 'terminate')
                    Kết thúc hợp đồng
                @else
                    Thành viên rời phòng
                @endif
            </p>
            <p><strong>🏠 Phòng:</strong> {{ $request->room->name ?? 'N/A' }}</p>
            <p><strong>📍 Bất động sản:</strong> {{ $request->room->property->name ?? 'N/A' }}</p>
            <p><strong>🕒 Trạng thái:</strong> {{ $request->status }}</p>
        </div>
    </div>

    <a href="{{ route('home.roomleave.stopRentForm') }}" class="btn btn-secondary mt-3">⬅️ Quay lại</a>
</div>
@endsection