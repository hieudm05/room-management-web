@extends('landlord.layouts.app')

@section('title', 'Chỉnh sửa phòng (nhân viên)')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white fw-bold">📨 Gửi yêu cầu chỉnh sửa phòng</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('landlords.staff.rooms.request_update', $room->room_id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold">👤 Thông tin chủ phòng</h6>

                        <div class="mb-2">
                            <label class="form-label">Họ tên</label>
                            <input type="text" class="form-control" value="{{ $room->property->landlord->name ?? 'Không rõ' }}" disabled>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="{{ $room->property->landlord->phone_number ?? '' }}" disabled>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">CCCD</label>
                            <input type="text" class="form-control" value="{{ $room->property->landlord->identity_number ?? '' }}" disabled>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Khu trọ</label>
                        <input type="text" class="form-control" value="{{ $room->property->name }}" disabled>
                    </div>
                    <input type="hidden" name="property_id" value="{{ $room->property_id }}">

                    <div class="mb-3">
                        <label class="form-label">Số phòng</label>
                        <input type="text" class="form-control" value="{{ $room->room_number }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="area" class="form-label">Diện tích (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="area" id="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $room->area) }}" required>
                        @error('area')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="rental_price" class="form-label">Giá thuê (VNĐ)</label>
                        <input type="number" name="rental_price" class="form-control" value="{{ old('rental_price', $room->rental_price) }}">
                    </div>

                    <div class="mb-3">
                        <label for="deposit_price" class="form-label">Giá tiền cọc (VNĐ)</label>
                        <input type="number" name="deposit_price" class="form-control" value="{{ old('deposit_price', $room->deposit_price ?? 0) }}">
                    </div>

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


                    <div class="mb-3">
                        <label for="occupants" class="form-label">Số người ở</label>
                        <input type="number" name="occupants" class="form-control" value="{{ old('occupants', $room->occupants) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tiện nghi</label>
                        <div class="row">
                            @foreach ($facilities as $facility)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility->facility_id }}" id="facility{{ $facility->facility_id }}" {{ in_array($facility->facility_id, old('facilities', $roomFacilities)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="facility{{ $facility->facility_id }}">
                                            {{ $facility->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <div class="row">
                            @foreach ($services as $service)
                                @php $existing = $roomServices[$service->service_id] ?? null; @endphp
                                <div class="col-md-6 mb-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="services[{{ $service->service_id }}][enabled]" value="1" id="service{{ $service->service_id }}" {{ $existing ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service{{ $service->service_id }}">
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text">Giá:</span>
                                        <input type="number" name="services[{{ $service->service_id }}][price]" class="form-control" value="{{ $existing['price'] ?? '' }}">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                    @if (in_array($service->service_id, [2, 3, 7]))
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="services[{{ $service->service_id }}][unit]" value="per_person" {{ ($existing['unit'] ?? 'per_person') == 'per_person' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo người</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="services[{{ $service->service_id }}][unit]" value="per_room" {{ ($existing['unit'] ?? '') == 'per_room' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo phòng</label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="photos" class="form-label">Ảnh mới (có thể chọn nhiều)</label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="form-control">
                        <div class="form-text">Chỉ thêm ảnh mới, ảnh cũ sẽ được giữ nguyên.</div>
                    </div>

                    <div id="preview-images" class="row mt-3"></div>

                    @if ($room->photos->count())
                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại</label>
                            <div class="row">
                                @foreach ($room->photos as $photo)
                                    <div class="col-md-3 mb-2 text-center border p-2 rounded">
                                        <img src="{{ $photo->image_url }}" alt="Ảnh phòng" width="100" class="mb-1 rounded">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_photos[]" value="{{ $photo->photo_id }}" id="delete_photo_{{ $photo->photo_id }}">
                                            <label class="form-check-label small" for="delete_photo_{{ $photo->photo_id }}">
                                                Xóa ảnh
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-start">
                        <button type="submit" class="btn btn-primary">📨 Gửi yêu cầu chỉnh sửa</button>
                        <a href="{{ route('landlords.staff.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('photos').addEventListener('change', function(event) {
                const previewContainer = document.getElementById('preview-images');
                previewContainer.innerHTML = '';

                const files = event.target.files;

                if (files) {
                    Array.from(files).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                const col = document.createElement('div');
                                col.classList.add('col-md-3', 'mb-3');

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.classList.add('img-thumbnail');
                                img.style.maxHeight = '150px';
                                img.alt = 'Ảnh phòng';

                                col.appendChild(img);
                                previewContainer.appendChild(col);
                            };

                            reader.readAsDataURL(file);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection