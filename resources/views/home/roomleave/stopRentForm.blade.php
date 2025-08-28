@extends('home.layouts.app')
@section('title', 'Thành viên phòng')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="container mt-4">
        <h3 class="mb-4">🧑‍🤝‍🧑 Thành viên trong phòng</h3>
        @if (isset($incomingTransferRequest) && $incomingTransferRequest->status === 'waiting_new_renter_accept')
            <!-- form hiển thị chuyển nhượng -->

            <div class="alert alert-info shadow-sm p-4 mb-4">
                <h5 class="mb-3">📋 Yêu cầu chuyển nhượng hợp đồng đến bạn</h5>
                <p>🧍 Người nhượng: <strong>{{ $incomingTransferRequest->user->name }}</strong></p>
                <p>🏠 Phòng: <strong>{{ $incomingTransferRequest->room->room_number }}</strong></p>
                <p>🏢 Tòa nhà: {{ $incomingTransferRequest->room->property->name ?? 'Không xác định' }}</p>
                <p>📅 Ngày chuyển:
                    <strong>{{ \Carbon\Carbon::parse($incomingTransferRequest->leave_date)->format('d/m/Y') }}</strong>
                </p>
                <p>📝 Ghi chú: {{ $incomingTransferRequest->note ?? 'Không có ghi chú' }}</p>

                <form method="POST" action="{{ route('renter.transfer.accept', $incomingTransferRequest->id) }}"
                    onsubmit="return confirm('Bạn có chắc chắn muốn nhận chuyển nhượng hợp đồng?')">
                    @csrf
                    <button type="submit" class="btn btn-success mt-2">✅ Tôi đồng ý nhận chuyển nhượng</button>
                </form>
                <form method="POST" action="{{ route('renter.transfer.reject',  $incomingTransferRequest->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Bạn có chắc chắn muốn từ chối chuyển nhượng hợp đồng?')">
                        ❌ Tôi từ chối
                </button>
            </form>
            </div>
        @endif
        {{-- Thông báo --}}
        {{-- SweetAlert Notifications --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi dữ liệu!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
        {{-- SweetAlert Notifications --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{{ session('success') }}",
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: "{{ session('error') }}",
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi dữ liệu!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

        {{-- Chủ hợp đồng --}}
        @if ($room->rentalAgreement)
            @php $renter = $room->rentalAgreement->renter; @endphp
            <div class="alert alert-primary">
                <strong>Chủ hợp đồng:</strong> {{ $renter->name }} ({{ $renter->email }})
            </div>
            <div class="alert alert-info mt-2">
                💰 Tiền cọc:
                <strong>{{ number_format($room->rentalAgreement->deposit) }} VNĐ</strong>
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
                        📱 {{ $info->phone ?? 'Chưa có SĐT' }}<br>
                        Tòa nhà: {{ $info->room->property->name ?? 'Chưa có' }}<br>
                    </div>

                    @if ($user->id == $userId)
                        @if ($isContractOwner)
                            <div>

                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#terminateContractModal">
                                    🛑 Kết thúc/nhượng hợp đồng
                                </button>
                            </div>
                        @else
                            @if ($leaveRequest)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('home.roomleave.viewRequest', $leaveRequest->id) }}"
                                        class="btn btn-info btn-sm">👁️ Xem yêu cầu</a>
                                    <form method="POST"
                                        action="{{ route('home.roomleave.cancelRequest', $leaveRequest->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-secondary btn-sm" type="submit">❌ Huỷ yêu cầu</button>
                                    </form>
                                </div>
                            @else
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#leaveModal-{{ $user->id }}">
                                    🛑 Dừng thuê
                                </button>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            {{-- Modal dừng thuê (cho thành viên) --}}
            @if ($user->id == $userId && !$isContractOwner)
                <div class="modal fade" id="leaveModal-{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('home.roomleave.send') }}">
                                @csrf
                                <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                                <input type="hidden" name="user_id" value="{{ $userId }}">
                                <input type="hidden" name="action_type" value="leave">

                                <div class="modal-header">
                                    <h5 class="modal-title">Xác nhận rời phòng</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label>📅 Ngày rời đi</label>
                                    <input type="date" name="leave_date" class="form-control" required
                                        min="{{ now()->toDateString() }}" value="{{ old('leave_date') }}">
                                    @error('leave_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror

                                    <label class="mt-3">📝 Lý do (tuỳ chọn)</label>
                                    <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
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

        {{-- Các yêu cầu đã gửi --}}
        @if ($leaveRequests->count())
            <h4 class="mt-5">📤 Yêu cầu rời phòng đã gửi</h4>

            @foreach ($leaveRequests as $req)
                @php $user = $req->user ?? null; @endphp
                @if ($user)
                    <div class="card mb-3 border-warning shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $user->name }}{{ $user->id == $userId ? ' (Bạn)' : '' }}</strong><br>
                                📅 <strong>Ngày rời:</strong>
                                {{ \Carbon\Carbon::parse($req->leave_date)->format('d/m/Y') }}<br>
                                📝 <strong>Lý do:</strong> {{ $req->note ?? 'Không có' }}<br>
                                {{-- ⏳ <strong>Trạng thái:</strong>
                                Trạng thái gốc: <code>{{ $req->status }}</code><br> --}}
                                ⏳ <strong>Trạng thái:</strong>
                                @switch(strtolower($req->status))
                                    @case('pending')
                                        <span class="text-warning">⏳ Đang chờ</span>
                                    @break

                                    @case('approved')
                                        <span class="text-success">✅ Đã duyệt (hệ thống)</span>
                                    @break

                                    @case('rejected')
                                        <span class="text-danger">❌ Bị từ chối</span>
                                    @break

                                    @default
                                        <span class="text-muted">⚠️ Không rõ</span>
                                @endswitch

                            </div>

                            @if ($req->user_id == $userId && strtolower(trim($req->status)) === 'pending')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('home.roomleave.viewRequest', $req->id) }}"
                                        class="btn btn-info btn-sm">👁️ Xem chi tiết</a>

                                    <form method="POST" action="{{ route('home.roomleave.cancelRequest', $req->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">❌ Hủy</button>
                                    </form>
                                </div>
                            @endif
                            @if ($req->status === 'approved' && $req->user_id == $userId)
                                <form method="POST" action="{{ route('home.roomleave.finalize', $req->id) }}"
                                    onsubmit="return confirm('Bạn chắc chắn đã rời phòng?')" class="mt-3">
                                    @csrf
                                    <button class="btn btn-outline-danger btn-sm">✅ Tôi đã rời phòng</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    {{-- Modal kết thúc / nhượng quyền (chủ hợp đồng) --}}
    @if ($isContractOwner)
        <div class="modal fade" id="terminateContractModal" tabindex="-1" aria-labelledby="terminateContractModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('home.roomleave.send') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                        <input type="hidden" name="user_id" value="{{ $userId }}">

                        <div class="modal-header">
                            <h5 class="modal-title text-danger" id="terminateContractModalLabel">🛑 Kết thúc hoặc Nhượng hợp
                                đồng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-warning">
                                Bạn là <strong>chủ hợp đồng</strong>. Vui lòng điền yêu cầu:
                            </p>

                            @php
                                $isOwner = optional($room->rentalAgreement)->renter_id === $userId;
                                $otherUsersCount = $room->userInfos->count(); // số người trong phòng
                            @endphp

                            @if ($isOwner && $otherUsersCount > 1)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="action_type" value="transfer"
                                        id="transferOption">
                                    <label class="form-check-label" for="transferOption">
                                        🔄 Nhượng quyền cho người khác
                                    </label>
                                </div>
                            @endif
          @php
    $otherUsersCount = $room->userInfos->where('active', 1)->where('user_id', '!=', $userId)->count();
@endphp
                            @if ($otherUsersCount === 0)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="action_type" value="leave"
                                        id="terminateOption">
                                    <label class="form-check-label" for="terminateOption">
                                        🛑 Kết thúc hợp đồng
                                    </label>
                                </div>
                            @endif
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="action_type" value="leave_all"
                                    id="leaveAllOption">
                                <label class="form-check-label" for="leaveAllOption">
                                    👥 Tất cả thành viên rời khỏi phòng
                                </label>
                            </div>
                            <div class="mt-3" id="transferTarget" style="display: none;">
                                <label for="new_renter_id" class="form-label">📋 Chọn người nhận quyền</label>
                                <select name="new_renter_id" id="new_renter_id" class="form-select">
                                    @foreach ($room->userInfos as $info)
                                        @if ($info->user->id !== $userId)
                                            <option value="{{ $info->user->id }}">
                                                {{ $info->user->name }} ({{ $info->user->email }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <label for="leave_date" class="form-label mt-3">📅 Ngày áp dụng</label>
                            <input type="date" name="leave_date" id="leave_date" class="form-control" required
                                min="{{ now()->toDateString() }}" value="{{ old('leave_date') }}">
                            @error('leave_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="mt-3" id="qrUpload" style="display: none;">
                                <label for="deposit_file" class="form-label">📷 Tải QR nhận lại tiền cọc</label>
                                <input type="file" name="deposit_qr_image" id="deposit_file" class="form-control"
                                    accept="image/*" required>
                                <small class="text-muted">Hãy tải lên ảnh QR để nhận lại tiền cọc.</small>
                            </div>

                            <label for="note" class="form-label mt-3">📝 Ghi chú (tuỳ chọn)</label>
                            <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                            <button type="submit" class="btn btn-warning">Xác nhận</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const acceptTransferForm = document.querySelector('form[action*="transfer/accept"]');
                if (acceptTransferForm) {
                    acceptTransferForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Bạn có chắc chắn?',
                            text: 'Bạn muốn nhận chuyển nhượng hợp đồng này?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Đồng ý',
                            cancelButtonText: 'Huỷ'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                acceptTransferForm.submit();
                            }
                        });
                    });
                }

                const finalizeForms = document.querySelectorAll('form[action*="roomleave/finalize"]');
                finalizeForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Xác nhận rời phòng?',
                            text: 'Bạn chắc chắn rằng bạn đã rời khỏi phòng?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Tôi đã rời',
                            cancelButtonText: 'Huỷ'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                const sendLeaveForm = document.querySelectorAll('form[action*="roomleave/send"]');
                sendLeaveForm.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        const selectedAction = form.querySelector('input[name="action_type"]:checked');
                        const isTransfer = selectedAction && selectedAction.value === 'transfer';
                        const title = isTransfer ? 'Xác nhận nhượng hợp đồng?' :
                            'Xác nhận gửi yêu cầu rời phòng?';

                        Swal.fire({
                            title: title,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Gửi',
                            cancelButtonText: 'Huỷ'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                const mainForm = document.querySelector('#terminateContractModal form');
                if (mainForm) {
                    mainForm.addEventListener('submit', function(e) {
                        const actionType = mainForm.querySelector('input[name="action_type"]:checked');
                        if (!actionType) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Thiếu lựa chọn!',
                                text: 'Vui lòng chọn hành động: nhượng hoặc kết thúc hợp đồng.'
                            });
                        }
                    });
                }

                // --- Bổ sung phần QR + ngày kết thúc hợp đồng ---
                const modal = document.getElementById('terminateContractModal');
                if (!modal) return;

                const transferOption = modal.querySelector('#transferOption');
                const terminateOption = modal.querySelector('#terminateOption');
                const transferTarget = modal.querySelector('#transferTarget');
                const qrUpload = modal.querySelector('#qrUpload');
                const leaveDateInput = modal.querySelector('#leave_date');
                const depositFile = modal.querySelector('#deposit_file');

                function parseDate(str) {
                    if (!str) return null;
                    const [y, m, d] = str.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }

                const rentalEndDate = parseDate("{{ optional($room->rentalAgreement)->end_date }}");

                function toggleFields() {
                    // Nếu chọn nhượng thì hiện select người nhận
                    transferTarget.style.display = transferOption?.checked ? 'block' : 'none';

                    // Nếu chọn kết thúc hợp đồng thì check ngày
                    const leaveDate = parseDate(leaveDateInput.value);
                    if (terminateOption?.checked && leaveDate && leaveDate >= rentalEndDate) {
                        qrUpload.style.display = 'block';
                    } else {
                        qrUpload.style.display = 'none';
                    }
                }

                transferOption?.addEventListener('change', toggleFields);
                terminateOption?.addEventListener('change', toggleFields);
                leaveDateInput?.addEventListener('change', toggleFields);
                modal.addEventListener('shown.bs.modal', toggleFields);
                toggleFields();

                // Kiểm tra khi submit (bắt buộc upload QR nếu leave_date >= end_date)
                mainForm?.addEventListener('submit', function(e) {
                    const actionType = mainForm.querySelector('input[name="action_type"]:checked');
                    const leaveDate = parseDate(leaveDateInput.value);

                    if (actionType?.value === 'leave' && leaveDate && leaveDate >= rentalEndDate) {
                        if (!depositFile.value) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Thiếu QR!',
                                text: 'Vui lòng tải lên QR để nhận lại tiền cọc.'
                            });
                        }
                    }
                });

            });
            document.addEventListener("DOMContentLoaded", function() {
                const modal = document.getElementById('terminateContractModal');
                if (!modal) return;

                const qrUpload = modal.querySelector('#qrUpload');
                const depositFile = modal.querySelector('#deposit_file');
                const leaveDateInput = modal.querySelector('#leave_date');
                const mainForm = modal.querySelector('form');
                const leaveAllRadio = modal.querySelector('input[name="action_type"][value="leave_all"]');

                function parseDate(str) {
                    if (!str) return null;
                    const [y, m, d] = str.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }

                const rentalEndDate = parseDate("{{ optional($room->rentalAgreement)->end_date }}");

                function toggleLeaveAllFields() {
                    const leaveDate = parseDate(leaveDateInput.value);
                    if (leaveAllRadio?.checked && leaveDate && leaveDate >= rentalEndDate) {
                        qrUpload.style.display = 'block';
                    } else if (leaveAllRadio?.checked) {
                        qrUpload.style.display = 'none';
                    }
                }

                leaveDateInput?.addEventListener('change', toggleLeaveAllFields);
                leaveAllRadio?.addEventListener('change', toggleLeaveAllFields);
                modal.addEventListener('shown.bs.modal', toggleLeaveAllFields);

                // --- Kiểm tra khi submit ---
                mainForm?.addEventListener('submit', function(e) {
                    const actionType = mainForm.querySelector('input[name="action_type"]:checked');
                    const leaveDate = parseDate(leaveDateInput.value);

                    if (actionType?.value === 'leave_all' && leaveDate && leaveDate >= rentalEndDate) {
                        if (!depositFile.value) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Thiếu QR!',
                                text: 'Khi rời phòng toàn bộ sau ngày kết thúc hợp đồng, bạn phải tải lên QR để nhận lại tiền cọc.'
                            });
                        }
                    }
                });
            });
        </script>


    @endif

@endsection
