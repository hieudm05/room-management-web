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
        <div class="card">
            <div class="card-header align-items-center d-flex justify-content-between">
                <h4 class="card-title mb-0">Danh sách phòng</h4>

                {{-- Nút thêm phòng, truyền property_id từ URL --}}
                <a href="{{ route('landlords.rooms.create', ['property_id' => request('property_id')]) }}"
                    class="btn btn-success btn-sm">
                    + Thêm phòng
                </a>
            </div>

            <div class="card-body">
                <div class="live-preview">
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
                                    <th style="width: 150px;">Hành động</th>
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
                                                <img src="{{ $room->photos->first()->image_url }}" width="50">
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
                                            {{-- @if ($room->status !== "Available") --}}
                                            <form action="{{ route('landlords.rooms.contract.info', $room) }}" method="get">
                                                <input type="hidden" value="{{$room->id_rental_agreements}}" name="rental_agreement_id" >
                                                <input type="hidden" value="{{$room->room_id}}" name="room_id" >
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Show RG</button>
                                            </form>
                                                
                                      
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có phòng nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->

        {{-- Phân trang --}}
        @if (method_exists($rooms, 'links'))
            <div class="mt-3 d-flex justify-content-end">
                {{ $rooms->links() }}
            </div>
        @endif

    </div><!-- end col -->
@endsection
