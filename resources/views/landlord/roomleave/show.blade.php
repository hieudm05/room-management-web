@extends('landlord.layouts.app')
@section('title', 'Chi tiết yêu cầu')

@section('content')
<div class="container mt-4">
    <h4>📋 Chi tiết yêu cầu</h4>

    <div class="card">
        <div class="card-body">
            <p>👤 Người thuê: <strong>{{ $request->user->name }}</strong></p>
            <p>🏠 Phòng: {{ $request->room->name }}</p>
            <p>📅 Ngày yêu cầu: {{ $request->leave_date }}</p>
            <p>📌 Loại: {{ $request->type === 'transfer' ? 'Nhượng quyền' : 'Rời phòng' }}</p>
            <p>📝 Ghi chú: {{ $request->reason ?? '(Không có)' }}</p>

            <form action="{{ route('landlord.roomleave.approve', $request->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-success">✅ Duyệt</button>
            </form>

            <a href="{{ route('landlord.roomleave.rejectForm', $request->id) }}" class="btn btn-danger ms-2">❌ Từ chối</a>
        </div>
    </div>
</div>
@endsection
