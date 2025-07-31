@extends('landlord.layouts.app')

@section('title', 'Danh sách nhân viên')

@section('content')

    {{-- ✅ Thông báo khi thao tác thành công --}}
    @if (session('success'))
        <script>
            window.onload = function() {
                alert("{{ session('success') }}");
            };
        </script>
    @endif

    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title mb-0">Danh sách nhân viên</h4>
                <a href="{{ route('landlords.staff_accounts.create') }}" class="btn btn-success btn-sm">+ Tạo Tài Khoản Cho
                    Nhân Viên</a>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap table-striped-columns mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staffAccounts as $staff)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $staff->name }}</td>
                                    <td>{{ $staff->email }}</td>
                                    <td>{{ $staff->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('landlords.staff_accounts.show', $staff->id) }}"
                                            class="btn btn-sm btn-outline-warning">👁️</a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có nhân viên nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Phân trang --}}
        @if (method_exists($staffAccounts, 'links'))
            <div class="mt-3 d-flex justify-content-end">
                {{ $staffAccounts->links() }}
            </div>
        @endif
    </div>

@endsection
