@extends('landlord.layouts.app')

@section('title', 'Quản lý hợp đồng')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 fw-bold">📑 Quản lý hợp đồng thuê phòng</h5>
        </div>
        <div class="card-body">

            {{-- Thông tin phòng --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Phòng</label>
                <input type="text" class="form-control" value="{{ $room->room_number }} - {{ $room->property->name }}" disabled>
            </div>

            {{-- Nếu đã có hợp đồng (chờ duyệt hoặc đã duyệt) --}}
            @if (($pendingApproval && $pendingApproval->file_path) || ($existingAgreement && $existingAgreement->contract_file))
                <div class="mb-4">
                    <label class="form-label fw-bold text-success">
                        📎 Hợp đồng {{ $pendingApproval ? 'đang chờ duyệt' : 'đã duyệt' }}
                    </label>

                    <div class="mt-2">
                        <a href="{{ asset('storage/' . ($pendingApproval->file_path ?? $existingAgreement->contract_file)) }}" target="_blank" class="btn btn-outline-success">
                            👁️ Xem hợp đồng (PDF)
                        </a>
                    </div>
                </div>
            @else
                {{-- Nếu chưa có hợp đồng nào --}}
                <form action="{{ route('landlords.staff.contract.upload', $room->room_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tải lên hợp đồng thuê (PDF)</label>
                        <input type="file" name="agreement_file" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" class="btn btn-primary">📤 Tải lên hợp đồng</button>
                </form>
            @endif

            {{-- Quay lại --}}
            <div class="mt-4">
                <a href="{{ route('landlords.staff.index') }}" class="btn btn-secondary">🔙 Quay lại danh sách phòng</a>
            </div>

        </div>
    </div>
</div>
@endsection
