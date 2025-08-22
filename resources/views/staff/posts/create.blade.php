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

                        <form action="{{ route('staff.posts.store') }}" method="POST" enctype="multipart/form-data"
                            id="rental-form">
                            @csrf

                            <div class="row g-4">
                                {{-- Loại chuyên mục --}}
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold text-dark">Loại chuyên
                                        mục</label>
                                    <select name="category_id" id="category_id" class="form-select shadow-sm rounded-3"
                                        required aria-label="Chọn loại chuyên mục">
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
                                        required aria-label="Chọn chủ trọ">
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
                                        required aria-label="Chọn bất động sản">
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
                                        disabled aria-label="Chọn phòng">
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
                                        value="{{ old('title') }}" required aria-label="Tiêu đề bài viết">
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
                                        value="{{ old('price') }}" required aria-label="Giá cho thuê">
                                    @error('price')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Diện tích --}}
                                <div class="col-md-6">
                                    <label for="area" class="form-label fw-semibold text-dark">Diện tích (m²)</label>
                                    <input type="number" name="area" id="area"
                                        class="form-control shadow-sm rounded-3" placeholder="VD: 25"
                                        value="{{ old('area') }}" min="1" required aria-label="Diện tích">
                                    @error('area')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Địa chỉ --}}
                                <div class="col-md-6">
                                    <label for="province" class="form-label fw-semibold text-dark">Tỉnh/Thành phố</label>
                                    <select id="province" name="province" class="form-select shadow-sm rounded-3" required
                                        aria-label="Chọn tỉnh/thành phố">
                                        <option value="">-- Chọn Tỉnh/Thành --</option>
                                    </select>
                                    @error('province')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="district" class="form-label fw-semibold text-dark">Quận/Huyện</label>
                                    <select id="district" name="district" class="form-select shadow-sm rounded-3" required
                                        aria-label="Chọn quận/huyện">
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                    @error('district')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ward" class="form-label fw-semibold text-dark">Phường/Xã</label>
                                    <select id="ward" name="ward" class="form-select shadow-sm rounded-3"
                                        required aria-label="Chọn phường/xã">
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
                                        value="{{ old('address') }}" required aria-label="Địa chỉ chi tiết">
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

                                {{-- Ngày dọn vào --}}
                                <div class="col-md-6">
                                    <label for="move_in_date" class="form-label fw-semibold text-dark">Ngày dọn
                                        vào</label>
                                    <input type="date" name="move_in_date" id="move_in_date"
                                        class="form-control shadow-sm rounded-3" value="{{ old('move_in_date') }}"
                                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required
                                        aria-label="Ngày dọn vào">
                                    @error('move_in_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Bản đồ --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Chọn vị trí trên bản đồ</label>
                                    <div class="input-group mb-3">
                                        <input type="text" id="search-map"
                                            class="form-control shadow-sm rounded-start-3"
                                            placeholder="VD: Số 123, Ngõ 45, Phường Cống Vị, Quận Ba Đình, Hà Nội"
                                            aria-label="Tìm kiếm địa chỉ">
                                        <button class="btn btn-outline-primary" type="button" id="search-button">
                                            <i class="bi bi-search"></i> Tìm
                                        </button>
                                    </div>
                                    <div id="map" class="rounded-3 shadow-sm" style="height: 400px;"
                                        role="region" aria-label="Bản đồ chọn vị trí"></div>
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
                                                        {{ in_array($feature->feature_id, old('features', [])) ? 'checked' : '' }}
                                                        aria-label="Tính năng {{ $feature->name }}">
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
                                    <textarea name="description" id="description" class="form-control shadow-sm rounded-3" rows="8"
                                        aria-label="Mô tả chi tiết">{{ old('description') }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Ảnh thumbnail --}}
                                <div class="col-md-6">
                                    <label for="thumbnail" class="form-label fw-semibold text-dark">Ảnh thumbnail</label>
                                    <input type="file" name="thumbnail" id="thumbnail"
                                        class="form-control shadow-sm rounded-3" accept="image/*"
                                        aria-label="Chọn ảnh thumbnail">
                                    @error('thumbnail')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <div id="thumbnail-preview" class="mt-3">
                                        <img id="thumbnail-img" class="img-fluid rounded-3 shadow-sm"
                                            style="max-height: 200px; display: none;" alt="Ảnh thumbnail">
                                    </div>
                                </div>

                                {{-- Album ảnh --}}
                                <div class="col-md-6">
                                    <label for="gallery" class="form-label fw-semibold text-dark">Album ảnh</label>
                                    <input type="file" name="gallery[]" id="gallery"
                                        class="form-control shadow-sm rounded-3" accept="image/*" multiple
                                        aria-label="Chọn album ảnh">
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
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"
            integrity="sha512-6zqQ3s3GyU3aChu6k3j2ZSQur1oI3djC5g7zQ3hTAF7gqSjoI4QZeY4I8S2vM0Uo3kT4q1d2yA1ENxt2nD6p2RQ=="
            crossorigin="anonymous"></script>
        <script>
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                    placeholder: 'Nhập mô tả chi tiết về nhà trọ...'
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });
        </script>

        {{-- Leaflet JS --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />

        {{-- jQuery --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        {{-- JavaScript for Address Handling, Map Integration, and Form Validation --}}
        <script>
            const VIET_MAP_API_KEY = '{{ env('VIET_MAP_API_KEY') }}';
            const LOCATIONIQ_API_KEY = '{{ env('LOCATIONIQ_API_KEY') }}';
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            const addressInput = document.getElementById('address');
            const searchInput = document.getElementById('search-map');
            const searchButton = document.getElementById('search-button');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            // Initialize Leaflet Map with Raster Light style
            const map = L.map('map', {
                center: [10.7769, 106.7009], // Ho Chi Minh City [lat, lng]
                zoom: 13,
                attributionControl: true
            });

            L.tileLayer(`https://maps.vietmap.vn/maps/tiles/lm/{z}/{x}/{y}@2x.png?apikey=${VIET_MAP_API_KEY}`, {
                maxZoom: 18,
                attribution: 'Map data &copy; <a href="https://vietmap.vn">VietMap</a>'
            }).addTo(map);

            let marker = null;

            // Store province, district, ward data
            let provincesData = [];
            let districtsData = {};
            let wardsData = {};

            // Cache keys for localStorage
            const CACHE_KEY_PROVINCES = 'provinces_data';
            const CACHE_KEY_DISTRICTS = 'districts_data';
            const CACHE_KEY_WARDS = 'wards_data';

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

            // Fetch address from LocationIQ
            async function fetchAddress(query) {
                try {
                    const response = await fetch(
                        `https://us1.locationiq.com/v1/search.php?key=${LOCATIONIQ_API_KEY}&q=${encodeURIComponent(query)}&format=json&countrycodes=vn&addressdetails=1`
                    );
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching address:', error);
                    return [];
                }
            }

            // Load provinces
            async function loadProvinces() {
                const cachedProvinces = localStorage.getItem(CACHE_KEY_PROVINCES);
                if (cachedProvinces) {
                    provincesData = JSON.parse(cachedProvinces);
                    populateProvinces();
                    return;
                }

                try {
                    const response = await fetch('https://provinces.open-api.vn/api/p/');
                    provincesData = await response.json();
                    localStorage.setItem(CACHE_KEY_PROVINCES, JSON.stringify(provincesData));
                    populateProvinces();
                } catch (error) {
                    console.error('Error loading provinces:', error);
                    alert('Không thể tải danh sách tỉnh/thành. Vui lòng thử lại.');
                }
            }

            function populateProvinces() {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                provincesData.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    option.dataset.code = item.code;
                    provinceSelect.appendChild(option);
                });
            }

            // Load districts
            provinceSelect.addEventListener('change', async function() {
                const provinceName = this.value;
                const provinceCode = Array.from(this.options).find(option => option.value === provinceName)?.dataset
                    .code;
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                if (provinceCode) {
                    const cacheKey = `${CACHE_KEY_DISTRICTS}_${provinceCode}`;
                    const cachedDistricts = localStorage.getItem(cacheKey);
                    if (cachedDistricts) {
                        districtsData[provinceCode] = JSON.parse(cachedDistricts);
                        populateDistricts(provinceCode);
                        updateMapFromAddress();
                        return;
                    }

                    try {
                        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                        const data = await response.json();
                        districtsData[provinceCode] = data.districts;
                        localStorage.setItem(cacheKey, JSON.stringify(data.districts));
                        populateDistricts(provinceCode);
                        updateMapFromAddress();
                    } catch (error) {
                        console.error('Error loading districts:', error);
                        alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại.');
                    }
                }
            });

            function populateDistricts(provinceCode) {
                districtsData[provinceCode].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.name;
                    option.textContent = district.name;
                    option.dataset.code = district.code;
                    districtSelect.appendChild(option);
                });
            }

            // Load wards
            districtSelect.addEventListener('change', async function() {
                const districtName = this.value;
                const districtCode = Array.from(this.options).find(option => option.value === districtName)?.dataset
                    .code;
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                if (districtCode) {
                    const cacheKey = `${CACHE_KEY_WARDS}_${districtCode}`;
                    const cachedWards = localStorage.getItem(cacheKey);
                    if (cachedWards) {
                        wardsData[districtCode] = JSON.parse(cachedWards);
                        populateWards(districtCode);
                        updateMapFromAddress();
                        return;
                    }

                    try {
                        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                        const data = await response.json();
                        wardsData[districtCode] = data.wards;
                        localStorage.setItem(cacheKey, JSON.stringify(data.wards));
                        populateWards(districtCode);
                        updateMapFromAddress();
                    } catch (error) {
                        console.error('Error loading wards:', error);
                        alert('Không thể tải danh sách phường/xã. Vui lòng thử lại.');
                    }
                }
            });

            function populateWards(districtCode) {
                wardsData[districtCode].forEach(ward => {
                    const option = document.createElement('option');
                    option.value = ward.name;
                    option.textContent = ward.name;
                    option.dataset.code = ward.code;
                    wardSelect.appendChild(option);
                });
            }

            // Update map based on address fields
            async function updateMapFromAddress() {
                const provinceName = provinceSelect.value;
                const districtName = districtSelect.value;
                const wardName = wardSelect.value;
                const address = addressInput.value;

                if (!provinceName) return;

                let query = [address, wardName, districtName, provinceName].filter(Boolean).join(', ');
                let data = await fetchAddress(query);

                if (!data.length) {
                    const simplifiedAddress = simplifyAddress(address);
                    query = [simplifiedAddress, wardName, districtName, provinceName].filter(Boolean).join(', ');
                    data = await fetchAddress(query);
                }

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

                latitudeInput.value = lat.toFixed(6);
                longitudeInput.value = lng.toFixed(6);

                try {
                    const response = await fetch(
                        `https://us1.locationiq.com/v1/reverse.php?key=${LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&addressdetails=1&countrycodes=vn`
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
                        const province = provincesData.find(p => normalizeName(p.name).includes(normalizeName(
                            provinceName)));

                        if (province) {
                            provinceSelect.value = province.name;
                            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                            const cacheKey = `${CACHE_KEY_DISTRICTS}_${province.code}`;
                            const cachedDistricts = localStorage.getItem(cacheKey);
                            if (cachedDistricts) {
                                districtsData[province.code] = JSON.parse(cachedDistricts);
                            } else {
                                const districtResponse = await fetch(
                                    `https://provinces.open-api.vn/api/p/${province.code}?depth=2`);
                                const districtData = await districtResponse.json();
                                districtsData[province.code] = districtData.districts;
                                localStorage.setItem(cacheKey, JSON.stringify(districtData.districts));
                            }

                            populateDistricts(province.code);
                            const district = districtsData[province.code].find(d => normalizeName(d.name).includes(
                                normalizeName(county)));
                            if (district) {
                                districtSelect.value = district.name;
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                                const wardCacheKey = `${CACHE_KEY_WARDS}_${district.code}`;
                                const cachedWards = localStorage.getItem(wardCacheKey);
                                if (cachedWards) {
                                    wardsData[district.code] = JSON.parse(cachedWards);
                                } else {
                                    const wardResponse = await fetch(
                                        `https://provinces.open-api.vn/api/d/${district.code}?depth=2`);
                                    const wardData = await wardResponse.json();
                                    wardsData[district.code] = wardData.wards;
                                    localStorage.setItem(wardCacheKey, JSON.stringify(wardData.wards));
                                }

                                populateWards(district.code);
                                const ward = wardsData[district.code].find(w => normalizeName(w.name).includes(
                                    normalizeName(suburb)));
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

                let data = await fetchAddress(query);
                if (!data.length) {
                    const simplified = simplifyAddress(query);
                    if (simplified !== query) {
                        data = await fetchAddress(simplified);
                    }
                }

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

                    latitudeInput.value = lat.toFixed(6);
                    longitudeInput.value = lon.toFixed(6);

                    try {
                        const addressResponse = await fetch(
                            `https://us1.locationiq.com/v1/reverse.php?key=${LOCATIONIQ_API_KEY}&lat=${lat}&lon=${lng}&format=json&addressdetails=1&countrycodes=vn`
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
                            const province = provincesData.find(p => normalizeName(p.name).includes(normalizeName(
                                provinceName)));

                            if (province) {
                                provinceSelect.value = province.name;
                                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                                const cacheKey = `${CACHE_KEY_DISTRICTS}_${province.code}`;
                                const cachedDistricts = localStorage.getItem(cacheKey);
                                if (cachedDistricts) {
                                    districtsData[province.code] = JSON.parse(cachedDistricts);
                                } else {
                                    const districtResponse = await fetch(
                                        `https://provinces.open-api.vn/api/p/${province.code}?depth=2`);
                                    const districtData = await districtResponse.json();
                                    districtsData[province.code] = districtData.districts;
                                    localStorage.setItem(cacheKey, JSON.stringify(districtData.districts));
                                }

                                populateDistricts(province.code);
                                const district = districtsData[province.code].find(d => normalizeName(d.name).includes(
                                    normalizeName(county)));
                                if (district) {
                                    districtSelect.value = district.name;
                                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

                                    const wardCacheKey = `${CACHE_KEY_WARDS}_${district.code}`;
                                    const cachedWards = localStorage.getItem(wardCacheKey);
                                    if (cachedWards) {
                                        wardsData[district.code] = JSON.parse(cachedWards);
                                    } else {
                                        const wardResponse = await fetch(
                                            `https://provinces.open-api.vn/api/d/${district.code}?depth=2`);
                                        const wardData = await wardResponse.json();
                                        wardsData[district.code] = wardData.wards;
                                        localStorage.setItem(wardCacheKey, JSON.stringify(wardData.wards));
                                    }

                                    populateWards(district.code);
                                    const ward = wardsData[district.code].find(w => normalizeName(w.name).includes(
                                        normalizeName(suburb)));
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

            wardSelect.addEventListener('change', updateMapFromAddress);
            addressInput.addEventListener('input', debounce(updateMapFromAddress, 500));

            // Debounce function
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
                            img.alt = 'Ảnh trong album';
                            galleryPreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Room selection
            $(document).ready(function() {
                $('#property_id').change(function() {
                    const propertyId = $(this).val();
                    const roomSelect = $('#room_id');

                    roomSelect.empty().append('<option value="">-- Chọn Phòng --</option>');
                    roomSelect.prop('disabled', true);

                    if (propertyId) {
                        $.ajax({
                            url: '/get-rooms/' + propertyId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                if (data.rooms && Array.isArray(data.rooms)) {
                                    roomSelect.prop('disabled', false);
                                    $.each(data.rooms, function(index, room) {
                                        roomSelect.append(
                                            `<option value="${room.room_id}">${room.room_number}</option>`
                                        );
                                    });
                                } else {
                                    alert('Không tìm thấy phòng cho bất động sản này.');
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('Error fetching rooms:', textStatus, errorThrown);
                                alert('Không thể tải danh sách phòng. Vui lòng thử lại.');
                            }
                        });
                    }
                });

                // Client-side validation
                $('#rental-form').on('submit', function(e) {
                    const price = $('#price').val().replace(/[^0-9]/g, '');
                    const area = $('#area').val();
                    const latitude = $('#latitude').val();
                    const longitude = $('#longitude').val();

                    if (!price || isNaN(price) || Number(price) <= 0) {
                        e.preventDefault();
                        alert('Vui lòng nhập giá cho thuê hợp lệ.');
                        $('#price').focus();
                        return;
                    }

                    if (!area || isNaN(area) || Number(area) <= 0) {
                        e.preventDefault();
                        alert('Vui lòng nhập diện tích hợp lệ.');
                        $('#area').focus();
                        return;
                    }

                    if (!latitude || !longitude) {
                        e.preventDefault();
                        alert('Vui lòng chọn vị trí trên bản đồ.');
                        $('#search-map').focus();
                        return;
                    }
                });

                // Format price input
                $('#price').on('input', function() {
                    let value = $(this).val().replace(/[^0-9]/g, '');
                    if (value) {
                        $(this).val(Number(value).toLocaleString('vi-VN'));
                    }
                });
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

            .form-control:not(:placeholder-shown):not([type="file"]),
            .form-control[type="file"]:valid,
            .form-select option[value]:not([value=""]):checked,
            textarea.form-control:not(:empty) {
                box-shadow: 0 0 8px rgba(0, 255, 0, 0.5);
                border-color: #00cc00;
            }

            .form-check-input:checked {
                background-color: #00cc00;
                border-color: #00cc00;
                box-shadow: 0 0 8px rgba(0, 255, 0, 0.5);
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
        </style>
    @endsection
