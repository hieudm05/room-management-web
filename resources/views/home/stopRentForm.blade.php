@extends('home.layouts.app')

@section('title', 'Ngừng cho thuê phòng')
<style>
    .content-wrapper {
        min-height: 100%; /* Đẩy footer xuống */
    }
</style>
@section('content')
<div class="container mt-4 content-wrapper">

<div class="container mt-4">
    <h3 class="mb-4">🚫 Danh sách thành viên phòng đang thuê</h3>

    @foreach($roomUsers as $agreement)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <p><strong>👤 Họ tên:</strong> {{ $agreement->renter->name }}</p>
                        <p><strong>📧 Email:</strong> {{ $agreement->renter->email }}</p>
                        <p><strong>📱 SĐT:</strong> {{ $agreement->renter->phone }}</p>
                    </div>
                    <div class="col-md-6">
                        @if ($agreement->is_active)
                            <!-- Nút mở modal -->
                            <button type="button"
                                class="btn btn-danger mt-2"
                                data-bs-toggle="modal"
                                data-bs-target="#stopModal{{ $agreement->rental_id }}">
                                Dừng thuê
                            </button>
                        @else
                            <span class="badge bg-secondary">Đã gửi yêu cầu</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="stopModal{{ $agreement->rental_id }}" tabindex="-1" aria-labelledby="stopModalLabel{{ $agreement->rental_id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('room-users.stop', $agreement->rental_id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="stopModalLabel{{ $agreement->rental_id }}">Xác nhận dừng thuê</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            <label for="leave_date_{{ $agreement->rental_id }}">📅 Ngày dự kiến rời đi:</label>
                            <input type="date" name="leave_date" id="leave_date_{{ $agreement->rental_id }}" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xác nhận</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<!-- Bootstrap JS (chỉ nếu chưa có) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
