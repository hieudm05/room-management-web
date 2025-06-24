@extends('landlord.layouts.app')

@section('title', 'Duyệt hợp đồng thuê')

@section('content')
@if (session('success'))
    <script>
        window.onload = function() {
            alert("{{ session('success') }}");
        };
    </script>
@endif

<div class="col-xl-12">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title mb-0">📑 Danh sách hợp đồng chờ duyệt</h4>
        </div>

        <div class="card-body">
            @forelse ($pendingApprovals as $approval)
                <div class="card mb-3 border shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary">Phòng: {{ $approval->room->room_number }} - {{ $approval->room->property->name }}</h5>
                        <p><strong>Giá thuê:</strong> {{ number_format($approval->rental_price) }} VNĐ</p>
                        <p><strong>Đặt cọc:</strong> {{ number_format($approval->deposit) }} VNĐ</p>
                        <p><strong>Ngày tạo:</strong> {{ $approval->created_at->format('d/m/Y H:i') }}</p>

                        <a href="{{ asset('storage/' . $approval->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                            👁️ Xem hợp đồng
                        </a>

                        <form action="{{ route('landlords.approvals.approve', $approval->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">✅ Duyệt</button>
                        </form>

                        <form action="{{ route('landlords.approvals.reject', $approval->id) }}" method="POST" class="d-inline-block"
                              onsubmit="return confirm('Bạn chắc chắn muốn từ chối hợp đồng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">❌ Từ chối</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning text-center">
                    Không có hợp đồng nào đang chờ duyệt.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
