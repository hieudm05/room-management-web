@extends('landlord.layouts.app')
@section('title', 'Yêu cầu chuyển hợp đồng / rời phòng')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">📄 Danh sách yêu cầu chờ chủ trọ xử lý</h4>

        @forelse ($requests as $req)
            <div class="card mb-3 shadow-sm border border-secondary-subtle">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">
                                🧍 <strong>{{ $req->user->name }}</strong> gửi yêu cầu
                                <span class="badge bg-info text-dark">
                                    @if ($req->action_type === 'transfer')
                                        Nhượng quyền
                                    @elseif ($req->action_type === 'leave')
                                        Rời phòng
                                    @elseif ($req->action_type === 'leave_all')
                                        Rời phòng toàn bộ
                                    @endif
                                </span>
                            </p>
                            <p class="mb-1">🏠 Phòng: <strong>{{ $req->room->room_number ?? $req->room->name }}</strong>
                            </p>
                            <p class="mb-1">🏢 Tòa nhà:
                                <strong>{{ $req->room->property?->name ?? 'Không xác định' }}</strong>
                            </p>
                            <p class="mb-1">📅 Ngày áp dụng: <strong>{{ $req->leave_date }}</strong></p>
                            <p class="mb-1">📝 Ghi chú: {{ $req->reason ?? 'Không có ghi chú' }}</p>
                        </div>

                        <div class="text-end">
                            <div class="btn-group-vertical">
                                {{-- Xem chi tiết --}}
                                <a href="{{ route('landlord.roomleave.show', $req->id) }}"
                                    class="btn btn-outline-primary btn-sm">🔍 Xem chi tiết</a>

                                {{-- Nếu là nhượng quyền --}}
                                @if ($req->action_type === 'transfer')
                                    <button type="button" class="btn btn-outline-success btn-sm mb-1"
                                        data-bs-toggle="modal" data-bs-target="#transferModal{{ $req->id }}">
                                        ✍️ Duyệt chuyển nhượng
                                    </button>
                                @endif

                                {{-- Nếu là rời phòng --}}
                                @if ($req->action_type === 'leave')
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#approveLeaveModal{{ $req->id }}">
                                        ✅ Duyệt
                                    </button>
                                @endif

                                {{-- Nếu là rời toàn bộ --}}
                                @if ($req->action_type === 'leave_all')
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#approveLeaveAllModal{{ $req->id }}">
                                        ⚠️ Duyệt rời toàn bộ
                                    </button>
                                @endif

                                {{-- Từ chối --}}
                                <a href="{{ route('landlord.roomleave.rejectForm', $req->id) }}"
                                    class="btn btn-danger btn-sm">❌ Từ chối</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Duyệt chuyển nhượng --}}
            @if ($req->action_type === 'transfer')
                <div class="modal fade" id="transferModal{{ $req->id }}" tabindex="-1"
                    aria-labelledby="modalLabel{{ $req->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel{{ $req->id }}">✍️ Xác nhận chuyển nhượng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('landlord.roomleave.transfer.submit', $req->id) }}">
                                    @csrf
                                    <input type="hidden" name="new_renter_id" value="{{ $req->new_renter_id ?? '' }}">

                                    <div class="mb-3">
                                        <label class="form-label">🧍 Người nhận</label>
                                        <input type="text" class="form-control"
                                            value="{{ $req->newRenter->name ?? 'Không có' }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">📧 Email</label>
                                        <input type="text" class="form-control"
                                            value="{{ $req->newRenter->email ?? 'Không có' }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">📞 Số điện thoại</label>
                                        <input type="text" class="form-control"
                                            value="{{ $req->newRenter->info->phone ?? 'Không có' }}" readonly>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Xác nhận chuyển hợp đồng cho người dùng này?')">
                                        ✅ Gửi chuyển nhượng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Modal Duyệt rời phòng --}}
            @if ($req->action_type === 'leave')
                <div class="modal fade" id="approveLeaveModal{{ $req->id }}" tabindex="-1"
                    aria-labelledby="modalLabelLeave{{ $req->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabelLeave{{ $req->id }}">✅ Duyệt yêu cầu rời phòng
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <form method="POST" action="{{ route('landlord.roomleave.approve', $req->id) }}"
                                enctype="multipart/form-data"
                                onsubmit="return confirm('Xác nhận duyệt rời phòng và xử lý cọc?')">
                                @csrf
                                <div class="modal-body">
                                    <p><strong>Người rời phòng:</strong> {{ $req->user->name }}</p>
                                    <p><strong>Phòng:</strong> {{ $req->room->room_number }}</p>
                                    <p><strong>Ngày yêu cầu:</strong> {{ $req->leave_date }}</p>
                                    @php $isOwner = optional($req->room->rentalAgreement)->renter_id === $req->user_id; @endphp
                                    @if ($isOwner)
                                        <p><strong>Số tiền cọc:</strong>
                                            {{ number_format($req->room->rentalAgreement?->deposit ?? 0) }} đ</p>
                                        {{-- QR --}}
                                        @if ($req->deposit_qr_image)
                                            <div class="mt-3">
                                                <label class="form-label">📷 QR đặt cọc</label>
                                                <img src="{{ Storage::url($req->deposit_qr_image) }}"
                                                    class="img-fluid rounded border">
                                            </div>
                                        @endif
                                        <div class="mt-3">
                                            <label class="form-label">💰 Xử lý tiền cọc</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="refund_deposit"
                                                    value="1" id="refundYes{{ $req->id }}" checked>
                                                <label class="form-check-label" for="refundYes{{ $req->id }}">✅ Hoàn
                                                    cọc</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="refund_deposit"
                                                    value="0" id="refundNo{{ $req->id }}">
                                                <label class="form-check-label" for="refundNo{{ $req->id }}">❌ Không
                                                    hoàn cọc</label>
                                            </div>
                                            <div class="mt-2" id="refundReasonDiv{{ $req->id }}"
                                                style="display:none;">
                                                <label class="form-label">Lý do không hoàn cọc</label>
                                                <textarea class="form-control" name="refund_reason" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="mt-3" id="proofDiv{{ $req->id }}">
                                            <label class="form-label">📎 Ảnh minh chứng hoàn cọc</label>
                                            <input type="file" class="form-control" name="proof_image"
                                                accept="image/*">
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <label class="form-label">📝 Ghi chú gửi người thuê</label>
                                        <textarea class="form-control" name="reason" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                                    <button type="submit" class="btn btn-success">Xác nhận duyệt</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    document.querySelectorAll('#approveLeaveModal{{ $req->id }} input[name="refund_deposit"]').forEach(el => {
                        el.addEventListener('change', function() {
                            const div = document.getElementById('refundReasonDiv{{ $req->id }}');
                            const proof = document.getElementById('proofDiv{{ $req->id }}');
                            if (this.value == '0') {
                                div.style.display = 'block';
                                proof.style.display = 'none';
                            } else {
                                div.style.display = 'none';
                                proof.style.display = 'block';
                            }
                        });
                    });
                </script>
            @endif

            {{-- Modal Duyệt rời toàn bộ --}}
            @if ($req->action_type === 'leave_all')
                <div class="modal fade" id="approveLeaveAllModal{{ $req->id }}" tabindex="-1"
                    aria-labelledby="modalLabelLeaveAll{{ $req->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">⚠️ Duyệt yêu cầu rời toàn bộ phòng</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('landlord.roomleave.approve', $req->id) }}"
                                enctype="multipart/form-data"
                                onsubmit="return confirm('Xác nhận duyệt rời toàn bộ phòng và xử lý cọc?')">
                                @csrf
                                <div class="modal-body">
                                    <p><strong>Người yêu cầu:</strong> {{ $req->user->name }}</p>
                                    <p><strong>Phòng:</strong> {{ $req->room->room_number }}</p>
                                    <p><strong>Ngày yêu cầu:</strong> {{ $req->leave_date }}</p>
                                    <p class="text-danger"><strong>⚠️ Lưu ý:</strong> Toàn bộ hợp đồng sẽ kết thúc. Tất cả
                                        người thuê trong phòng sẽ bị cập nhật thành đã rời.</p>
                                    <p><strong>Số tiền cọc:</strong>
                                        {{ number_format($req->room->rentalAgreement?->deposit ?? 0) }} đ</p>
                                    @if ($req->deposit_qr_image)
                                        <div class="mt-3">
                                            <label class="form-label">📷 QR đặt cọc</label>
                                            <img src="{{ Storage::url($req->deposit_qr_image) }}"
                                                class="img-fluid rounded border">
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <label class="form-label">💰 Xử lý tiền cọc</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="refund_deposit"
                                                value="1" id="refundYesAll{{ $req->id }}" checked>
                                            <label class="form-check-label" for="refundYesAll{{ $req->id }}">✅ Hoàn
                                                cọc</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="refund_deposit"
                                                value="0" id="refundNoAll{{ $req->id }}">
                                            <label class="form-check-label" for="refundNoAll{{ $req->id }}">❌ Không
                                                hoàn cọc</label>
                                        </div>
                                        <div class="mt-2" id="refundReasonDivAll{{ $req->id }}"
                                            style="display:none;">
                                            <label class="form-label">Lý do không hoàn cọc</label>
                                            <textarea class="form-control" name="refund_reason" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3" id="proofDivAll{{ $req->id }}">
                                        <label class="form-label">📎 Ảnh minh chứng hoàn cọc</label>
                                        <input type="file" class="form-control" name="proof_image" accept="image/*">
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">📝 Ghi chú gửi người thuê</label>
                                        <textarea class="form-control" name="reason" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Huỷ</button>
                                    <button type="submit" class="btn btn-warning">Xác nhận duyệt rời toàn bộ</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    document.querySelectorAll('#approveLeaveAllModal{{ $req->id }} input[name="refund_deposit"]').forEach(el => {
                        el.addEventListener('change', function() {
                            const div = document.getElementById('refundReasonDivAll{{ $req->id }}');
                            const proof = document.getElementById('proofDivAll{{ $req->id }}');
                            if (this.value == '0') {
                                div.style.display = 'block';
                                proof.style.display = 'none';
                            } else {
                                div.style.display = 'none';
                                proof.style.display = 'block';
                            }
                        });
                    });
                </script>
            @endif
        @empty
            <div class="alert alert-info">Hiện không có yêu cầu nào đang chờ xử lý.</div>
        @endforelse
    </div>
@endsection

