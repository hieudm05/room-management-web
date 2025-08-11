@extends('landlord.layouts.app')

@section('title', 'Quản lý dịch vụ')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">📦 Danh sách dịch vụ</h5>
                <a href="{{ route('landlords.services.create') }}" class="btn btn-light btn-sm">
                    ➕ Thêm dịch vụ
                </a>
            </div>

            <div class="card-body">
                {{-- Bảng dịch vụ --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Tên dịch vụ</th>
                                <th width="50%">Mô tả</th>
                                <th width="20%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td class="fw-semibold">{{ $service->name }}</td>
                                    <td class="text-muted">{{ $service->description ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('landlords.services.edit', $service) }}"
                                            class="btn btn-sm btn-outline-warning me-1">✏️ Sửa</a>

                                        {{-- <form action="{{ route('landlords.services.destroy', $service) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xoá dịch vụ này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">🗑️ Xoá</button>
                                        </form> --}}

                                        <button
                                            class="btn btn-sm toggle-visibility-btn {{ $service->is_hidden ? 'btn-outline-success' : 'btn-outline-secondary' }}"
                                            data-id="{{ $service->service_id }}">
                                            {{ $service->is_hidden ? '👁️ Bỏ ẩn' : '🙈 Ẩn' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">Chưa có dịch vụ nào.</td>
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
    @if (session('success'))
        <script>
            Swal.fire({
                title: "Thành công!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    {{-- AJAX Toggle Ẩn / Bỏ ẩn --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-visibility-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const serviceId = this.getAttribute('data-id');
                    const btn = this;

                    fetch(`/landlords/services/${serviceId}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (data.is_hidden) {
                                    btn.textContent = '👁️ Bỏ ẩn';
                                    btn.classList.remove('btn-outline-secondary');
                                    btn.classList.add('btn-outline-success');
                                } else {
                                    btn.textContent = '🙈 Ẩn';
                                    btn.classList.remove('btn-outline-success');
                                    btn.classList.add('btn-outline-secondary');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: data.is_hidden ? 'Dịch vụ đã được ẩn.' :
                                        'Dịch vụ đã được bỏ ẩn.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                });
            });
        });
    </script>
@endpush
