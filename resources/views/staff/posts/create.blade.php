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

                                <div class="col-md-6">
                                    <label for="room_id" class="form-label fw-semibold text-dark">Phòng</label>
                                    <select name="room_id" id="room_id" class="form-select shadow-sm rounded-3" required
                                        disabled>
                                        <option value="">-- Chọn Phòng --</option>
                                    </select>
                                    @error('room_id')
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
                                    <label for="address" class="form-label fw-semibold text-dark">Địa chỉ chi
                                        tiết</label>
                                    <input type="text" name="address" id="address"
                                        class="form-control shadow-sm rounded-3" placeholder="VD: Số 123, Ngõ 45"
                                        value="{{ old('address') }}" required>
                                    @error('address')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Hidden fields for latitude and longitude --}}
                                <div class="col-md-6">
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude"
                                        value="{{ old('longitude') }}">
                                    @error('latitude')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
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
                                            placeholder="VD: Số 123, Ngõ 45, Phường Cống Vị, Quận Ba Đình, Hà Nội">
                                        <button class="btn btn-outline-primary" type="button" id="search-button">
                                            <i class="bi bi-search"></i> Tìm
                                        </button>
                                    </div>
                                    <div id="map" class="rounded-3 shadow-sm" style="height: 400px;"></div>
                                    <small class="text-muted">Nhấn vào bản đồ để chọn vị trí hoặc sử dụng ô tìm kiếm. Nếu
                                        địa chỉ chi tiết không tìm thấy, hãy thử chọn trên bản đồ hoặc nhập địa chỉ tổng
                                        quát hơn.</small>
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
                                        class="form-control shadow-sm rounded-3" accept="image/*">
                                    @error('thumbnail')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <div id="thumbnail-preview" class="mt-3">
                                        <img id="thumbnail-img" class="img-fluid rounded-3 shadow-sm"
                                            style="max-height: 200px; display: none;" alt="Thumbnail Preview">
                                    </div>
                                </div>

                                {{-- Album ảnh --}}
                                <div class="col-md-6">
                                    <label for="gallery" class="form-label fw-semibold text-dark">Album ảnh</label>
                                    <input type="file" name="gallery[]" id="gallery"
                                        class="form-control shadow-sm rounded-3" accept="image/*" multiple>
                                    @error('gallery')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <div id="gallery-preview" class="mt-3 d-flex flex-wrap gap-2"></div>
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

        {{-- JavaScript for Address Handling and Map Integration --}}
        <script>
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            const addressInput = document.getElementById('address');
            const searchInput = document.getElementById('search-map');
            const searchButton = document.getElementById('search-button');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            // Initialize map
            const map = L.map('map').setView([10.7769, 106.7009], 13); // Default: Ho Chi Minh City
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            let marker = null;

            // Store province, district, ward data
            let provincesData = [];
            let districtsData = {};
            let wardsData = {};

            // Normalize Vietnamese text for comparison
            function normalizeName(name) {
                if (!name) return '';
                return name
                    .replace(/^(Thành phố|Tỉnh|Quận|Huyện|Phường|Xã)\s+/i, '')
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .trim();
            }

            // Simplify address for search
            function simplifyAddress(address) {
                if (!address) return '';
                return address
                    .toLowerCase()
                    .replace(/\b(số nhà|số|ngõ|ngách|hẻm|đường|phố|hem|duong|pho)\b/gi, '')
                    .replace(/[,;]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            // Fetch address from Nominatim
            async function fetchAddress(query) {
                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=vn&addressdetails=1`
                    );
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching address:', error);
                    return [];
                }
            }

            // Load provinces
            async function loadProvinces() {
                try {
                    const response = await fetch('https://provinces.open-api.vn/api/p/');
                    provincesData = await response.json();
                    provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                    provincesData.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.name;
                        option.textContent = item.name;
                        option.dataset.code = item.code;
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
                            option.value = district.name;
                            option.textContent = district.name;
                            option.dataset.code = district.code;
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
                            option.value = ward.name;
                            option.textContent = ward.name;
                            option.dataset.code = ward.code;
                            wardSelect.appendChild(option);
                        });
                        updateMapFromAddress();
                    } catch (error) {
                        console.error('Error loading wards:', error);
                        alert('Không thể tải danh sách phường/xã. Vui lòng thử lại.');
                    }
                }
            });

            // Update map based on address fields
            async function updateMapFromAddress() {
                const provinceName = provinceSelect.value;
                const districtName = districtSelect.value;
                const wardName = wardSelect.value;
                const address = addressInput.value;

                if (!provinceName) return;

                // Try full address first
                let query = [address, wardName, districtName, provinceName].filter(Boolean).join(', ');
                let data = await fetchAddress(query);

                // If no results, try simplified address
                if (!data.length) {
                    const simplifiedAddress = simplifyAddress(address);
                    query = [simplifiedAddress, wardName, districtName, provinceName].filter(Boolean).join(', ');
                    data = await fetchAddress(query);
                }

                // If still no results, try without address
                if (!data.length && (wardName || districtName || provinceName)) {
                    query = [wardName, districtName, provinceName].filter(Boolean).join(', ');
                    data = await fetchAddress(query);
                }

                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], data[0].importance < 0.3 ? 13 : 17);

                    if (marker) {
                        marker.setLatLng([lat, lon]);
                    } else {
                        marker = L.marker([lat, lon]).addTo(map);
                    }

                    // Update hidden inputs with latitude and longitude
                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lon.toFixed(6);
                } else {
                    alert(
                        'Không tìm thấy địa chỉ chính xác. Vui lòng thử nhập địa chỉ ngắn hơn hoặc chọn trực tiếp trên bản đồ.'
                    );
                }
            }

            // Map click event for reverse geocoding
            map.on('click', async function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }

                // Update hidden inputs with latitude and longitude
                latitudeInput.value = lat.toFixed(6);
                longitudeInput.value = lng.toFixed(6);

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&countrycodes=vn`
                    );
                    const data = await response.json();
                    if (data.address) {
                        const {
                            city,
                            state,
                            county,
                            suburb,
                            road,
                            house_number
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
                                `https://provinces.open-api.vn/api/p/${province.code}?depth=2`
                            );
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
                                    `https://provinces.open-api.vn/api/d/${district.code}?depth=2`
                                );
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

                            addressInput.value = [house_number, road].filter(Boolean).join(' ') || '';
                        } else {
                            provinceSelect.value = '';
                            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            addressInput.value = [house_number, road].filter(Boolean).join(' ') || '';
                            alert('Không tìm thấy tỉnh/thành phù hợp. Vui lòng chọn thủ công.');
                        }
                    } else {
                        alert('Không thể xác định địa chỉ từ vị trí này. Vui lòng chọn thủ công.');
                    }
                } catch (error) {
                    console.error('Error reverse geocoding:', error);
                    alert('Có lỗi xảy ra khi lấy thông tin địa chỉ. Vui lòng thử lại.');
                }
            });

            // Search address
            async function searchAddress() {
                let query = searchInput.value.trim();
                if (!query) {
                    alert('Vui lòng nhập địa chỉ để tìm kiếm.');
                    return;
                }

                // Try full address first
                let data = await fetchAddress(query);

                // If no results, try simplified address
                if (!data.length) {
                    const simplified = simplifyAddress(query);
                    if (simplified !== query) {
                        data = await fetchAddress(simplified);
                    }
                }

                // If still no results, try broader components
                if (!data.length) {
                    const parts = query.split(',').map(part => part.trim());
                    if (parts.length > 1) {
                        query = parts.slice(-3).join(', ');
                        data = await fetchAddress(query);
                    }
                }

                if (data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    map.setView([lat, lon], data[0].importance < 0.3 ? 13 : 17);

                    if (marker) {
                        marker.setLatLng([lat, lon]);
                    } else {
                        marker = L.marker([lat, lon]).addTo(map);
                    }

                    // Update hidden inputs with latitude and longitude
                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lon.toFixed(6);

                    // Update form fields
                    try {
                        const addressResponse = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1&countrycodes=vn`
                        );
                        const addressData = await addressResponse.json();
                        if (addressData.address) {
                            const {
                                city,
                                state,
                                county,
                                suburb,
                                road,
                                house_number
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
                                    `https://provinces.open-api.vn/api/p/${province.code}?depth=2`
                                );
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
                                        `https://provinces.open-api.vn/api/d/${district.code}?depth=2`
                                    );
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

                                addressInput.value = [house_number, road].filter(Boolean).join(' ') || '';
                            } else {
                                provinceSelect.value = '';
                                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                                addressInput.value = [house_number, road].filter(Boolean).join(' ') || '';
                                alert('Không tìm thấy tỉnh/thành phù hợp. Vui lòng chọn thủ công.');
                            }
                        }
                    } catch (error) {
                        console.error('Error reverse geocoding:', error);
                        alert('Có lỗi xảy ra khi lấy thông tin địa chỉ. Vui lòng thử lại.');
                    }
                } else {
                    alert(
                        'Không tìm thấy địa chỉ. Vui lòng thử nhập địa chỉ ngắn hơn (VD: Phường, Quận, Tỉnh) hoặc chọn trực tiếp trên bản đồ.'
                    );
                }
            }

            searchButton.addEventListener('click', searchAddress);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchAddress();
                }
            });

            // Update map when address fields change
            wardSelect.addEventListener('change', updateMapFromAddress);
            addressInput.addEventListener('input', debounce(updateMapFromAddress, 500));

            // Debounce function to prevent excessive API calls
            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            // Image preview functionality
            const thumbnailInput = document.getElementById('thumbnail');
            const thumbnailImg = document.getElementById('thumbnail-img');
            const galleryInput = document.getElementById('gallery');
            const galleryPreview = document.getElementById('gallery-preview');

            thumbnailInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        thumbnailImg.src = e.target.result;
                        thumbnailImg.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    thumbnailImg.src = '';
                    thumbnailImg.style.display = 'none';
                }
            });

            galleryInput.addEventListener('change', function(e) {
                galleryPreview.innerHTML = '';
                const files = e.target.files;
                Array.from(files).forEach(file => {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-fluid rounded-3 shadow-sm';
                            img.style.maxHeight = '100px';
                            galleryPreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Load provinces on page load
            loadProvinces();
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#property_id').change(function() {
                    var propertyId = $(this).val();
                    var roomSelect = $('#room_id');

                    // Xóa các tùy chọn hiện tại trong dropdown phòng
                    roomSelect.empty().append('<option value="">-- Chọn Phòng --</option>');
                    roomSelect.prop('disabled', true);

                    if (propertyId) {
                        // Gửi yêu cầu AJAX để lấy danh sách phòng
                        $.ajax({
                            url: '/get-rooms/' + propertyId, // URL endpoint để lấy danh sách phòng
                            type: 'GET',
                            success: function(data) {
                                // Kích hoạt dropdown phòng
                                roomSelect.prop('disabled', false);

                                // Thêm các phòng vào dropdown
                                $.each(data.rooms, function(index, room) {
                                    roomSelect.append('<option value="' + room.room_id +
                                        '">' + room.room_id + '</option>');
                                });
                            },
                            error: function() {
                                alert('Không thể tải danh sách phòng. Vui lòng thử lại.');
                            }
                        });
                    }
                });
            });
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
