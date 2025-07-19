@extends('landlord.layouts.app')

@section('title', 'Quản lý hợp đồng')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bage-primary text-white">
                <h5 class="mb-0 fw-bold">📑 Quản lý hợp đồng thuê phòng</h5>
            </div>
            <div class="card-body">

                {{-- Thông tin phòng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Phòng</label>
                    <input type="text" class="form-control" value="{{ $room->room_number }} - {{ $room->property->name }}"
                        disabled>
                </div>

                {{-- Nếu đã có hợp đồng (chờ duyệt hoặc đã duyệt) --}}
                @if ($pendingApproval && $pendingApproval->file_path)
                    {{-- Hợp đồng chờ duyệt --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-warning">📎 Hợp đồng đang chờ duyệt</label>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $pendingApproval->file_path) }}" target="_blank"
                                class="btn btn-outline-warning">
                                👁️ Xem hợp đồng chờ duyệt
                            </a>
                        </div>
                    </div>
                @elseif ($activeAgreement && $activeAgreement->contract_file)
                    {{-- Hợp đồng hiện tại --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-success">📎 Hợp đồng đã duyệt</label>
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $activeAgreement->contract_file) }}" target="_blank"
                                class="btn btn-outline-success">
                                👁️ Xem hợp đồng hiện tại
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Cho phép upload nếu không có hợp đồng hoạt động --}}
                    <div class="alert alert-warning">
                        ⚠️ Không có hợp đồng hiện tại. Bạn có thể tải hợp đồng mới.
                    </div>
                    <form action="{{ route('landlords.staff.contract.upload', $room->room_id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">📤 Tải lên hợp đồng thuê mới (PDF)</label>
                            <input type="file" name="agreement_file" class="form-control" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi duyệt hợp đồng</button>
                    </form>
                @endif

                @if ($terminatedAgreements->count())
                    <hr>
                    <h5 class="mt-4">📜 Hợp đồng cũ đã bị vô hiệu hóa:</h5>
                    <ul class="list-group">
                        @foreach ($terminatedAgreements as $agreement)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    🗂️ <strong>{{ $agreement->file_name ?? 'Hợp đồng trước' }}</strong>
                                    <br>
                                    <span class="badge bg-danger">Đã bị vô hiệu hóa</span>
                                </div>
                                <a href="{{ asset('storage/' . $agreement->contract_file) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">👁️ Xem</a>
                            </li>
                        @endforeach
                    </ul>
                @endif


                {{-- Quay lại --}}
                <div class="mt-4">
                    <a href="{{ route('landlords.staff.index') }}" class="btn btn-secondary">🔙 Quay lại danh sách phòng</a>
                </div>

            </div>
        </div>
    </div>
@endsection
