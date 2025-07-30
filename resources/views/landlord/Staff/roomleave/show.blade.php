@extends('landlord.layouts.app')
@section('title', 'Chi tiết yêu cầu rời phòng')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">📄 Chi tiết yêu cầu rời phòng / nhượng quyền</h4>

    <div class="card shadow-sm border border-secondary-subtle">
        <div class="card-body">
            <p><strong>🧍 Người gửi:</strong> {{ $request->user->name }}</p>
            <p><strong>🏠 Phòng:</strong> {{ $request->room->name }}</p>
            <p><strong>📅 Ngày áp dụng:</strong> {{ $request->leave_date }}</p>
            <p><strong>📎 Loại yêu cầu:</strong> 
                <span class="badge bg-{{ $request->action_type === 'transfer' ? 'info' : 'secondary' }}">
                    {{ $request->action_type === 'transfer' ? 'Nhượng quyền' : 'Rời phòng' }}
                </span>
            </p>

            @if ($request->action_type === 'transfer')
                <p><strong>🔄 Người được nhượng:</strong> {{ $request->newRenter?->name ?? '(Chưa chỉ định)' }}</p>
            @endif

            <p><strong>📝 Ghi chú:</strong> {{ $request->reason ?? 'Không có' }}</p>

            <p><strong>📌 Trạng thái hiện tại:</strong>
                <span class="badge bg-warning text-dark">{{ ucfirst($request->status) }}</span>
            </p>

            <p><strong>📆 Ngày gửi yêu cầu:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>

            @if ($request->status === 'Pending')
                <div class="mt-4">
                    <form method="POST" action="{{ route('landlord.staff.roomleave.approve', $request->id) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-success">
                            ✅ Duyệt & Gửi cho chủ trọ
                        </button>
                    </form>

                    <a href="{{ route('landlord.staff.roomleave.index') }}" class="btn btn-secondary ms-2">
                        ⬅️ Quay lại danh sách
                    </a>
                </div>
            @else
                <a href="{{ route('landlord.staff.roomleave.index') }}" class="btn btn-outline-secondary mt-3">
                    ⬅️ Quay lại danh sách
                </a>
            @endif
        </div>
    </div>
</div>
@endsection