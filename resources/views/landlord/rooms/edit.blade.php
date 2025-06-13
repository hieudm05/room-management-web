@extends('landlord.layouts.app')

@section('title', 'Cập nhật phòng')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white fw-bold">✏️ Cập nhật phòng</h5>
            </div>
            <div class="card-body">

                {{-- Hiển thị lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('landlords.rooms.update', $room) }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Hiển thị tên khu trọ --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Khu trọ</label>
                        <input type="text" class="form-control" value="{{ $room->property->name }}" disabled>
                    </div>
                    <input type="hidden" name="property_id" value="{{ $room->property_id }}">

                    {{-- Số phòng (không cho sửa) --}}
                    <div class="mb-3">
                        <label class="form-label">Số phòng</label>
                        <input type="text" class="form-control" value="{{ $room->room_number }}" disabled>
                    </div>

                    {{-- Diện tích --}}
                    <div class="mb-3">
                        <label for="area" class="form-label">Diện tích (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="area" id="area"
                            class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $room->area) }}"
                            required>
                        @error('area')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giá thuê --}}
                    <div class="mb-3">
                        <label for="rental_price" class="form-label">Giá thuê (VNĐ) <span
                                class="text-danger">*</span></label>
                        <input type="number" name="rental_price" id="rental_price"
                            class="form-control @error('rental_price') is-invalid @enderror"
                            value="{{ old('rental_price', $room->rental_price) }}" required>
                        @error('rental_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" id="room_status" class="form-select @error('status') is-invalid @enderror"
                            required>
                            @foreach (['Available', 'Rented', 'Hidden', 'Suspended', 'Confirmed'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $room->status) == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiện nghi --}}
                    <div class="mb-3">
                        <label class="form-label">Tiện nghi</label>
                        <div class="row">
                            @foreach ($facilities as $facility)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]"
                                            value="{{ $facility->facility_id }}" id="facility{{ $facility->facility_id }}"
                                            {{ in_array($facility->facility_id, old('facilities', $roomFacilities)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="facility{{ $facility->facility_id }}">
                                            {{ $facility->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Thêm ảnh mới --}}
                    <div class="mb-3">
                        <label for="photos" class="form-label">Ảnh mới (có thể chọn nhiều)</label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="form-control">
                        <div class="form-text">Chỉ thêm ảnh mới, ảnh cũ sẽ được giữ nguyên.</div>
                    </div>

                    {{-- Dịch vụ --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <div class="row">
                            @foreach ($services as $service)
                                @php
                                    $existing = $roomServices[$service->service_id] ?? null;
                                @endphp
                                <div class="col-md-6 mb-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox"
                                            name="services[{{ $service->service_id }}][enabled]" value="1"
                                            id="service{{ $service->service_id }}" {{ $existing ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service{{ $service->service_id }}">
                                            {{ $service->name }} — <small
                                                class="text-muted">{{ $service->description }}</small>
                                        </label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">Giá:</span>
                                        <input type="number" name="services[{{ $service->service_id }}][price]"
                                            step="1000" class="form-control" value="{{ $existing['price'] ?? '' }}"
                                            placeholder="Miễn phí nếu để trống">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Ảnh hiện tại --}}
                    @if ($room->photos->count())
                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại</label>
                            <div class="row">
                                @foreach ($room->photos as $photo)
                                    <div class="col-md-3 mb-2 text-center border p-2 rounded">
                                        <img src="{{ $photo->image_url }}" alt="Ảnh phòng" width="100"
                                            class="mb-1 rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_photos[]"
                                                value="{{ $photo->photo_id }}" id="delete_photo_{{ $photo->photo_id }}">
                                            <label class="form-check-label small"
                                                for="delete_photo_{{ $photo->photo_id }}">
                                                Xóa ảnh
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Nút submit --}}
                    <div class="text-start">
                        <button type="submit" class="btn btn-primary">💾 Cập nhật phòng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
