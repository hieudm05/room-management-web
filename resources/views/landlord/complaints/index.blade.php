@extends('landlord.layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4"><span class="me-2">📋</span>Danh sách khiếu nại từ người thuê</h2>

        {{-- Hiển thị thông báo bằng SweetAlert2 --}}
        @if (session('success'))
            <script>
                window.onload = () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: "{{ session('success') }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            </script>
        @endif

        @if (session('error'))
            <script>
                window.onload = () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: "{{ session('error') }}",
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            </script>
        @endif

        @if ($complaints->isEmpty())
            <div class="alert alert-info">Chưa có khiếu nại nào được gửi.</div>
        @else
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh đầu</th>
                                <th>Ảnh xử lý</th>
                                <th>Người gửi</th>
                                <th>SĐT</th>
                                <th>Tòa</th>
                                <th>Phòng</th>
                                <th>Gửi lúc</th>
                                <th>Trạng thái</th>
                                <th>Nhân viên</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $c)
                                @php
                                    $initialPhoto = $c->photos->where('type', 'initial')->first();
                                    $resolvedPhoto = $c->photos->where('type', 'resolved')->first();
                                @endphp

                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>
                                        @if ($initialPhoto)
                                            <img src="{{ asset('storage/' . $initialPhoto->photo_path) }}" width="70">
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($resolvedPhoto)
                                            <img src="{{ asset('storage/' . $resolvedPhoto->photo_path) }}" width="70">
                                        @else
                                            <span class="text-muted">Chưa cập nhật</span>
                                        @endif
                                    </td>
                                    <td>{{ $c->full_name }}</td>
                                    <td>{{ $c->phone }}</td>
                                    <td>{{ $c->property->name ?? '---' }}</td>
                                    <td>{{ $c->room->room_number ?? '---' }}</td>
                                    <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @switch($c->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @break

                                            @case('in_progress')
                                                <span class="badge bg-primary">Đang xử lý</span>
                                            @break

                                            @case('resolved')
                                                <span class="badge bg-success">Đã xử lý</span>
                                            @break

                                            @case('rejected')
                                                <span class="badge bg-danger">Từ chối</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-secondary">Đã hủy</span>
                                            @break

                                            @default
                                                <span class="badge bg-light text-dark">Không rõ</span>
                                        @endswitch
                                    </td>
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
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- Luôn có nút xem --}}
                                            <a href="{{ route('landlord.complaints.show', $c->id) }}"
                                                class="btn btn-sm btn-outline-warning">👁️</a>

                                            @if ($c->status === 'pending')
                                                @if ($c->room->staffs->isEmpty())
                                                    {{-- Chủ tự xử lý --}}
                                                    <a href="{{ route('landlord.complaints.resolve.form', $c->id) }}"
                                                        class="btn btn-sm btn-outline-primary">✍</a>
                                                    <a href="{{ route('landlord.complaints.reject.form', $c->id) }}"
                                                        class="btn btn-sm btn-outline-danger">❌</a>
                                                @else
                                                    {{-- Có nhân viên thì giao --}}
                                                    <form method="POST"
                                                        action="{{ route('landlord.complaints.approve', $c->id) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-success">✅</button>
                                                    </form>
                                                @endif
                                            @elseif ($c->status === 'rejected')
                                                {{-- Luôn hiển thị nút ❗ --}}


                                                {{-- Chỉ hiện 2 nút này nếu bị nhân viên từ chối --}}
                                                @if ($c->handled_by === $c->staff_id)
                                                    <a href="{{ route('landlord.complaints.rejection', $c->id) }}"
                                                        class="btn btn-sm btn-outline-danger">❗</a>
                                                    <a href="{{ route('landlord.complaints.assign.form', $c->id) }}"
                                                        class="btn btn-sm btn-outline-primary">🔁</a>
                                                  
                                                    {{-- Chủ trọ đồng ý từ chối -> đóng khiếu nại --}}
                                                    <form method="POST"
                                                        action="{{ route('landlord.complaints.accept-reject', $c->id) }}"
                                                        onsubmit="return confirm('Bạn có chắc muốn đồng ý từ chối và đóng khiếu nại này?')">
                                                        @csrf
                                                        <input type="hidden" name="action" value="cancel">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">✅
                                                            Đóng</button>
                                                    </form>

                                                    {{-- Chủ trọ không đồng ý -> tự xử lý --}}
                                                    <form method="POST"
                                                        action="{{ route('landlord.complaints.accept-reject', $c->id) }}"
                                                        onsubmit="return confirm('Bạn có chắc muốn tự tiếp nhận xử lý khiếu nại này?')">
                                                        @csrf
                                                        <input type="hidden" name="action" value="takeover">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">✍ 
                                                            </button>
                                                    </form>
                                                @endif
                                            @elseif ($c->status === 'in_progress')
                                                <span class="badge bg-info text-dark">🔧 Đang xử lý</span>
                                            @elseif ($c->status === 'resolved')
                                                <span class="badge bg-success">✔ Hoàn tất</span>
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

@section('scripts')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
