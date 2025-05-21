@extends('admin.layouts.app') {{-- hoặc layouts.admin nếu bạn có layout riêng --}}

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
    </div>
@endif

    <div class="container">
        <h2 class="mb-4">Quản lý tài khoản</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-2">Thêm tài khoản mới</a>

        {{-- Form lọc tài khoản theo role --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 mb-4">
            <div class="col-auto">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Tất cả quyền --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="renter" {{ request('role') == 'renter' ? 'selected' : '' }}>Renter</option>
                    <option value="landlord" {{ request('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <!-- Thêm các role khác nếu hệ thống có -->
                </select>
            </div>
        </form>

        {{-- Thông báo lọc --}}
        @if (request('role'))
            <div class="alert alert-info">
                Đang lọc theo quyền: <strong>{{ ucfirst(request('role')) }}</strong>
            </div>
        @endif

        {{-- Bảng danh sách tài khoản --}}
        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>STT</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                    <tr class="text-center">
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $user->role }}</span>
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="badge bg-success">Đang hoạt động</span>
                            @else
                                <span class="badge bg-danger">Bị khóa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Không có tài khoản nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $users->links() }} {{-- Phân trang --}}
    </div>
@endsection
