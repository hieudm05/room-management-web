@extends('landlord.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center">🗂️ Lịch sử khiếu nại đã xử lý</h2>

    {{-- Thông báo session --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($complaints->isEmpty())
        <div class="alert alert-info text-center">
            Không có khiếu nại nào đã xử lý.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Vấn đề</th>
                        <th>Phòng</th>
                        <th>Trạng thái</th>
                        <th>Ngày xử lý</th>
                        <th>Ảnh</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $complaint)
                        <tr>
                            <td class="text-center">{{ $complaint->id }}</td>
                            <td>
                                {{ $complaint->full_name }}<br>
                                <small class="text-muted">{{ $complaint->phone }}</small>
                            </td>
                            <td>{{ $complaint->commonIssue->name ?? 'N/A' }}</td>
                            <td>{{ $complaint->room->room_number ?? '---' }}</td>
                            <td class="text-center">
                                <span class="badge 
                                    @if ($complaint->status == 'resolved') bg-success
                                    @elseif ($complaint->status == 'cancelled') bg-secondary
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </td>
                            <td>{{ $complaint->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                @foreach ($complaint->photos->where('type', 'resolved') as $photo)
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Ảnh xử lý"
                                         width="60" height="60" class="rounded me-1 mb-1"
                                         style="object-fit: cover;">
                                @endforeach
                            </td>
                            <td class="text-center">
                                <a href="{{ route('landlord.staff.complaints.show', $complaint->id) }}"
                                   class="btn btn-sm btn-primary me-1">
                                    <i class="bi bi-eye"></i> Xem
                                </a>

                                <form action="{{ route('landlord.staff.complaints.destroy', $complaint->id) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa khiếu nại này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $complaints->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
