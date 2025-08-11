@extends('landlord.layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">📋 Khiếu nại được ủy quyền</h2>

    @if ($complaints->isEmpty())
        <div class="alert alert-info">Không có khiếu nại nào đang chờ xử lý.</div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Phòng</th>
                                <th>Khu trọ</th>
                                <th>Người gửi</th>
                                <th>SĐT</th>
                                <th>Hình ảnh</th>
                                <th>Nội dung khiếu nại</th>
                                <th>Ngày gửi</th>
                                <th class="text-center" colspan="2">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $c)
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ $c->room->room_number ?? 'N/A' }}</td>
                                    <td>{{ $c->property->name ?? 'N/A' }}</td>
                                    <td>{{ $c->full_name }}</td>
                                    <td>{{ $c->phone }}</td>
                                    <td class="text-center">
                                        @if ($c->photos->isNotEmpty())
                                            <img src="{{ asset('storage/' . $c->photos->first()->photo_path) }}"
                                                 alt="Ảnh khiếu nại"
                                                 class="img-thumbnail"
                                                 style="width: 96px; height: 96px; object-fit: cover;">
                                        @else
                                            <span class="text-muted fst-italic">Không có</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($c->detail, 80) }}</td>
                                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('landlord.staff.complaints.edit', $c->id) }}"
                                           class="btn btn-sm btn-outline-primary" title="Xử lý">
                                            🔧
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('landlord.staff.complaints.rejectform', $c->id) }}"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn chắc chắn muốn từ chối khiếu nại này?');">
                                            ❌ Từ chối
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- table-responsive -->
            </div> <!-- card-body -->
        </div> <!-- card -->
    @endif
</div>
@endsection
