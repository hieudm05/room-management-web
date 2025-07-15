@extends('landlord.layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4"><span class="me-2">📋</span>Danh sách khiếu nại từ người thuê</h2>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($complaints->isEmpty())
        <p class="text-muted">Chưa có khiếu nại nào.</p>
    @else
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh khiếu nại</th>
                        <th>Ảnh sau xử lý</th>
                        <th>Người gửi</th>
                        <th>SĐT</th>
                        <th>Tòa</th>
                        <th>Phòng</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th>Nhân viên</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($complaints as $c)
                        @php
                            $initialPhoto = $c->photos->where('type', 'initial')->first();
                            $resolvedPhoto = $c->photos->where('type', 'resolved')->first();
                        @endphp

                        <tr>
                            <td class="text-center">{{ $c->id }}</td>

                            {{-- Ảnh khiếu nại --}}
                            <td class="text-center">
                                @if ($initialPhoto)
                                    <img src="{{ asset('storage/' . $initialPhoto->photo_path) }}" alt="Ảnh khiếu nại" width="100">
                                @else
                                    <span class="text-muted fst-italic">Không có</span>
                                @endif
                            </td>

                            {{-- Ảnh sau xử lý --}}
                            <td class="text-center">
                                @if ($resolvedPhoto)
                                    <img src="{{ asset('storage/' . $resolvedPhoto->photo_path) }}" alt="Ảnh xử lý" width="100">
                                @else
                                    <span class="text-muted fst-italic">Chưa cập nhật</span>
                                @endif
                            </td>

                            <td>{{ $c->full_name }}</td>
                            <td>{{ $c->phone }}</td>
                            <td>{{ $c->property->name ?? '---' }}</td>
                            <td>{{ $c->room->room_number ?? '---' }}</td>
                            <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>

                            {{-- Trạng thái --}}
                            <td>
                                @switch($c->status)
                                    @case('pending') <span class="text-warning">Chờ duyệt</span> @break
                                    @case('in_progress') <span class="text-primary">Đang xử lý</span> @break
                                    @case('resolved') <span class="text-success">Đã xử lý</span> @break
                                    @case('rejected') <span class="text-danger">Từ chối</span> @break
                                    @case('cancelled') <span class="text-muted">Đã hủy</span> @break
                                    @default <span class="text-secondary">Không rõ</span>
                                @endswitch
                            </td>

                            {{-- Nhân viên --}}
                            <td>
                                @php
                                    $assigned = $c->staff;
                                    $default = $c->room->staffs->first();
                                @endphp

                                @if ($assigned)
                                    {{ $assigned->name }} <small class="text-muted">(Đã duyệt)</small>
                                @elseif ($default)
                                    {{ $default->name }}
                                @else
                                    <span class="text-muted fst-italic">Chưa có</span>
                                @endif
                            </td>

                            {{-- Thao tác --}}
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('landlord.complaints.show', $c->id) }}" class="btn btn-sm btn-outline-warning">👁️</a>

                                    @if ($c->status === 'pending' && $c->room->staffs->isNotEmpty())
                                        <form method="POST" action="{{ route('landlord.complaints.approve', $c->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">✅</button>
                                        </form>

                                    @elseif ($c->status === 'in_progress')
                                        <span class="text-primary small">🔧 Đang xử lý</span>

                                    @elseif ($c->status === 'resolved')
                                        <span class="text-success small">✔ Hoàn tất</span>

                                    @elseif ($c->status === 'rejected')
                                        <a href="{{ route('landlord.complaints.rejection', $c->id) }}" class="btn btn-sm btn-outline-danger">❗</a>
                                        <a href="{{ route('landlord.complaints.assign.form', $c->id) }}" class="btn btn-sm btn-outline-primary">🔁</a>
                                        <form method="POST" action="{{ route('landlord.complaints.accept-reject', $c->id) }}" onsubmit="return confirm('Chấp nhận lý do từ chối và đóng khiếu nại này?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">✅</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if (method_exists($complaints, 'links'))
            <div class="card-footer d-flex justify-content-end">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
    @endif
</div>
@endsection