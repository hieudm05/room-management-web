@extends('landlord.layouts.app')

@section('title', 'Danh sách phòng')

@section('content')
    @if (session('success'))
        <script>
            window.onload = function() {
                alert("{{ session('success') }}");
            };
        </script>
    @endif

    <div class="col-xl-12">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('landlords.rooms.index') }}" class="row g-3 align-items-end">
                    {{-- Tìm kiếm --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="🔍 Tìm tên phòng, khu trọ, tiện nghi..." value="{{ request('search') }}">
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

                    {{-- Lọc theo giá cố định --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Mức giá</label>
                        <select name="price_range" class="form-select">
                            <option value="">-- Chọn mức giá --</option>
                            <option value="0-1000000" {{ request('price_range') == '0-1000000' ? 'selected' : '' }}>
                                Dưới 1 triệu
                            </option>
                            <option value="1000000-3000000"
                                {{ request('price_range') == '1000000-3000000' ? 'selected' : '' }}>
                                1 - 3 triệu
                            </option>
                            <option value="3000000-5000000"
                                {{ request('price_range') == '3000000-5000000' ? 'selected' : '' }}>
                                3 - 5 triệu
                            </option>
                            <option value="5000000" {{ request('price_range') == '5000000' ? 'selected' : '' }}>
                                Trên 5 triệu
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
                <h4 class="card-title mb-0">Danh sách phòng</h4>
                <a href="{{ route('landlords.rooms.create') }}" class="btn btn-success btn-sm">+ Thêm phòng</a>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap table-striped-columns mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Khu trọ</th>
                                <th>Số phòng</th>
                                <th>Số người</th>
                                <th>Diện tích</th>
                                <th>Giá thuê</th>
                                <th>Trạng thái</th>
                                <th>Tiện nghi</th>
                                <th>Dịch vụ</th>
                                <th>Ảnh</th>
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
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($room->photos->first())
                                            <img src="{{ asset($room->photos->first()->image_url) }}" width="50">
                                        @else
                                            <span class="text-muted">Chưa có ảnh</span>
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Không có phòng nào.</td>
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
        $(document).ready(function () {
            $('#select-khu-tro').select2({
                placeholder: "🔍 Chọn khu trọ",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection


@endsection
