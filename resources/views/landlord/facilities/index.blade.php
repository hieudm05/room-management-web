@extends('landlord.layouts.app')

@section('title', 'Quản lý tiện nghi')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">🛠️ Danh sách tiện nghi</h5>
            <a href="{{ route('landlords.facilities.create') }}" class="btn btn-light btn-sm">
                ➕ Thêm tiện nghi
            </a>
        </div>

        <div class="card-body">
            {{-- Thông báo thành công --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Bảng danh sách --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th width="30%">Tên tiện nghi</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facilities as $facility)
                            <tr>
                                <td class="fw-semibold">{{ $facility->name }}</td>
                                <td>
                                    <a href="{{ route('landlords.facilities.edit', $facility) }}"
                                       class="btn btn-sm btn-outline-warning me-1">
                                       ✏️ Sửa
                                    </a>

                                    <form action="{{ route('landlords.facilities.destroy', $facility) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xoá tiện nghi này?')">
                                        @csrf
                                        @method('DELETE')
                                        {{-- <button class="btn btn-sm btn-outline-danger">🗑️ Xoá</button> --}}
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Chưa có tiện nghi nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- SweetAlert2 CDN (chỉ cần nếu layout chưa có) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Hiển thị thông báo SweetAlert2 nếu có --}}
    @if(session('success'))
        <script>
            Swal.fire({
                title: "Thành công!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>
    @endif
@endpush