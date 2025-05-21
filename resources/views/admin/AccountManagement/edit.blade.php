@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Chỉnh sửa tài khoản</h3>
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <form id="edit-user-form" action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Họ tên</label>
            <input type="text" class="form-control" value="{{ $user->name }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Quyền</label>
            <select name="role" class="form-select">
                @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="is_active" class="form-select">
                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('edit-user-form');
    const roleInput = document.querySelector('select[name="role"]');
    const statusInput = document.querySelector('select[name="is_active"]');

    form.addEventListener('submit', function (e) {
        const isActive = parseInt(statusInput.value);
        const role = roleInput.value;

        if (!isActive) { // Nếu đang chọn Bị khóa
            if (role === 'Admin') {
                e.preventDefault();
                alert("❌ Không thể khóa tài khoản Admin.");
                return;
            }

            const confirmLock = confirm("Xác nhận: Bạn có chắc chắn muốn khóa tài khoản này?");
            if (!confirmLock) {
                e.preventDefault(); // Huỷ submit nếu không đồng ý
            }
        }
    });
});
</script>
