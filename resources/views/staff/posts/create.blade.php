@extends('landlord.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-5 bg-white">
                        <h2 class="mb-5 text-center text-primary fw-bold display-5">
                            <i class="bi bi-house-door-fill me-2"></i> Đăng bài cho thuê nhà trọ
                        </h2>

                        <form action="{{ route('staff.posts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                {{-- Loại chuyên mục --}}
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold text-dark">Loại chuyên
                                        mục</label>
                                    <select name="category_id" id="category_id" class="form-select shadow-sm rounded-3"
                                        required>
                                        <option value="">-- Chọn loại chuyên mục --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Chủ trọ --}}
                                <div class="col-md-6">
                                    <label for="landlord_id" class="form-label fw-semibold text-dark">Chủ trọ</label>
                                    <select name="landlord_id" id="landlord_id" class="form-select shadow-sm rounded-3"
                                        required>
                                        <option value="">-- Chọn chủ trọ --</option>
                                        @foreach ($landlords as $landlord)
                                            <option value="{{ $landlord->id }}">{{ $landlord->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('landlord_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Bất động sản --}}
                                <div class="col-md-6">
                                    <label for="property_id" class="form-label fw-semibold text-dark">Bất động sản</label>
                                    <select name="property_id" id="property_id" class="form-select shadow-sm rounded-3"
                                        required>
                                        <option value="">-- Chọn bất động sản --</option>
                                        @foreach ($properties as $property)
                                            <option value="{{ $property->property_id }}">{{ $property->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Tiêu đề --}}
                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-semibold text-dark">Tiêu đề</label>
                                    <input type="text" name="title" id="title"
                                        class="form-control shadow-sm rounded-3" placeholder="Nhập tiêu đề bài viết"
                                        value="{{ old('title') }}" required>
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Giá cho thuê --}}
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold text-dark">Giá cho thuê
                                        (VNĐ)</label>
                                    <input type="text" name="price" id="price"
                                        class="form-control shadow-sm rounded-3" placeholder="VD: 2,500,000"
                                        value="{{ old('price') }}" required>
                                    @error('price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Diện tích --}}
                                <div class="col-md-6">
                                    <label for="area" class="form-label fw-semibold text-dark">Diện tích (m²)</label>
                                    <input type="number" name="area" id="area"
                                        class="form-control shadow-sm rounded-3" placeholder="VD: 25"
                                        value="{{ old('area') }}" required>
                                    @error('area')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Địa chỉ --}}
                                <div class="col-md-6">
                                    <label for="province" class="form-label fw-semibold text-dark">Tỉnh/Thành phố</label>
                                    <select id="province" name="province" class="form-select shadow-sm rounded-3" required>
                                        <option value="">-- Chọn Tỉnh/Thành --</option>
                                    </select>
                                    @error('province')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="district" class="form-label fw-semibold text-dark">Quận/Huyện</label>
                                    <select id="district" name="district" class="form-select shadow-sm rounded-3" required>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                    @error('district')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ward" class="form-label fw-semibold text-dark">Phường/Xã</label>
                                    <select id="ward" name="ward" class="form-select shadow-sm rounded-3" required>
                                        <option value="">-- Chọn Phường/Xã --</option>
                                    </select>
                                    @error('ward')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="address" class="form-label fw-semibold text-dark">Địa chỉ chi tiết</label>
                                    <input type="text" name="address" id="address"
                                        class="form-control shadow-sm rounded-3" placeholder="Nhập địa chỉ chi tiết"
                                        value="{{ old('address') }}" required>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Tọa độ bản đồ --}}
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label fw-semibold text-dark">Vĩ độ</label>
                                    <input type="text" name="latitude" id="latitude"
                                        class="form-control shadow-sm rounded-3" placeholder="Vĩ độ"
                                        value="{{ old('latitude') }}" readonly>
                                    @error('latitude')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label fw-semibold text-dark">Kinh độ</label>
                                    <input type="text" name="longitude" id="longitude"
                                        class="form-control shadow-sm rounded-3" placeholder="Kinh độ"
                                        value="{{ old('longitude') }}" readonly>
                                    @error('longitude')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Bản đồ --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Chọn vị trí trên bản đồ</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="search-map"
                                            class="form-control shadow-sm rounded-start-3"
                                            placeholder="Tìm kiếm địa chỉ...">
                                        <button class="btn btn-outline-primary" type="button" id="search-button">
                                            <i class="bi bi-search"></i> Tìm
                                        </button>
                                    </div>
                                    <div id="map" class="rounded-3 shadow-sm" style="height: 400px;"></div>
                                    <small class="text-muted">Nhấn vào bản đồ để chọn vị trí hoặc sử dụng ô tìm
                                        kiếm.</small>
                                </div>

                                {{-- Đặc điểm nổi bật --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Đặc điểm nổi bật</label>
                                    <div class="row g-3">
                                        @foreach ($features as $feature)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input type="checkbox" name="features[]"
                                                        value="{{ $feature->feature_id }}"
                                                        id="feature{{ $feature->feature_id }}" class="form-check-input"
                                                        {{ in_array($feature->feature_id, old('features', [])) ? 'checked' : '' }}>
                                                    <label for="feature{{ $feature->feature_id }}"
                                                        class="form-check-label">
                                                        {{ $feature->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('features')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Mô tả chi tiết --}}
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold text-dark">Mô tả chi
                                        tiết</label>
                                    <textarea name="description" id="description" class="form-control shadow-sm rounded-3" rows="8">{{ old('description') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Ảnh thumbnail --}}
                                <div class="col-md-6">
                                    <label for="thumbnail" class="form-label fw-semibold text-dark">Ảnh thumbnail</label>
                                    <input type="file" name="thumbnail" id="thumbnail"
                                        class="form-control shadow-sm rounded-3">
                                    @error('thumbnail')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Album ảnh --}}
                                <div class="col-md-6">
                                    <label for="gallery" class="form-label fw-semibold text-dark">Album ảnh</label>
                                    <input type="file" name="gallery[]" id="gallery"
                                        class="form-control shadow-sm rounded-3" multiple>
                                    @error('gallery')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-5 text-center">
                                <button type="submit" class="btn btn-gradient-primary px-5 py-3 rounded-pill fw-bold">
                                    <i class="bi bi-upload me-2"></i> Đăng bài
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                placeholder: 'Nhập mô tả chi tiết về nhà trọ...'
            })
            .catch(error => {
                console.error(error);
            });
    </script>

    {{-- Leaflet.js for Map --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    {{-- Load tỉnh/thành, quận/huyện, phường/xã and Map Integration --}}
    <script>
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        const addressInput = document.getElementById('address');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const searchInput = document.getElementById('search-map');
        const searchButton = document.getElementById('search-button');

        // Initialize map
        const map = L.map('map').setView([10.7769, 106.7009], 13); // Default: Ho Chi Minh City
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = null;

        // Store province, district, ward data for reverse lookup
        let provincesData = [];
        let districtsData = {};
        let wardsData = {};

        // Hàm chuẩn hóa tên
        function normalizeName(name) {
            if (!name) return '';
            return name.replace(/^(Thành phố|Tỉnh)\s+/i, '')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();
        }

        // Load provinces
        async function loadProvinces() {
            try {
                const response = await fetch('https://provinces.open-api.vn/api/p/');
                provincesData = await response.json();
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                provincesData.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name; // Use name instead of code
                    option.textContent = item.name;
                    option.dataset.code = item.code; // Store code for API calls
                    provinceSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading provinces:', error);
                alert('Không thể tải danh sách tỉnh/thành. Vui lòng thử lại.');
            }
        }

        // Load districts
        provinceSelect.addEventListener('change', async function() {
            const provinceName = this.value;
            const provinceCode = Array.from(this.options).find(option => option.value === provinceName)?.dataset
                .code;
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

            if (provinceCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                    const data = await response.json();
                    districtsData[provinceCode] = data.districts;
                    data.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.name; // Use name instead of code
                        option.textContent = district.name;
                        option.dataset.code = district.code; // Store code for API calls
                        districtSelect.appendChild(option);
                    });
                    updateMapFromAddress();
                } catch (error) {
                    console.error('Error loading districts:', error);
                    alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại.');
                }
            }
        });

        // Load wards
        districtSelect.addEventListener('change', async function() {
            const districtName = this.value;
            const districtCode = Array.from(this.options).find(option => option.value === districtName)?.dataset
                .code;
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

            if (districtCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                    const data = await response.json();
                    wardsData[districtCode] = data.wards;
                    data.wards.forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.name; // Use name instead of code
                        option.textContent = ward.name;
                        option.dataset.code = ward.code; // Store code for API calls
                        wardSelect.appendChild(option);
                    });
                    updateMapFromAddress();
                } catch (error) {
                    console.error('Error loading wards:', error);
                    alert('Không thể tải danh sách phường/xã. Vui lòng thử lại.');
                }
            }
        });

        // Update map when ward or address changes
        wardSelect.addEventListener('change', updateMapFromAddress);
        addressInput.addEventListener('input', updateMapFromAddress);

        // Function to update map based on address fields
        async function updateMapFromAddress() {
            const provinceName = provinceSelect.value;
            const districtName = districtSelect.value;
            const wardName = wardSelect.value;
            const address = addressInput.value;

            if (!provinceName) return;

            let query = [address, wardName, districtName, provinceName].filter(Boolean).join(', ');

            if (query) {
                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat).toFixed(6);
                        const lon = parseFloat(data[0].lon).toFixed(6);
                        map.setView([lat, lon], 15);
                        latitudeInput.value = lat;
                        longitudeInput.value = lon;

                        if (marker) {
                            marker.setLatLng([lat, lon]);
                        } else {
                            marker = L.marker([lat, lon]).addTo(map);
                        }
                    }
                } catch (error) {
                    console.error('Error searching address:', error);
                    alert('Không thể tìm kiếm địa chỉ. Vui lòng thử lại.');
                }
            }
        }

        // Map click event for reverse geocoding
        map.on('click', async function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            latitudeInput.value = lat;
            longitudeInput.value = lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                if (data.address) {
                    const {
                        city,
                        state,
                        county,
                        suburb,
                        road
                    } = data.address;

                    const provinceName = state || city || '';
                    const province = provincesData.find(p =>
                        normalizeName(p.name).includes(normalizeName(provinceName))
                    );

                    if (province) {
                        provinceSelect.value = province.name;
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                        const districtResponse = await fetch(
                            `https://provinces.open-api.vn/api/p/${province.code}?depth=2`);
                        const districtData = await districtResponse.json();
                        districtsData[province.code] = districtData.districts;
                        districtData.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name;
                            option.textContent = district.name;
                            option.dataset.code = district.code;
                            districtSelect.appendChild(option);
                        });

                        const district = districtData.districts.find(d =>
                            normalizeName(d.name).includes(normalizeName(county))
                        );
                        if (district) {
                            districtSelect.value = district.name;
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                            const wardResponse = await fetch(
                                `https://provinces.open-api.vn/api/d/${district.code}?depth=2`);
                            const wardData = await wardResponse.json();
                            wardsData[district.code] = wardData.wards;
                            wardData.wards.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.name;
                                option.textContent = ward.name;
                                option.dataset.code = ward.code;
                                wardSelect.appendChild(option);
                            });

                            const ward = wardData.wards.find(w =>
                                normalizeName(w.name).includes(normalizeName(suburb))
                            );
                            if (ward) {
                                wardSelect.value = ward.name;
                            }
                        }

                        addressInput.value = road || '';
                    } else {
                        provinceSelect.value = '';
                        districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        addressInput.value = road || '';
                        alert('Không tìm thấy tỉnh/thành phù hợp. Vui lòng chọn thủ công.');
                    }
                }
            } catch (error) {
                console.error('Error reverse geocoding:', error);
                alert('Có lỗi xảy ra khi lấy thông tin địa chỉ. Vui lòng thử lại.');
            }
        });

        // Search address
        async function searchAddress() {
            const query = searchInput.value;
            if (!query) {
                alert('Vui lòng nhập địa chỉ để tìm kiếm.');
                return;
            }

            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat).toFixed(6);
                    const lon = parseFloat(data[0].lon).toFixed(6);
                    map.setView([lat, lon], 15);
                    latitudeInput.value = lat;
                    longitudeInput.value = lon;

                    if (marker) {
                        marker.setLatLng([lat, lon]);
                    } else {
                        marker = L.marker([lat, lon]).addTo(map);
                    }

                    const addressResponse = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const addressData = await addressResponse.json();
                    if (addressData.address) {
                        const {
                            city,
                            state,
                            county,
                            suburb,
                            road
                        } = addressData.address;

                        const provinceName = state || city || '';
                        const province = provincesData.find(p =>
                            normalizeName(p.name).includes(normalizeName(provinceName))
                        );

                        if (province) {
                            provinceSelect.value = province.name;
                            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                            const districtResponse = await fetch(
                                `https://provinces.open-api.vn/api/p/${province.code}?depth=2`);
                            const districtData = await districtResponse.json();
                            districtsData[province.code] = districtData.districts;
                            districtData.districts.forEach(district => {
                                const option = document.createElement('option');
                                option.value = district.name;
                                option.textContent = district.name;
                                option.dataset.code = district.code;
                                districtSelect.appendChild(option);
                            });

                            const district = districtData.districts.find(d =>
                                normalizeName(d.name).includes(normalizeName(county))
                            );
                            if (district) {
                                districtSelect.value = district.name;
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                                const wardResponse = await fetch(
                                    `https://provinces.open-api.vn/api/d/${district.code}?depth=2`);
                                const wardData = await wardResponse.json();
                                wardsData[district.code] = wardData.wards;
                                wardData.wards.forEach(ward => {
                                    const option = document.createElement('option');
                                    option.value = ward.name;
                                    option.textContent = ward.name;
                                    option.dataset.code = ward.code;
                                    wardSelect.appendChild(option);
                                });

                                const ward = wardData.wards.find(w =>
                                    normalizeName(w.name).includes(normalizeName(suburb))
                                );
                                if (ward) {
                                    wardSelect.value = ward.name;
                                }
                            }

                            addressInput.value = road || '';
                        } else {
                            provinceSelect.value = '';
                            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            addressInput.value = road || '';
                            alert('Không tìm thấy tỉnh/thành phù hợp. Vui lòng chọn thủ công.');
                        }
                    }
                } else {
                    alert('Không tìm thấy địa chỉ. Vui lòng thử lại.');
                }
            } catch (error) {
                console.error('Error searching address:', error);
                alert('Có lỗi xảy ra khi tìm kiếm địa chỉ. Vui lòng thử lại.');
            }
        }

        searchButton.addEventListener('click', searchAddress);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchAddress();
            }
        });

        // Load provinces on page load
        loadProvinces();
    </script>

    {{-- Custom Styles --}}
    <style>
        .btn-gradient-primary {
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, #00c6ff, #0072ff);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control,
        .form-select {
            border-color: #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 0.2rem rgba(79, 172, 254, 0.25);
        }

        .form-check-input:checked {
            background-color: #4facfe;
            border-color: #4facfe;
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        #map {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
        }

        .leaflet-container {
            background: #f8f9fa;
        }
    </style>
@endsection
