@extends('landlord.layouts.app')

@section('title', 'Create Properties ')

@section('content')
    <style>
        .border-dashed {
            border: 1px dashed red;
            transition: border-color 0.3s ease;
        }

        .image-box:hover {
            background-color: #fff3f3;
        }

        .preview-container {
            width: 100%;
            max-width: 300px;
            height: 200px;
            margin: 0 auto;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border-radius: 8px;
        }

        .preview-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    </style>
    <div class="container card">
        <div class="card-header align-items-center d-flex justify-content-center">
            <h3 class="card-title mb-0">Đăng ký bất động sản mới</h3>
            {{-- <a href="{{ route('landlord.properties.list') }}" class="btn btn-secondary btn-sm">← Danh sách</a> --}}
        </div>

        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form id="propertyForm" method="POST" action="{{ route('landlords.properties.store') }}"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        {{-- Ảnh đại diện --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text-info">Ảnh đại diện bất động sản</h5>
                            <div class="text-center">
                                <label for="main_image" class="d-block">
                                    <div class="border-dashed p-4 rounded image-box"
                                        style="cursor: pointer; background-color: #f8f9fa;">
                                        <div class="preview-container">
                                            <img id="imagePreview"
                                                src="{{ old('image_url') ? asset('storage/' . old('image_url')) : 'https://via.placeholder.com/200x150?text=Chọn+ảnh' }}"
                                                alt="Preview" />
                                        </div>
                                        <div class="text-danger fw-semibold mt-2">📷 Bấm để chọn ảnh đại diện</div>
                                    </div>
                                </label>
                                <input type="file" id="main_image" name="image_url"
                                    class="d-none @error('image_url') is-invalid @enderror" accept="image/*" required>
                                @error('image_url')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Thông tin bất động sản --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text-primary">1. Thông tin bất động sản</h5>
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên bất động sản <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="VD: Khu trọ Nguyễn Văn A" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea id="description" name="description" class="form-control" rows="3"
                                    placeholder="Giới thiệu chung về khu trọ...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Địa chỉ chi tiết --}}
                        <div class="mb-4">
                            <h5 class="mb-3 text-success">2. Địa chỉ chi tiết</h5>
                            <div class="row">
                                {{-- Tỉnh/Thành phố --}}
                                <div class="col-md-4 mb-3">
                                    <label for="province" class="form-label">Tỉnh/Thành phố <span
                                            class="text-danger">*</span></label>
                                    <select id="province" name="province"
                                        class="form-select @error('province') is-invalid @enderror" required>
                                        <option value="">-- Chọn tỉnh --</option>
                                    </select>
                                    @error('province')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Quận/Huyện --}}
                                <div class="col-md-4 mb-3">
                                    <label for="district" class="form-label">Quận/Huyện <span
                                            class="text-danger">*</span></label>
                                    <select id="district" name="district"
                                        class="form-select @error('district') is-invalid @enderror" required disabled>
                                        <option value="">-- Chọn huyện --</option>
                                    </select>
                                    @error('district')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phường/Xã --}}
                                <div class="col-md-4 mb-3">
                                    <label for="ward" class="form-label">Phường/Xã <span
                                            class="text-danger">*</span></label>
                                    <select id="ward" name="ward"
                                        class="form-select @error('ward') is-invalid @enderror" required disabled>
                                        <option value="">-- Chọn xã --</option>
                                    </select>
                                    @error('ward')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="detailed_address" class="form-label">Địa chỉ cụ thể <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="detailed_address" name="detailed_address" class="form-control"
                                        placeholder="Số nhà, đường..." value="{{ old('detailed_address') }}" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Xác định vị trí trên bản đồ</label>
                                    <div id="map" style="height: 350px; border: 1px solid #ccc;"></div>
                                </div>
                            </div>

                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                        </div>

                        {{-- Giấy tờ pháp lý --}}
                        <div class="mb-3">
                            <label for="giay_phep_kinh_doanh" class="form-label">Giấy phép kinh doanh (nếu có)</label>
                            <input type="file" id="giay_phep_kinh_doanh" name="document_files[giay_phep_kinh_doanh]"
                                class="form-control @error('document_files.giay_phep_kinh_doanh') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @error('document_files.giay_phep_kinh_doanh')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nút gửi --}}
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">📤 Gửi đăng ký</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Thư viện -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script>
        //Sử lý xem trước ảnh
        const input = document.getElementById("main_image");
        const preview = document.getElementById("imagePreview");
        const error = document.getElementById("imageError");
        const defaultImage = "https://via.placeholder.com/200x150?text=Chọn+ảnh";

        input.addEventListener("change", function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    error.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                // Chỉ hiển thị lỗi nếu hiện tại vẫn là ảnh mặc định
                if (preview.src.includes("placeholder.com")) {
                    error.classList.remove('d-none');
                } else {
                    error.classList.add('d-none'); // Đã có ảnh từ trước -> không lỗi
                }
            }
        });
        document.getElementById('main_image').addEventListener('change', function(e) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('imagePreview');
                preview.src = URL.createObjectURL(file);
            }
        });

        // Phục hồi dữ liệu
        document.addEventListener('DOMContentLoaded', async function() {
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            const oldProvince = '{{ old('province') }}';
            const oldDistrict = '{{ old('district') }}';
            const oldWard = '{{ old('ward') }}';

            // Load tỉnh
            const provinces = await fetch("https://provinces.open-api.vn/api/p/").then(res => res.json());
            provinces.forEach(province => {
                const option = new Option(province.name, province.code);
                if (province.code == oldProvince) option.selected = true;
                provinceSelect.add(option);
            });

            if (oldProvince) {
                provinceSelect.disabled = false;
                const districts = await fetch(`https://provinces.open-api.vn/api/p/${oldProvince}?depth=2`)
                    .then(res => res.json());
                districts.districts.forEach(d => {
                    const option = new Option(d.name, d.code);
                    if (d.code == oldDistrict) option.selected = true;
                    districtSelect.add(option);
                });
                districtSelect.disabled = false;
            }

            if (oldDistrict) {
                const wards = await fetch(`https://provinces.open-api.vn/api/d/${oldDistrict}?depth=2`).then(
                    res => res.json());
                wards.wards.forEach(w => {
                    const option = new Option(w.name, w.code);
                    if (w.code == oldWard) option.selected = true;
                    wardSelect.add(option);
                });
                wardSelect.disabled = false;
            }
        });



        //Map
        let map = L.map('map').setView([21.028511, 105.804817], 13); // Hà Nội mặc định
        let marker = null;
        const apiKey = '{{ config('services.locationiq.key') }}'

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        function updateMapWithAddress() {
            let detail = $("#detailed_address").val().trim();
            let provinceText = $("#province option:selected").text();
            let districtText = $("#district option:selected").text();
            let wardText = $("#ward option:selected").text();

            if (!detail && (!wardText || wardText === '-- Chọn xã --')) return;

            let fullAddress = `${detail ? detail + ', ' : ''}${wardText}, ${districtText}, ${provinceText}, Việt Nam`;

            if (fullAddress.length < 10) return;

            fetch(`https://us1.locationiq.com/v1/search.php?key=${apiKey}&q=${encodeURIComponent(fullAddress)}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let lat = parseFloat(data[0].lat);
                        let lon = parseFloat(data[0].lon);

                        if (marker) map.removeLayer(marker);
                        marker = L.marker([lat, lon]).addTo(map);
                        map.setView([lat, lon], 16);

                        // Gán tọa độ vào input ẩn
                        $('#latitude').val(lat);
                        $('#longitude').val(lon);
                    } else {
                        alert("Không tìm thấy vị trí với địa chỉ bạn nhập.");
                    }
                })
                .catch(() => {
                    alert("Đã xảy ra lỗi khi định vị bản đồ.");
                });
        }

        $(document).ready(function() {
            // Load danh sách tỉnh
            $.get('/provinces', function(data) {
                data.forEach(function(province) {
                    $('#province').append(
                        `<option value="${province.code}">${province.name}</option>`);
                });
            });

            $('#province').on('change', function() {
                let provinceCode = $(this).val();
                $('#district').html('<option value="">-- Chọn huyện --</option>').prop('disabled', true);
                $('#ward').html('<option value="">-- Chọn xã --</option>').prop('disabled', true);

                if (!provinceCode) return;

                $.get(`/districts/${provinceCode}`, function(data) {
                    if (data.length > 0) {
                        data.forEach(function(district) {
                            $('#district').append(
                                `<option value="${district.code}">${district.name}</option>`
                            );
                        });
                        $('#district').prop('disabled', false);
                    }
                    updateMapWithAddress();
                });
            });

            $('#district').on('change', function() {
                let districtCode = $(this).val();
                $('#ward').html('<option value="">-- Chọn xã --</option>').prop('disabled', true);

                if (!districtCode) return;

                $.get(`/wards/${districtCode}`, function(data) {
                    if (data.length > 0) {
                        data.forEach(function(ward) {
                            $('#ward').append(
                                `<option value="${ward.code}">${ward.name}</option>`);
                        });
                        $('#ward').prop('disabled', false);
                    }
                    updateMapWithAddress();
                });
            });

            $('#ward').on('change', updateMapWithAddress);

            let debounceTimer;
            $('#detailed_address').on('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(updateMapWithAddress, 1000);
            });
        });
    </script>
@endsection
