@extends('home.layouts.app')

@section('content')

<div class="container bg-white p-4 mt-5 rounded shadow">
    <h2 class="h5 fw-semibold mb-4">Danh sách khiếu nại</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($complaints->isEmpty())
        <p class="text-muted">Chưa có khiếu nại nào.</p>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên KH</th>
                    <th>SĐT</th>
                    <th>Tên khiếu nại</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->id }}</td>
                    <td>
                        @if ($complaint->photos->isNotEmpty())
                            <img src="{{ asset('storage/' . $complaint->photos->first()->photo_path) }}"
                                 alt="Ảnh khiếu nại"
                                 class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;">
                        @else
                            <span class="text-muted fst-italic">Không có</span>
                        @endif
                    </td>
                    <td>{{ $complaint->full_name }}</td>
                    <td>{{ $complaint->phone }}</td>
                    <td>{{ $complaint->commonIssue->name ?? 'N/A' }}</td>
                    <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-capitalize">{{ $complaint->status }}</td>

                    <td>
                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                            {{-- Nút Chi tiết --}}
                            <a href="{{ route('home.complaints.show', $complaint) }}"
                               class="btn btn-sm btn-primary">
                                Chi tiết
                            </a>

                            @if ($complaint->status === 'pending')
                                {{-- Nút Sửa --}}
                                <a href="{{ route('home.complaints.edit', $complaint) }}"
                                   class="btn btn-sm btn-warning text-white">
                                    Sửa
                                </a>

                                {{-- Nút Xóa --}}
                                <form action="{{ route('home.complaints.destroy', $complaint) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa khiếu nại này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Xóa
                                    </button>
                                </form>

                                {{-- Nút Hủy --}}
                                <form action="{{ route('home.complaints.cancel', $complaint) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy khiếu nại này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        Hủy
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
