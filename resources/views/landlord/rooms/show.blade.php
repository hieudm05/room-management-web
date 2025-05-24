@extends('landlord.layouts.app')

@section('title', 'Chi tiết phòng')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">🔍 Chi tiết phòng</h5>
            </div>
            <div class="card-body">

                {{-- Khu trọ --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Khu trọ</label>
                    <input type="text" class="form-control" value="{{ $room->property->name }}" disabled>
                </div>

                {{-- Số phòng --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số phòng</label>
                    <input type="text" class="form-control" value="{{ $room->room_number }}" disabled>
                </div>

                {{-- Diện tích --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Diện tích (m²)</label>
                    <input type="text" class="form-control" value="{{ $room->area }}" disabled>
                </div>

                {{-- Giá thuê --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Giá thuê (VNĐ)</label>
                    <input type="text" class="form-control" value="{{ number_format($room->rental_price) }}" disabled>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <input type="text" class="form-control" value="{{ $room->status }}" disabled>
                </div>

                {{-- Tiện nghi --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiện nghi</label>
                    <ul class="list-group">
                        @forelse ($room->facilities as $facility)
                            <li class="list-group-item">{{ $facility->name }}</li>
                        @empty
                            <li class="list-group-item text-muted">Không có tiện nghi</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Dịch vụ --}}
                @if ($room->services->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <ul class="list-group">
                            @foreach ($room->services as $service)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $service->name }}</div>
                                        <small class="text-muted">{{ $service->description }}</small>
                                    </div>
                                    <span class="badge bg-{{ $service->pivot->is_free ? 'success' : 'primary' }}">
                                        {{ $service->pivot->is_free ? 'Miễn phí' : number_format($service->pivot->price) . ' VNĐ' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <p class="text-muted">Không có dịch vụ nào.</p>
                    </div>
                @endif

                {{-- Ảnh phòng --}}
                @if ($room->photos->count())
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ảnh phòng</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($room->photos as $photo)
                                <div class="border p-1">
                                    <img src="{{ $photo->image_url }}" width="150" class="rounded shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Nút quay lại --}}
                <div class="text-start mt-4">
                    <a href="{{ route('landlords.rooms.index') }}" class="btn btn-secondary">🔙 Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
@endsection
