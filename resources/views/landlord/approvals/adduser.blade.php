@extends('landlord.layouts.app')

@section('title', 'Duyệt yêu cầu thêm người')

@section('content')
@if (session('success'))
<script>
    window.onload = function() {
        alert("{{ session('success') }}");
    };
</script>
@endif

@if (session('error'))
<div class="alert alert-danger mt-2">{{ session('error') }}</div>
@endif

<div class="col-xl-12">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title mb-0">📋 Danh sách yêu cầu thêm người vào phòng</h4>
        </div>

        <div class="card-body">
            @forelse ($pendingApprovals as $approval)
            @php
            preg_match('/Tên:\s*(.*?)\s*\|\s*Email:\s*(.*)/', $approval->note, $matches);
            $fullName = $matches[1] ?? 'Không rõ';
            $email = $matches[2] ?? 'Không rõ';
            $room = $approval->room;
            @endphp

            <div class="card mb-3 border shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-primary">Phòng: {{ $room->room_number }} - {{ $room->property->name }}</h5>
                    <p><strong>Họ tên:</strong> {{ $fullName }}</p>
                    <p><strong>Email:</strong> {{ $email }}</p>
                    <p><strong>Ngày yêu cầu:</strong> {{ $approval->created_at->format('d/m/Y H:i') }}</p>

                    <form action="{{ route('landlords.approvals.users.approve', $approval->id) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">✅ Duyệt</button>
                    </form>

                    <form action="{{ route('landlords.approvals.users.reject', $approval->id) }}" method="POST" class="d-inline-block"
                        onsubmit="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">❌ Từ chối</button>
                    </form>

                </div>
            </div>
            @empty
            <div class="alert alert-warning text-center">
                Không có yêu cầu nào đang chờ duyệt.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
