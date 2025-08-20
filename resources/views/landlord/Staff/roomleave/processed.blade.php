@extends('landlord.layouts.app')
@section('title', 'Yêu cầu đã xử lý')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">📋 Danh sách yêu cầu rời phòng / chuyển hợp đồng đã xử lý</h4>

    @forelse ($processedLeaves as $req)
        <div class="card mb-3 border-start border-4 {{ $req->status === 'approved' ? 'border-success' : 'border-danger' }} shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">🧍 {{ $req->user->name }}</h5>
                    <span class="badge bg-{{ $req->status === 'approved' ? 'success' : 'danger' }}">
                        {{ $req->status === 'approved' ? 'Đã duyệt' : 'Từ chối' }}
                    </span>
                </div>

                <p class="mb-1">🏠 <strong>Phòng:</strong> {{ $req->room->room_number ?? $req->room->name }}</p>
                <p class="mb-1">📌 <strong>Loại yêu cầu:</strong> {{ $req->action_type === 'transfer' ? 'Chuyển hợp đồng' : 'Rời phòng' }}</p>
                <p class="mb-1">📅 <strong>Ngày yêu cầu:</strong> {{ \Carbon\Carbon::parse($req->leave_date)->format('d/m/Y') }}</p>
                <p class="mb-1">🕒 <strong>Xử lý lúc:</strong> {{ \Carbon\Carbon::parse($req->handled_at)->format('d/m/Y H:i') }}</p>

                @if ($req->status === 'rejected')
                    <p class="mt-2 text-danger">📝 <strong>Lý do từ chối:</strong> <em>{{ $req->reject_reason }}</em></p>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">Chưa có yêu cầu nào được xử lý.</div>
    @endforelse

    {{-- Nếu có phân trang --}}
    {{-- <div class="mt-3"> {{ $requests->links() }} </div> --}}
</div>
@endsection