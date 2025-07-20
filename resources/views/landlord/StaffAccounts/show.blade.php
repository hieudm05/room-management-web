@extends('landlord.layouts.app')

@section('title', 'Chi tiết nhân viên')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold">Thông tin nhân viên</h5>
                    <a href="{{ route('landlords.staff_accounts.index') }}" class="btn btn-success btn-sm">
                        Quay lại
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Họ tên:</strong> {{ $staff->name }}</p>
                <p><strong>Email:</strong> {{ $staff->email }}</p>
                <p><strong>CCCD:</strong> {{ $staff->identity_number }}</p>
                <strong class="badge {{ $staff->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $staff->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                </strong>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 text-white fw-bold">Danh sách phòng đang quản lý</h5>
            </div>
            <div class="card-body">
                @if ($staff->rooms->count())
                    <ul>
                        @foreach ($staff->rooms as $room)
                            <li>
                                {{ $room->room_number }} - Khu trọ: {{ $room->property->name ?? 'N/A' }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Nhân viên này chưa được phân quyền quản lý phòng nào.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
