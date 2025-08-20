@extends('landlord.layouts.app')
@section('title', 'Yêu cầu đã xử lý')

@section('content')
<div class="container mt-4">
    <h4>📁 Danh sách yêu cầu rời phòng / chuyển hợp đồng đã xử lý</h4>

    @forelse ($requests as $req)
        <div class="card mb-3 border border-success-subtle">
            <div class="card-body">
                <p>🧍 Người thuê: <strong>{{ $req->user->name }}</strong></p>
                <p>🏠 Phòng: {{ $req->room->room_number ?? $req->room->name }}</p>
                <p>📌 Loại: {{ $req->action_type === 'transfer' ? 'Chuyển hợp đồng' : 'Rời phòng' }}</p>
                <p>📅 Ngày yêu cầu: {{ $req->leave_date }}</p>
                <p>📅 Xử lý lúc: {{ $req->handled_at }}</p>
                <p>
                    ⚙️ Trạng thái:
                    @if ($req->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @else
                        <span class="badge bg-danger">Bị từ chối</span>
                        <br>📝 Lý do từ chối: <em>{{ $req->reject_reason }}</em>
                    @endif
                </p>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">Chưa có yêu cầu nào được xử lý.</div>
    @endforelse
</div>
@endsection