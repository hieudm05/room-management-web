@extends('landlord.layouts.app')

@section('title', 'Danh sách phòng')

@section('content')

    <div class="col-xl-12">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('landlords.rooms.index') }}" class="row g-3 align-items-end">
                    {{-- Tìm kiếm --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text">🔍</span>
                            <input type="text" name="search" class="form-control"
                                placeholder="Tên phòng, khu trọ, tiện nghi..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Lọc theo khu trọ --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Khu trọ</label>
                        <select name="property_id" id="select-khu-tro" class="form-select">
                            <option value="">-- Tất cả khu trọ --</option>
                            @foreach ($allProperties as $property)
                                <option value="{{ $property->property_id }}"
                                    {{ request('property_id') == $property->property_id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lọc giá cố định --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mức giá</label>
                        <select name="price_range" class="form-select">
                            <option value="">-- Chọn mức giá --</option>
                            <option value="0-1000000" {{ request('price_range') == '0-1000000' ? 'selected' : '' }}>Dưới 1
                                triệu</option>
                            <option value="1000000-3000000"
                                {{ request('price_range') == '1000000-3000000' ? 'selected' : '' }}>1 - 3 triệu</option>
                            <option value="3000000-5000000"
                                {{ request('price_range') == '3000000-5000000' ? 'selected' : '' }}>3 - 5 triệu</option>
                            <option value="5000000" {{ request('price_range') == '5000000' ? 'selected' : '' }}>Trên 5 triệu
                            </option>
                        </select>
                    </div>

                    {{-- Lọc theo giá tự nhập --}}
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Từ giá (VNĐ)</label>
                        <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}"
                            placeholder="Tối thiểu">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Đến giá (VNĐ)</label>
                        <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}"
                            placeholder="Tối đa">
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách phòng --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title mb-0">📋Danh sách phòng</h4>
                <a href="{{ route('landlords.rooms.create') }}" class="btn btn-success">➕ Thêm phòng mới</a>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap table-striped-columns mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Khu trọ</th>
                                <th>Số phòng</th>
                                <th>Số người ở tối đa</th>
                                <th>Diện tích</th>
                                <th>Giá thuê</th>
                                <th>Trạng thái</th>
                                <th>Tiện nghi</th>
                                <th>Dịch vụ</th>
                                <th>Ảnh</th>
                                <th>Nhân viên quản lý</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rooms as $room)
                                <tr>
                                    <td>{{ $room->property->name ?? 'N/A' }}</td>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->occupants }}</td>
                                    <td>{{ $room->area }} m²</td>
                                    <td>{{ number_format($room->rental_price) }} VND</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($room->status) {
                                                'Available' => 'badge bg-success',
                                                'Rented' => 'badge bg-primary',
                                                'Hidden' => 'badge bg-warning',
                                                'Suspended' => 'badge bg-danger',
                                                'Confirmed' => 'badge bg-info',
                                                default => 'badge bg-secondary',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ $room->status }}</span>
                                    </td>
                                    <td>{{ $room->facilities_count }}</td>
                                    <td>
                                        @if ($room->services->count())
                                            @foreach ($room->services->take(2) as $service)
                                                <span class="badge bg-secondary">{{ $service->name }}</span>
                                            @endforeach
                                            @if ($room->services->count() > 2)
                                                <span
                                                    class="badge bg-light text-dark">+{{ $room->services->count() - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($room->photos->first())
                                            <img src="{{ asset($room->photos->first()->image_url) }}"
                                                class="rounded shadow-sm" width="50">
                                        @else
                                            <span class="text-muted">Chưa có ảnh</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse ($room->staffs as $staff)
                                            <span class="badge bg-info">{{ $staff->name }}</span>
                                        @empty
                                            <span class="text-muted">Chưa phân quyền</span>
                                        @endforelse
                                    </td>

                                    <td>
                                        {{-- Các nút thao tác --}}
                                        <a href="{{ route('landlords.rooms.edit', $room) }}"
                                            class="btn btn-sm btn-outline-primary">✏️</a>
                                        <a href="{{ route('landlords.rooms.show', $room) }}"
                                            class="btn btn-sm btn-outline-warning">👁️</a>

                                        <form action="{{ route('landlords.rooms.destroy', $room) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xoá phòng này?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                        </form>

                                        <a href="{{ route('landlords.rooms.staffs.edit', $room->room_id) }}"
                                            class="btn btn-sm btn-outline-info">👤</a>

                                        <div class="d-flex gap-1 mt-1">
                                            @if (!$room->is_contract_locked)
                                                {{-- Khóa phòng --}}
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#lockRoomModal{{ $room->room_id }}">🔒</button>
                                            @else
                                                {{-- Mở khóa phòng --}}
                                                <form action="{{ route('landlords.rooms.unlock', $room) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn mở khóa phòng này không?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">🔓</button>
                                                </form>
                                            @endif

                                            {{-- Chuyển phòng --}}
                                            @if ($room->currentAgreementValid)
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#moveRoomModal{{ $room->room_id }}">🔄</button>
                                            @endif


                                            {{-- Thống kê --}}
                                            <a href="{{ route('landlords.rooms.statistics', $room) }}"
                                                class="btn btn-sm btn-outline-secondary">📊</a>
                                        </div>

                                        {{-- Modal Khóa phòng --}}
                                        <div class="modal fade" id="lockRoomModal{{ $room->room_id }}" tabindex="-1"
                                            aria-labelledby="lockRoomLabel{{ $room->room_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('landlords.rooms.lock', $room) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="lockRoomLabel{{ $room->room_id }}">Khóa phòng
                                                                {{ $room->room_number }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="lock_reason_{{ $room->room_id }}"
                                                                class="form-label">Nhập lý do khóa:</label>
                                                            <textarea id="lock_reason_{{ $room->room_id }}" class="form-control" name="lock_reason" required rows="3"
                                                                placeholder="Ví dụ: Phòng cần sửa chữa, bảo trì..."></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-danger">Xác nhận
                                                                khóa</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal Chuyển Phòng -->
                                        @if ($room->currentAgreementValid)
                                            <div class="modal fade" id="moveRoomModal{{ $room->room_id }}"
                                                tabindex="-1" aria-labelledby="moveRoomLabel{{ $room->room_id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('landlords.rooms.move', $room) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="moveRoomLabel{{ $room->room_id }}">
                                                                    Chuyển phòng {{ $room->room_number }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Đóng"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label for="new_room_{{ $room->room_id }}"
                                                                    class="form-label">Chọn phòng mới:</label>
                                                                <select name="new_room_id"
                                                                    id="new_room_{{ $room->room_id }}"
                                                                    class="form-select" required>
                                                                    <option value="">-- Chọn phòng mới --</option>
                                                                    @foreach ($availableRooms as $availableRoom)
                                                                        @if ($availableRoom->room_id != $room->room_id && !$availableRoom->currentAgreementValid)
                                                                            <option value="{{ $availableRoom->room_id }}">
                                                                                {{ $availableRoom->property->name ?? '' }}
                                                                                -
                                                                                {{ $availableRoom->room_number }}
                                                                                ({{ number_format($availableRoom->rental_price) }}
                                                                                VND)
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Hủy</button>
                                                                <button type="submit" class="btn btn-warning">Xác nhận
                                                                    chuyển</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Không có phòng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Phân trang --}}
        @if (method_exists($rooms, 'links'))
            <div class="mt-3 d-flex justify-content-end">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#select-khu-tro').select2({
                    placeholder: "🔍 Chọn khu trọ",
                    allowClear: true,
                    width: '100%'
                });
            });
        </script>
    @endsection

   @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        @if ($errors->has('delete'))
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "{{ $errors->first('delete') }}",
                    confirmButtonText: "OK"
                });
            </script>
        @endif
    @endpush

@endsection
