
@extends('landlord.layouts.app')
@section('title', 'Quản lý yêu cầu rời phòng')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">📋 Danh sách yêu cầu rời phòng / nhượng quyền đang chờ xử lý</h3>

    @forelse ($requests as $req)
        <div class="card mb-3 shadow-sm border border-secondary-subtle">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1">
                            🧍 <strong>{{ $req->user->name }}</strong> gửi yêu cầu 
                            <span class="badge bg-info text-dark">{{ $req->action_type === 'transfer' ? 'Nhượng quyền' : 'Rời phòng' }}</span>
                        </p>
                        <p class="mb-1">🏠 Phòng: <strong>{{ $req->room->room_number }}</strong></p>
                        <p class="mb-1">🏢 Tòa nhà: <strong>{{ $req->room->property?->name ?? 'Không xác định' }}</strong></p>
                        <p class="mb-1">📅 Ngày áp dụng: <strong>{{ $req->leave_date }}</strong></p>
                        <p class="mb-1">📝 Ghi chú: {{ $req->note ?? 'Không có ghi chú' }}</p>
                        <p class="mb-1">🔄 Trạng thái: <strong>{{ $req->status }}</strong></p>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('landlord.staff.roomleave.show', $req->id) }}" class="btn btn-outline-primary btn-sm mb-2">
                            🔍 Xem chi tiết
                        </a>

                        {{-- Nếu nhân viên chưa duyệt --}}
                        @if ($req->staff_status === 'Pending')
                            <form method="POST" action="{{ route('landlord.staff.roomleave.approve', $req->id) }}">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Bạn chắc chắn muốn duyệt và gửi cho chủ trọ?')">
                                    ✅ Duyệt & Gửi chủ trọ
                                </button>
                            </form>
                        @endif

                        {{-- Nếu chủ trọ đã duyệt và chờ nhân viên xác nhận --}}
                        @if ($req->staff_status === 'Approved' && $req->status === 'approved')
                            <form method="POST" action="{{ route('landlord.staff.roomleave.finalize', $req->id) }}" class="mt-2">
                                @csrf
                                <button class="btn btn-warning btn-sm" onclick="return confirm('Xác nhận hoàn tất yêu cầu này?')">
                                    ✅ Xác nhận hoàn tất
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Hiện không có yêu cầu nào đang chờ xử lý.
        </div>
    @endforelse
</div>
@endsection
