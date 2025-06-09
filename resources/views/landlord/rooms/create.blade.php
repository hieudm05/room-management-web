@extends('landlord.layouts.app')

@section('title', 'Thêm phòng mới')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white fw-bold">➕ Thêm phòng mới</h5>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('landlords.rooms.store') }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf

                    {{-- <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="fw-bold">👤 Thông tin người tạo (Chủ trọ)</h6>

                        <div class="mb-2">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="creator_name" class="form-control" required
                                value="{{ old('creator_name', Auth::user()?->name) }}">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="creator_phone" class="form-control" required
                                value="{{ old('creator_phone', Auth::user()?->phone_number) }}">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">CCCD <span class="text-danger">*</span></label>
                            <input type="text" name="creator_identity" class="form-control" required
                                value="{{ old('creator_identity', Auth::user()?->identity_number) }}">
                        </div>
                    </div> --}}

                    {{-- Chọn khu trọ --}}
                    <div class="mb-3">
                        <label for="property_id" class="form-label fw-bold">Chọn khu trọ <span
                                class="text-danger">*</span></label>
                        <select name="property_id" id="property_id"
                            class="form-select @error('property_id') is-invalid @enderror" required>
                            <option disabled selected>-- Chọn khu trọ --</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->property_id }}"
                                    {{ old('property_id') == $property->property_id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số phòng --}}
                    <div class="mb-3">
                        <label for="room_number" class="form-label">Số phòng <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" id="room_number"
                            class="form-control @error('room_number') is-invalid @enderror"
                            value="{{ old('room_number') }}" required>
                        @error('room_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Diện tích --}}
                    <div class="mb-3">
                        <label for="area" class="form-label">Diện tích (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="area" id="area"
                            class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}" required>
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
                            value="{{ old('rental_price') }}" required>
                        @error('rental_price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" id="room_status" class="form-select @error('status') is-invalid @enderror"
                            required>
                            <option disabled selected>-- Chọn trạng thái --</option>
                            @foreach (['Available', 'Rented', 'Hidden', 'Suspended', 'Confirmed'] as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Số người ở --}}
                    <div class="mb-3">
                        <label for="occupants" class="form-label">Số người ở <span class="text-danger">*</span></label>
                        <input type="number" name="occupants" id="occupants"
                            class="form-control @error('occupants') is-invalid @enderror" value="{{ old('occupants', 0) }}"
                            min="0" required>
                        @error('occupants')
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
                                            value="{{ $facility->facility_id }}"
                                            id="facility{{ $facility->facility_id }}"
                                            {{ is_array(old('facilities')) && in_array($facility->facility_id, old('facilities')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="facility{{ $facility->facility_id }}">
                                            {{ $facility->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Dịch vụ --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Dịch vụ</label>
                        <div class="row">
                            @foreach ($services as $service)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox"
                                            name="services[{{ $service->service_id }}][enabled]" value="1"
                                            id="service{{ $service->service_id }}"
                                            {{ old("services.{$service->service_id}.enabled") ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service{{ $service->service_id }}">
                                            {{ $service->name }} — <small
                                                class="text-muted">{{ $service->description }}</small>
                                        </label>
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text">Giá:</span>
                                        <input type="number" name="services[{{ $service->service_id }}][price]"
                                            step="1000" class="form-control"
                                            value="{{ old("services.{$service->service_id}.price") }}"
                                            placeholder="Miễn phí nếu để trống">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>

                                    {{-- Cách tính riêng cho nước và wifi --}}
                                    @if ($service->service_id == 2)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="services[2][unit]"
                                                value="per_person"
                                                {{ old('services.2.unit', 'per_person') == 'per_person' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo người</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="services[2][unit]"
                                                value="per_m3" {{ old('services.2.unit') == 'per_m3' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo khối (m³)</label>
                                        </div>
                                    @elseif ($service->service_id == 3)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="services[3][unit]"
                                                value="per_person"
                                                {{ old('services.3.unit', 'per_person') == 'per_person' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo người</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="services[3][unit]"
                                                value="per_room"
                                                {{ old('services.3.unit') == 'per_room' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tính theo phòng</label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Ảnh phòng --}}
                    <div class="mb-3">
                        <label for="photos" class="form-label">Ảnh phòng</label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*"
                            class="form-control @error('photos.*') is-invalid @enderror">
                        <div class="form-text">Chọn nhiều ảnh nếu cần (jpg, png, jpeg...)</div>
                        @error('photos.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="text-start">
                        <button type="submit" class="btn btn-success">💾 Lưu phòng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection