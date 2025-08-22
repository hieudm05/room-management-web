@extends('landlord.layouts.app')

@section('title', 'Duyệt hợp đồng & đặt cọc')

@section('content')
{{-- SweetAlert thông báo --}}
@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            position: "center",
            icon: "success",
            title: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
@endif

<div class="col-xl-12">
    <div class="card mb-3 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">📑 Danh sách chờ duyệt</h4>
        </div>

        <div class="card-body">
            @forelse ($pendingApprovals as $approval)
            <div class="card mb-3 border shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold text-primary mb-2">
                        🏠 Phòng: {{ $approval->room->room_number }} - {{ $approval->room->property->name }}
                    </h5>
                    <p class="mb-1"><strong>Ngày tạo:</strong> {{ $approval->created_at->format('d/m/Y H:i') }}</p>

                    {{-- Phân loại --}}
                    @if ($approval->type === 'contract')
                    <span class="badge bg-info mb-2">Hợp đồng thuê</span>
                    <p class="mb-1"><strong>Giá thuê:</strong> {{ number_format($approval->rental_price) }} VNĐ</p>
                    <p class="mb-3"><strong>Đặt cọc:</strong> {{ number_format($approval->deposit) }} VNĐ</p>
                    <a href="{{ Str::contains($approval->file_path, 'storage/') ? asset($approval->file_path) : asset('storage/' . $approval->file_path) }}"
                        target="_blank"
                        class="btn btn-outline-primary btn-sm">
                        👁️ Xem hợp đồng
                    </a>

                    @elseif ($approval->type === 'deposit_image')
                    <span class="badge bg-warning mb-2">Ảnh đặt cọc</span>
                    <p class="mb-2"><strong>Ghi chú:</strong> {{ $approval->note ?? 'Không có' }}</p>
                    <div class="mb-3">
                        <img src="{{ Str::contains($approval->file_path, 'storage/') ? asset($approval->file_path) : asset('storage/' . $approval->file_path) }}"
                            alt="Ảnh đặt cọc"
                            class="img-fluid rounded border"
                            style="max-width: 320px;">
                    </div>
                    @endif

                    {{-- Nút hành động --}}
                    <div class="mt-2">
                        @if ($approval->type === 'contract')
                        <form action="{{ route('landlords.approvals.approve.contract', $approval->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">✅ Duyệt hợp đồng</button>
                        </form>
                        @elseif ($approval->type === 'deposit_image')
                        <form action="{{ route('landlords.approvals.approve.deposit', $approval->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">✅ Duyệt đặt cọc</button>
                        </form>
                        @endif


                        <form action="{{ route('landlords.approvals.reject', $approval->id) }}"
                            method="POST"
                            class="d-inline-block"
                            onsubmit="return confirm('Bạn chắc chắn muốn từ chối?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                ❌ Từ chối
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-warning text-center">
                ⚠️ Không có mục nào đang chờ duyệt.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
