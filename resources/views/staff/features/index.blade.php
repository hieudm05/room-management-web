@extends('landlord.layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold text-primary">Danh sách Features</h2>
            <a href="{{ route('features.create') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    
                    <thead class="table-primary">
                        <tr>
                            <th>STT</th>
                            <th>Tên</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($features as $f)
                            <tr>
                                <td>{{ $loop->iteration + ($features->currentPage() - 1) * $features->perPage() }}</td>
                                <td>{{ $f->name }}</td>
                                <td>{{ $f->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>{{ $f->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('features.edit', $f) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('features.destroy', $f) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Xóa feature này?')"
                                            class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
                <div class="mt-3">{{ $features->links() }}</div>
            </div>
        </div>
    </div>
@endsection
