@extends('home.layouts.app')
@section('title', 'Thành viên phòng')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">🧑‍🤝‍🧑 Thành viên trong phòng</h3>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Chủ hợp đồng --}}
    @if ($room->rentalAgreement)
        @php $renter = $room->rentalAgreement->renter; @endphp
        <div class="alert alert-primary">
            <strong>Chủ hợp đồng:</strong> {{ $renter->name }} ({{ $renter->email }})
        </div>
    @endif

    {{-- Danh sách thành viên --}}
    @foreach ($room->userInfos as $info)
       @php
    $user = $info->user;
    $leaveRequest = $leaveRequests[$user->id] ?? null;
@endphp

        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $user->name }}</strong> - {{ $user->email }}<br>
                    📱 {{ $info->phone_number ?? 'Chưa có SĐT' }}<br>
                    🏠 CCCD: {{ $info->identity_number ?? 'Chưa cập nhật' }}
                </div>

                @if ($user->id == $currentUserId)
                    @if ($isContractOwner)
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#terminateContractModal">
                            🛑 Kết thúc/nhượng hợp đồng
                        </button>
                    @else
                        @if ($leaveRequest)
                            <div class="d-flex gap-2">
                                <a href="{{ route('home.roomleave.viewRequest', $leaveRequest->id) }}" class="btn btn-info btn-sm">👁️ Xem yêu cầu</a>
                                <form method="POST" action="{{ route('home.roomleave.cancelRequest', $leaveRequest->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-secondary btn-sm" type="submit">❌ Huỷ yêu cầu</button>
                                </form>
                            </div>
                        @else
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#leaveModal-{{ $user->id }}">
                                🛑 Dừng thuê
                            </button>
                        @endif
                    @endif
                @endif
            </div>
        </div>

        {{-- Modal dừng thuê --}}
        @if ($user->id == $currentUserId && !$isContractOwner)
        <div class="modal fade" id="leaveModal-{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('home.roomleave.send') }}">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="modal-header">
                            <h5 class="modal-title">Xác nhận rời phòng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label>📅 Ngày rời đi</label>
                            <input type="date" name="leave_date" class="form-control" required>

                            <label class="mt-3">📝 Lý do (tuỳ chọn)</label>
                            <textarea name="reason" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                            <button class="btn btn-danger" type="submit">Gửi yêu cầu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    {{-- Danh sách yêu cầu rời phòng đã gửi --}}
    @if ($leaveRequests->count())
        <h4 class="mt-5">📤 Yêu cầu rời phòng đã gửi</h4>

        @foreach ($leaveRequests as $req)
            @php
                $user = $room->userInfos->where('user_id', $req->user_id)->first()?->user ?? null;
            @endphp

            @if ($user)
            <div class="card mb-3 border-warning shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $user->name }}</strong>
                        @if ($user->id == $currentUserId)
                            (Bạn)
                        @endif
                        <br>
                        📅 <strong>Ngày rời:</strong> {{ \Carbon\Carbon::parse($req->leave_date)->format('d/m/Y') }}<br>
                        📝 <strong>Lý do:</strong> {{ $req->reason ?? 'Không có' }}<br>
                        ⏳ <strong>Trạng thái:</strong> 
                       @switch(strtolower($req->status))
    @case('pending')
        <span class="text-warning">Đang chờ</span>
        @break
    @case('approved')
        <span class="text-success">Đã duyệt</span>
        @break
    @case('rejected')
        <span class="text-danger">Từ chối</span>
        @break
    @default
        <span class="text-muted">Không rõ</span>
@endswitch
                    </div>

                    @if ($req->user_id == $currentUserId && $req->status === 'Pending')
                        <form method="POST" action="{{ route('home.leave.cancel', $req->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">❌ Hủy</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        @endforeach
    @endif
</div>

{{-- Modal cho chủ hợp đồng --}}
@if ($isContractOwner)
<div class="modal fade" id="terminateContractModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('home.roomleave.send') }}">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">🛑 Kết thúc hoặc Nhượng hợp đồng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-warning">Bạn là <strong>chủ hợp đồng</strong>. Vui lòng chọn hành động:</p>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="action_type" value="terminate" id="terminateOption" checked>
                        <label class="form-check-label" for="terminateOption">🔚 Kết thúc hợp đồng</label>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="action_type" value="transfer" id="transferOption">
                        <label class="form-check-label" for="transferOption">🔄 Nhượng quyền cho người khác</label>
                    </div>

                    <div class="mt-3" id="transferTarget" style="display: none;">
                        <label>📋 Chọn người nhận quyền</label>
                        <select name="new_renter_id" class="form-select">
                            @foreach ($room->userInfos as $info)
                                @if ($info->user->id !== $currentUserId)
                                    <option value="{{ $info->user->id }}">{{ $info->user->name }} ({{ $info->user->email }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <label class="mt-3">📅 Ngày áp dụng</label>
                    <input type="date" name="terminate_date" class="form-control" required>

                    <label class="mt-3">📝 Ghi chú (tuỳ chọn)</label>
                    <textarea name="reason" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                    <button class="btn btn-warning" type="submit">Xác nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS xử lý chuyển nhượng --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const transferOption = document.getElementById('transferOption');
        const transferTarget = document.getElementById('transferTarget');
        const terminateOption = document.getElementById('terminateOption');

        transferOption.addEventListener('change', () => {
            transferTarget.style.display = 'block';
        });

        terminateOption.addEventListener('change', () => {
            transferTarget.style.display = 'none';
        });
    });
</script>
@endif
@endsection