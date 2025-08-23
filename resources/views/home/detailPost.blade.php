@extends('home.layouts.app')

@section('title', $post->title)

@section('styles')
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/@mapbox/polyline@1.2.0/src/polyline.min.js"></script> --}}

    <style>
        .user-location-icon {
            background-color: #ff4500;
            border-radius: 50%;
            width: 12px;
            height: 12px;
            border: 2px solid #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }
        /* Map Styles */
      #map {
            width: 100% !important;
            height: 500px !important;
            min-height: 300px;
            position: relative;
        }
        .map-container {
            width: 100% !important;
            height: 500px !important;
            min-height: 300px !important;
            border-radius: 8px;
            overflow: hidden;
            position: relative !important;
            display: block !important;
        }
        #routeInfo {
                transition: opacity 0.3s ease-in-out;
            }
            #routeInfo.show {
                display: block;
                opacity: 1;
            }
            #routeInfo.hide {
                opacity: 0;
                display: none;
            }

        /* Leaflet container fix */
        .leaflet-container {
            width: 100% !important;
            height: 100% !important;
        }

        /* Card body containing map */
        .map-card .card-body {
            padding: 0 !important;
            height: auto !important;
            min-height: auto !important;
        }

        /* Gallery Styles */
        .gallery-main img {
            max-height: 400px;
            max-width: 100%;
            object-fit: cover;
            width: 100%;
            border-radius: 8px;
            aspect-ratio: 16 / 9;
        }

        .gallery-side {
            position: relative;
            width: 200px;
            height: 200px;
        }

        .gallery-side img {
            position: absolute;
            width: 200px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .gallery-side img:hover {
            transform: scale(1.05);
            z-index: 5;
        }

        .gallery-side img:nth-child(1) {
            top: 0;
            left: 0;
            z-index: 3;
        }

        .gallery-side img:nth-child(2) {
            top: 20px;
            left: 20px;
            z-index: 2;
        }
.gallery-side img:nth-child(3) {
            top: 40px;
            left: 40px;
            z-index: 1;
        }

        /* Other Styles */
        .card-body {
            min-height: 0;
            padding: 20px;
        }

        .nearby-posts {
            max-height: 300px;
            overflow-y: auto;
        }

        .nearby-posts .property-item {
            transition: background-color 0.2s;
        }

        .nearby-posts .property-item:hover {
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .gallery-main img {
                max-height: 250px;
            }

            .gallery-side {
                display: none;
            }

            #map {
                width: 100% !important;
                height: 250px !important;
                min-height: 250px !important;
                max-height: 250px !important;
            }

            .map-container {
                height: 250px !important;
                min-height: 250px !important;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Gallery Section -->
    <section class="gallery-section pt-4 pb-4 d-none d-xl-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-7 pe-lg-2">
                    <div class="gallery-main">
                        <a href="{{ asset('storage/' . $post->thumbnail) }}" class="mfp-gallery">
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                                class="img-fluid rounded w-100 object-fit-cover" loading="lazy">
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 ps-lg-2">
                    <div class="gallery-side d-flex flex-column gap-2">
                        @php
                            $galleryImages = $post->gallery ? array_slice(json_decode($post->gallery, true), 0, 3) : [];
                        @endphp
                        @foreach ($galleryImages as $image)
                            <a href="{{ asset('storage/' . $image) }}" class="mfp-gallery">
                                <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="img-fluid rounded"
                                    loading="lazy">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Gallery Slider -->
    <section class="gallery-slider d-block d-xl-none">
        <div class="container">
            <div class="gallery-carousel owl-carousel">
                <div class="gallery-item">
                    <a href="{{ asset('storage/' . $post->thumbnail) }}" class="mfp-gallery">
<img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}"
                            class="img-fluid rounded" loading="lazy">
                    </a>
                </div>
                @foreach ($galleryImages as $image)
                    <div class="gallery-item">
                        <a href="{{ asset('storage/' . $image) }}" class="mfp-gallery">
                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="img-fluid rounded"
                                loading="lazy">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Property Details Section -->
    <section class="property-details pt-5 pb-5">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8 col-md-12">
                    <!-- About Property -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Thông tin: {{ $post->title }}</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><strong>Loại Danh Mục:</strong> {{ $post->category->name }}</li>
                                <li><strong>Loại bất động sản:</strong> {{ $post->property->name ?? 'Không xác định' }}
                                </li>
                                <li><strong>Tiền Phòng/tháng:</strong> {{ number_format($post->price, 2) }} VND</li>
                                <li><strong>Diện Tích:</strong> {{ $post->area }} m²</li>
                                <li><strong>Địa Chỉ:</strong> {{ $post->address }}, {{ $post->ward }},
                                <li>
                                    <strong>Ngày chuyển vào dự kiến:</strong>
                                    {{ \Carbon\Carbon::parse($post->move_in_date)->format('d/m/Y') }}
                                </li>
                                <li><strong>Ngày đăng:</strong>
                                    {{ $post->published_at ? $post->published_at->format('d/m/Y') : 'Chưa xác định' }}
                                </li>
                                <li><strong>Hết hạn:</strong>
                                    {{ $post->expired_at ? $post->expired_at->format('d/m/Y') : 'Không giới hạn' }}
                            </ul>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Mô Tả</h4>
                        </div>
                        <div class="card-body p-4">
<div class="description-content lh-lg text-black">
                                {!! $post->description !!}
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Tiện Nghi</h4>
                        </div>
                        <div class="card-body">
                            <ul class="amenities-list list-unstyled row row-cols-2 row-cols-md-3 g-3">
                                @foreach ($post->features as $feature)
                                    <li><i class="fas fa-check-circle text-success me-2"></i>{{ $feature->name }}</li>
                                @endforeach
                                @foreach (json_decode($post->amenities, true) ?? [] as $amenity)
                                    <li><i class="fas fa-check-circle text-success me-2"></i>{{ $amenity }}</li>
                                @endforeach
                                @foreach (json_decode($post->furnitures, true) ?? [] as $furniture)
                                    <li><i class="fas fa-check-circle text-success me-2"></i>{{ $furniture }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Location Map -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3 map-card">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Vị Trí</h4>
                        </div>
                        <div class="card-body">
                            <div class="map-container">
                                <div id="map"></div>
                            </div>
                            <button id="getDirections" class="btn btn-primary mt-2">Hiển thị đường đi từ vị trí của bạn</button>
                            <button id="toggleSatellite" class="btn btn-secondary mt-2">Chuyển sang chế độ vệ tinh</button>
                           <div id="routeInfo" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                <h5>Thông tin tuyến đường <button id="closeRouteInfo" class="btn btn-sm btn-danger float-end">X</button></h5>
                                <p><strong>Khoảng cách:</strong> <span id="distance"></span></p>
                                <p><strong>Thời gian:</strong> <span id="duration"></span></p>
                                <ul id="instructions" class="list-unstyled"></ul>
                            </div>
                            <select id="vehicleType" class="form-select mt-2" style="width: 200px;">
                                <option value="motorcycle">Xe máy</option>
                                <option value="car">Ô tô</option>
                                <option value="foot">Đi bộ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <!-- Booking Widget -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-primary text-white border-0 rounded-top-3">
                            <h4 class="mb-0">{{ number_format($post->price, 2) }} VND/tháng</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('bookings') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                                <input type="hidden" name="room_id" value="{{ $post->room_id }}">
                                <div class="row g-3">
                                    <!-- Check In Date -->
                                    <div class="col-12">
                            <label for="checkIn" class="form-label">Chọn ngày đặt lịch</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="calendarTrigger" style="cursor: pointer;">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="text" id="checkIn" name="check_in" class="form-control"
                                                value="{{ \Carbon\Carbon::today()->format('d/m/Y') }}"
                                                placeholder="Chọn ngày nhận phòng" required>
                                        </div>
                                    </div>

                                    <!-- Ghi chú -->
                                    <div class="col-12">
                                        <label for="checkInNote" class="form-label">Chú thích</label>
                                        <input type="text" id="checkInNote" name="note" class="form-control"
                                            placeholder="Nhập ghi chú cho ngày nhận phòng (nếu có)">
                                    </div>

                                    @guest
                                        <div class="col-12">
                                            <label for="guestName" class="form-label">Tên của bạn</label>
                                            <input type="text" id="guestName" name="guest_name" class="form-control"
                                                required>
                                        </div>

                                        <div class="col-12">
                                            <label for="guestPhone" class="form-label">Số điện thoại</label>
                                            <input type="tel" id="guestPhone" name="phone" class="form-control"
                                                pattern="^(0\d{9}|\+84\d{9})$"
                                                title="Số điện thoại phải bắt đầu bằng 0 hoặc +84 và có 10 chữ số."
                                                placeholder="Nhập số điện thoại của bạn" required>

                                        </div>
                                        <div class="col-12">
                                            <label for="guestEmail" class="form-label">Email</label>
                                            <input type="email" id="guestEmail" name="email" class="form-control"
                                                placeholder="Nhập email của bạn" required>
                                        </div>


                                    @endguest

                                    <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                            <span>Tổng số tiền thanh toán</span>
                                            <h4 class="text-primary mb-0">{{ number_format($post->price, 2) }} VND</h4>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary rounded-pill w-100">Đặt lịch
                                            ngay</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Similar Properties -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Bài Viết Liên Quan</h4>
                    </div>
                    <div class="card-body">
                        <div class="property-list">
                            @foreach ($post->category->posts()->where('post_id', '!=', $post->post_id)->where('status', 1)->where('is_public', 1)->take(4)->get() as $similar)
                                <div class="property-item d-flex mb-3">
                                    <a href="{{ route('posts.show', $similar->slug) }}">
                                        <img src="{{ asset('storage/' . $similar->thumbnail) }}"
                                            class="img-fluid rounded me-3"
                                            style="width: 100px; max-height: 100px; object-fit: cover;"
                                            alt="{{ $similar->title }}" loading="lazy">
                                    </a>
                                    <div>
                                        <h5>
                                            <a href="{{ route('posts.show', $similar->slug) }}"
                                                class="text-decoration-none">
                                                {{ $similar->title }}
                                            </a>
                                        </h5>
                                        <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $similar->city }}</p>
                                        <span class="badge bg-primary">For Rent</span>
                                        <h6 class="mt-1">${{ number_format($similar->price, 2) }}</h6>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<!-- JavaScript Libraries -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Flatpickr Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('checkIn');
        const calendarIcon = document.getElementById('calendarTrigger');

        const picker = flatpickr(checkInInput, {
            enableTime: true, // Cho phép chọn giờ
            time_24hr: true, // Hiển thị giờ 24h
            dateFormat: "d/m/Y H:i", // Ngày + Giờ
            minDate: "today",
            defaultDate: new Date()

        });

        calendarIcon.addEventListener('click', function() {
            picker.open();
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('guestPhone');
        if (phoneInput) {
            const form = phoneInput.closest('form');
            form.addEventListener('submit', function(e) {
                // Regex kiểm tra số điện thoại VN: bắt đầu bằng 0 hoặc +84, sau đó là 9 số
                const phonePattern = /^(0[1-9][0-9]{8}|\+84[1-9][0-9]{8})$/;
                if (!phonePattern.test(phoneInput.value.trim())) {
                    e.preventDefault();
                    alert(
                        'Số điện thoại không hợp lệ! Hãy nhập theo định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx'
                    );
                    phoneInput.focus();
                }
            });
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('guestPhone');
        const emailInput = document.getElementById('guestEmail');
        if (phoneInput || emailInput) {
            const form = (phoneInput || emailInput).closest('form');
            form.addEventListener('submit', function(e) {
                // Regex kiểm tra số điện thoại VN
                const phonePattern = /^(0[1-9][0-9]{8}|\+84[1-9][0-9]{8})$/;

                // Regex kiểm tra email hợp lệ
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (phoneInput && !phonePattern.test(phoneInput.value.trim())) {
                    e.preventDefault();
                    alert(
                        'Số điện thoại không hợp lệ! Hãy nhập theo định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx');
                    phoneInput.focus();
                    return;
                }

                if (emailInput && !emailPattern.test(emailInput.value.trim())) {
                    e.preventDefault();
                    alert('Email không hợp lệ! Hãy nhập theo định dạng: example@gmail.com');
                    emailInput.focus();
                    return;
                }
            });
        }
    });
</script>

<!-- VietMap Script -->
<script src="https://cdn.jsdelivr.net/npm/@mapbox/polyline@1.2.0/src/polyline.min.js"></script>

<script>
    let vietMapInstance = null;
    let userMarker = null;
    let routeLayer = null;
    let isSatellite = false;

    const defaultLayer = L.tileLayer(
        `https://maps.vietmap.vn/api/tm/{z}/{x}/{y}.png?apikey={{ config('services.viet_map.key') }}`, {
            maxZoom: 22,
            tileSize: 256,
            zoomOffset: 0,
            attribution: '&copy; <a href="https://www.vietmap.vn/">VietMap</a>'
        }
    );

    const satelliteLayer = L.tileLayer(
        `https://maps.vietmap.vn/api/satellite/{z}/{x}/{y}.png?apikey={{ config('services.viet_map.key') }}`, {
            maxZoom: 22,
            tileSize: 256,
            zoomOffset: 0,
            attribution: '&copy; <a href="https://www.vietmap.vn/">VietMap</a>'
        }
    );

    function initializeVietMap() {
        console.log('🗺️ Bắt đầu khởi tạo VietMap...');

        if (typeof L === 'undefined') {
            console.error('❌ Leaflet chưa được load');
            return false;
        }

        const mapElement = document.getElementById('map');
        if (!mapElement) {
            console.error('❌ Không tìm thấy phần tử #map');
            return false;
        }

        // Xóa bản đồ cũ nếu có
        if (vietMapInstance) {
            try {
                vietMapInstance.remove();
                console.log('🗑️ Đã xóa bản đồ cũ');
            } catch (e) {
                console.warn('⚠️ Lỗi khi xóa bản đồ cũ:', e);
            }
            vietMapInstance = null;
        }

        mapElement.innerHTML = '';
        mapElement.style.cssText = `
            width: 100% !important;
            height: 500px !important;
            display: block !important;
            position: relative !important;
            background: #f8f9fa;
            border-radius: 8px;
        `;

        const lat = {{ $post->latitude ?? 21.0278 }};
        const lng = {{ $post->longitude ?? 105.8342 }};

        try {
            vietMapInstance = L.map('map').setView([lat, lng], 13);
            defaultLayer.addTo(vietMapInstance);
            L.control.zoom({ position: 'topright' }).addTo(vietMapInstance);
            L.control.scale().addTo(vietMapInstance);

            const destinationMarker = L.marker([lat, lng]).addTo(vietMapInstance);
            destinationMarker.bindPopup(`
                <div style="min-width: 200px;">
                    <strong style="color: #007bff;">{{ $post->title }}</strong><br>
                    <small style="color: #6c757d;">
                        📍 {{ $post->address }}<br>
                        {{ $post->ward }}, {{ $post->district }}<br>
                        {{ $post->city }}
                    </small>
                </div>
            `).openPopup();

            setTimeout(() => vietMapInstance.invalidateSize(), 500);
            window.addEventListener('resize', () => vietMapInstance.invalidateSize());

            console.log('🎉 VietMap khởi tạo thành công!');
            return true;

        } catch (error) {
            console.error('💥 Lỗi khởi tạo VietMap:', error);
            mapElement.innerHTML = `
                <div style="height: 500px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; color: #6c757d; border-radius: 8px; text-align: center; padding: 20px;">
                    <div>
                        <i class="fas fa-map-marker-alt" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                        <p style="margin: 0; font-size: 0.9rem;">Không thể tải bản đồ</p>
                        <small style="opacity: 0.7;">Vui lòng thử lại sau</small>
                    </div>
                </div>
            `;
            return false;
        }
    }

  // Thay thế hàm getUserLocationAndDrawRoute() trong file Blade của bạn bằng version cải tiến này:

function getUserLocationAndDrawRoute() {
    console.log('🔍 Bắt đầu lấy vị trí người dùng với độ chính xác cao...');
    
    // Kiểm tra hỗ trợ geolocation
    if (!navigator.geolocation) {
        showLocationError(`
            <div class="alert alert-danger">
                <strong>❌ Trình duyệt không hỗ trợ</strong><br>
                Trình duyệt của bạn không hỗ trợ Geolocation.<br>
                Vui lòng sử dụng Chrome, Firefox, Safari hoặc Edge phiên bản mới.
            </div>
        `);
        return;
    }

    // Hiển thị loading với thông tin chi tiết
    const getDirectionsBtn = document.getElementById('getDirections');
    const originalText = getDirectionsBtn.innerHTML;
    
    // Animation loading đẹp hơn
    getDirectionsBtn.innerHTML = `
        <i class="fas fa-spinner fa-spin me-2"></i>
        <span id="loadingText">Đang khởi tạo GPS...</span>
    `;
    getDirectionsBtn.disabled = true;

    // Cấu hình lấy vị trí với độ chính xác cao nhất
    const options = {
        enableHighAccuracy: true,    // Bắt buộc sử dụng GPS
        timeout: 20000,              // Tăng timeout lên 20 giây
        maximumAge: 0                // Không dùng cache, luôn lấy vị trí mới
    };

    // Thêm loading animation với các bước
    const loadingSteps = [
        'Đang khởi tạo GPS...',
        'Đang tìm kiếm vệ tinh...',
        'Đang tính toán vị trí...',
        'Đang tối ưu độ chính xác...'
    ];
    
    let currentStep = 0;
    const loadingInterval = setInterval(() => {
        const loadingTextEl = document.getElementById('loadingText');
        if (loadingTextEl && currentStep < loadingSteps.length - 1) {
            currentStep++;
            loadingTextEl.textContent = loadingSteps[currentStep];
        }
    }, 2000);

    // Lấy vị trí với watchPosition để có độ chính xác cao hơn
    let watchId;
    let bestAccuracy = Infinity;
    let bestPosition = null;
    let attempts = 0;
    const maxAttempts = 3;

    function startWatching() {
        watchId = navigator.geolocation.watchPosition(
            function(position) {
                attempts++;
                const accuracy = position.coords.accuracy;
                
                console.log(`📍 Lần đo ${attempts}: Vị trí ${position.coords.latitude}, ${position.coords.longitude} (±${accuracy}m)`);
                
                // Lưu vị trí tốt nhất
                if (accuracy < bestAccuracy) {
                    bestAccuracy = accuracy;
                    bestPosition = position;
                }
                
                // Nếu đạt độ chính xác tốt (≤ 20m) hoặc đã thử đủ số lần
                if (accuracy <= 20 || attempts >= maxAttempts) {
                    clearInterval(loadingInterval);
                    navigator.geolocation.clearWatch(watchId);
                    
                    // Reset button
                    getDirectionsBtn.innerHTML = originalText;
                    getDirectionsBtn.disabled = false;
                    
                    onLocationSuccess(bestPosition || position);
                }
            },
            function(error) {
                clearInterval(loadingInterval);
                navigator.geolocation.clearWatch && navigator.geolocation.clearWatch(watchId);
                
                // Reset button
                getDirectionsBtn.innerHTML = originalText;
                getDirectionsBtn.disabled = false;
                
                onLocationError(error);
            },
            options
        );
    }

    // Bắt đầu theo dõi vị trí
    startWatching();
    
    // Timeout backup
    setTimeout(() => {
        if (bestPosition) {
            clearInterval(loadingInterval);
            navigator.geolocation.clearWatch && navigator.geolocation.clearWatch(watchId);
            getDirectionsBtn.innerHTML = originalText;
            getDirectionsBtn.disabled = false;
            onLocationSuccess(bestPosition);
        }
    }, options.timeout);
}

function onLocationSuccess(position) {
    const userLat = position.coords.latitude;
    const userLng = position.coords.longitude;
    const accuracy = position.coords.accuracy;
    const timestamp = new Date(position.timestamp);
    
    console.log(`✅ Vị trí tốt nhất: ${userLat}, ${userLng} (±${accuracy}m)`);
    
    // Hiển thị thông tin vị trí chi tiết
    showLocationInfo(position);
    
    // Vẽ route
    drawRoute(userLat, userLng);
}

function onLocationError(error) {
    console.error('❌ Lỗi khi lấy vị trí:', error);
    
    let title = '';
    let message = '';
    let solutions = '';

    switch (error.code) {
        case error.PERMISSION_DENIED:
            title = '🔒 Quyền truy cập vị trí bị từ chối';
            message = 'Bạn đã từ chối quyền truy cập vị trí.';
            solutions = `
                <div class="mt-3">
                    <strong>💡 Cách khắc phục:</strong>
                    <ol class="mt-2">
                        <li>Nhấp vào biểu tượng <strong>🔒</strong> hoặc <strong>ℹ️</strong> bên trái thanh địa chỉ</li>
                        <li>Chọn <strong>"Cho phép"</strong> cho Location/Vị trí</li>
                        <li>Refresh trang và thử lại</li>
                        <li>Hoặc vào <strong>Settings → Privacy → Location</strong> để bật quyền</li>
                    </ol>
                    <div class="alert alert-info mt-2">
                        <small><strong>Lưu ý:</strong> Trên mobile, hãy đảm bảo Location Services được bật trong Settings hệ thống.</small>
                    </div>
                </div>
            `;
            break;
            
        case error.POSITION_UNAVAILABLE:
            title = '📡 Không thể xác định vị trí';
            message = 'Hệ thống định vị không khả dụng.';
            solutions = `
                <div class="mt-3">
                    <strong>🔧 Hãy kiểm tra:</strong>
                    <ul class="mt-2">
                        <li><strong>GPS</strong> đã được bật trên thiết bị</li>
                        <li><strong>Kết nối internet</strong> ổn định</li>
                        <li><strong>Location Services</strong> được bật trong cài đặt</li>
                        <li>Không ở trong <strong>tòa nhà che chắn</strong> tín hiệu</li>
                        <li>Thử <strong>ra ngoài trời</strong> để có tín hiệu GPS tốt hơn</li>
                    </ul>
                </div>
            `;
            break;
            
        case error.TIMEOUT:
            title = '⏱️ Hết thời gian chờ';
            message = 'Việc lấy vị trí mất quá nhiều thời gian.';
            solutions = `
                <div class="mt-3">
                    <strong>🚀 Thử các cách sau:</strong>
                    <ul class="mt-2">
                        <li><strong>Ra ngoài trời</strong> để GPS hoạt động tốt hơn</li>
                        <li><strong>Khởi động lại</strong> ứng dụng GPS/Maps trên điện thoại</li>
                        <li><strong>Đợi vài phút</strong> rồi thử lại</li>
                        <li><strong>Kiểm tra kết nối mạng</strong> và thử lại</li>
                        <li>Đảm bảo <strong>không có ứng dụng nào khác</strong> đang sử dụng GPS</li>
                    </ul>
                </div>
            `;
            break;
            
        default:
            title = '❌ Lỗi không xác định';
            message = 'Đã xảy ra lỗi khi lấy vị trí.';
            solutions = `
                <div class="mt-3">
                    <strong>🔄 Vui lòng:</strong>
                    <ul class="mt-2">
                        <li>Refresh trang và thử lại</li>
                        <li>Kiểm tra cài đặt trình duyệt</li>
                        <li>Thử trên trình duyệt khác</li>
                        <li>Liên hệ hỗ trợ nếu vấn đề vẫn tiếp diễn</li>
                    </ul>
                </div>
            `;
    }

    const errorHtml = `
        <div class="alert alert-warning alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <h5 class="alert-heading">${title}</h5>
            <p class="mb-2">${message}</p>
            ${solutions}
            <hr>
            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-primary btn-sm" onclick="getUserLocationAndDrawRoute()">
                    <i class="fas fa-redo me-1"></i> Thử lại
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    // Xóa alert cũ nếu có
    const oldAlert = document.querySelector('.alert[style*="position: fixed"]');
    if (oldAlert) oldAlert.remove();
    
    document.body.insertAdjacentHTML('beforeend', errorHtml);
    
    // Tự động ẩn sau 15 giây
    setTimeout(() => {
        const alert = document.querySelector('.alert[style*="position: fixed"]');
        if (alert) alert.remove();
    }, 15000);
}

function showLocationInfo(position) {
    const { latitude, longitude, accuracy, altitude, speed, heading } = position.coords;
    const timestamp = new Date(position.timestamp);
    
    // Xác định mức độ chính xác
    let accuracyLevel, accuracyColor, accuracyIcon;
    if (accuracy <= 10) {
        accuracyLevel = 'Rất chính xác';
        accuracyColor = 'success';
        accuracyIcon = 'fa-bullseye';
    } else if (accuracy <= 30) {
        accuracyLevel = 'Chính xác';
        accuracyColor = 'primary';
        accuracyIcon = 'fa-crosshairs';
    } else if (accuracy <= 100) {
        accuracyLevel = 'Khá chính xác';
        accuracyColor = 'warning';
        accuracyIcon = 'fa-dot-circle';
    } else {
        accuracyLevel = 'Ít chính xác';
        accuracyColor = 'danger';
        accuracyIcon = 'fa-circle';
    }

    const infoHtml = `
        <div class="alert alert-info mt-3" style="background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%); border-left: 4px solid #2196F3;">
            <h5 class="alert-heading">
                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                Thông tin vị trí của bạn
            </h5>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <strong><i class="fas fa-globe me-1"></i> Tọa độ:</strong><br>
                    <code style="background: #fff; padding: 2px 6px; border-radius: 4px;">
                        ${latitude.toFixed(6)}, ${longitude.toFixed(6)}
                    </code>
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('${latitude}, ${longitude}')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
                <div class="col-md-6">
                    <strong><i class="fas ${accuracyIcon} me-1"></i> Độ chính xác:</strong><br>
                    <span class="badge bg-${accuracyColor}">${accuracyLevel} (±${Math.round(accuracy)}m)</span>
                </div>
            </div>
            
            ${altitude !== null ? `
                <div class="mt-2">
                    <strong><i class="fas fa-mountain me-1"></i> Độ cao:</strong>
                    <span class="badge bg-info">${Math.round(altitude)}m</span>
                </div>
            ` : ''}
            
            <div class="mt-2">
                <strong><i class="fas fa-clock me-1"></i> Thời gian:</strong>
                <small>${timestamp.toLocaleString('vi-VN')}</small>
            </div>
            
            <div class="mt-3">
                <button class="btn btn-primary btn-sm me-2" onclick="window.open('https://maps.google.com/?q=${latitude},${longitude}', '_blank')">
                    <i class="fab fa-google me-1"></i> Mở Google Maps
                </button>
                <button class="btn btn-success btn-sm" onclick="copyCoordinates(${latitude}, ${longitude})">
                    <i class="fas fa-share me-1"></i> Chia sẻ vị trí
                </button>
            </div>
        </div>
    `;
    
    // Thêm thông tin vào container map
    const mapContainer = document.querySelector('.map-container');
    let infoContainer = document.getElementById('locationInfoContainer');
    if (!infoContainer) {
        infoContainer = document.createElement('div');
        infoContainer.id = 'locationInfoContainer';
        mapContainer.parentNode.insertBefore(infoContainer, mapContainer.nextSibling);
    }
    infoContainer.innerHTML = infoHtml;
}

// Hàm tiện ích
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Hiển thị thông báo thành công
        const toast = document.createElement('div');
        toast.className = 'toast show position-fixed';
        toast.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;';
        toast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="fas fa-check me-2"></i>Đã copy: ${text}
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.remove(), 3000);
    }).catch(err => {
        console.error('Không thể copy:', err);
        alert('Không thể copy tự động. Vui lòng copy thủ công: ' + text);
    });
}

function copyCoordinates(lat, lng) {
    const text = `Vị trí của tôi: ${lat.toFixed(6)}, ${lng.toFixed(6)}\nGoogle Maps: https://maps.google.com/?q=${lat},${lng}`;
    copyToClipboard(text);
}
    function drawRoute(userLat, userLng) {
        console.log('🗺️ Bắt đầu vẽ tuyến đường...');
        
        // Xóa marker và route cũ
        if (userMarker) vietMapInstance.removeLayer(userMarker);
        if (routeLayer) vietMapInstance.removeLayer(routeLayer);

        // Thêm marker vị trí người dùng
        userMarker = L.marker([userLat, userLng], {
            icon: L.divIcon({
                className: 'user-location-icon',
                html: '<div style="background-color: #ff4500; border-radius: 50%; width: 12px; height: 12px; border: 2px solid #fff; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            })
        }).addTo(vietMapInstance);

        userMarker.bindPopup('📍 Vị trí của bạn').openPopup();

        const destinationLat = {{ $post->latitude ?? 21.0278 }};
        const destinationLng = {{ $post->longitude ?? 105.8342 }};
        const apiKey = "{{ config('services.viet_map.key') }}";
        const vehicleSelect = document.getElementById('vehicleType');
        const vehicle = vehicleSelect ? vehicleSelect.value : 'motorcycle';

        const url = `https://maps.vietmap.vn/api/route?api-version=1.1&apikey=${apiKey}&point=${userLat},${userLng}&point=${destinationLat},${destinationLng}&vehicle=${vehicle}&points_encoded=true`;
        
        console.log(`🔗 Gọi API VietMap: ${url}`);
        
        // Hiển thị loading trong route info
        const routeInfoDiv = document.getElementById('routeInfo');
        routeInfoDiv.innerHTML = `
            <div class="text-center">
                <i class="fas fa-spinner fa-spin me-2"></i>
                <span>Đang tính toán tuyến đường...</span>
            </div>
        `;
        routeInfoDiv.style.display = 'block';

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 Dữ liệu từ VietMap API:', data);
                
                if (data.code === 'OK' && data.paths && data.paths.length > 0) {
                    const path = data.paths[0];
                    const distance = (path.distance / 1000).toFixed(2);
                    const duration = (path.time / 60000).toFixed(1);
                    const instructions = path.instructions || [];

                    // Hiển thị thông tin tuyến đường
                    routeInfoDiv.innerHTML = `
                        <h5>
                            Thông tin tuyến đường 
                            <button id="closeRouteInfo" class="btn btn-sm btn-danger float-end">×</button>
                        </h5>
                        <p><strong>Khoảng cách:</strong> <span id="distance">${distance} km</span></p>
                        <p><strong>Thời gian:</strong> <span id="duration">${duration} phút</span></p>
                        <div style="max-height: 200px; overflow-y: auto;">
                            <strong>Chỉ dẫn:</strong>
                            <ul id="instructions" class="list-unstyled mt-2">
                                ${instructions.map(inst => `<li class="mb-1">🔹 ${inst.text || 'Không có chỉ dẫn'} <small class="text-muted">(~${Math.round(inst.distance || 0)}m)</small></li>`).join('')}
                            </ul>
                        </div>
                    `;

                    // Vẽ tuyến đường nếu có dữ liệu points
                    if (path.points) {
                        try {
                            // Kiểm tra xem polyline đã được load chưa
                            if (typeof polyline === 'undefined') {
                                throw new Error('Polyline library chưa được load');
                            }
                            
                            const decodedPoints = polyline.decode(path.points).map(coord => [coord[0], coord[1]]);
                            
                            routeLayer = L.polyline(decodedPoints, {
                                color: '#007bff',
                                weight: 5,
                                opacity: 0.8,
                                smoothFactor: 1.0
                            }).addTo(vietMapInstance);

                            // Fit map để hiển thị toàn bộ tuyến đường
                            const bounds = L.latLngBounds([[userLat, userLng], [destinationLat, destinationLng]]);
                            vietMapInstance.fitBounds(bounds, { padding: [50, 50] });
                            
                            console.log('✅ Vẽ tuyến đường thành công!');
                            
                        } catch (decodeError) {
                            console.error('❌ Lỗi khi decode polyline:', decodeError);
                            routeInfoDiv.innerHTML += `<div class="alert alert-warning mt-2">Không thể vẽ tuyến đường trên bản đồ, nhưng thông tin đã được hiển thị ở trên.</div>`;
                        }
                    } else {
                        console.warn('⚠️ Không có dữ liệu points để vẽ tuyến đường');
                        routeInfoDiv.innerHTML += `<div class="alert alert-info mt-2">Thông tin tuyến đường đã được tính toán nhưng không thể hiển thị trên bản đồ.</div>`;
                    }

                } else {
                    console.error('❌ Lỗi từ API VietMap:', data);
                    routeInfoDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>❌ Không thể tính toán tuyến đường</strong><br>
                            ${data.messages ? data.messages.join('<br>') : 'Lỗi không xác định từ VietMap API'}
                        </div>
                    `;
                }
                
                // Thêm event listener cho nút đóng
                const closeBtn = document.getElementById('closeRouteInfo');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        routeInfoDiv.style.display = 'none';
                    });
                }
                
            })
            .catch(error => {
                console.error('💥 Lỗi khi gọi API:', error);
                routeInfoDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Lỗi kết nối</strong><br>
                        Không thể kết nối đến VietMap API. Vui lòng kiểm tra:
                        <ul class="mt-2 mb-0">
                            <li>Kết nối internet</li>
                            <li>API key VietMap hợp lệ</li>
                            <li>Firewall/AdBlocker không chặn</li>
                        </ul>
                        <button class="btn btn-sm btn-primary mt-2" onclick="location.reload()">Thử lại</button>
                    </div>
                `;
            });
    }

    // Khởi tạo khi DOM đã sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 DOM Content Loaded - Khởi tạo VietMap...');
        
        // Đợi một chút để đảm bảo tất cả resources đã load
        setTimeout(() => {
            if (initializeVietMap()) {
                // Thêm event listeners sau khi map đã khởi tạo thành công
                const getDirectionsBtn = document.getElementById('getDirections');
                const toggleSatelliteBtn = document.getElementById('toggleSatellite');
                
                if (getDirectionsBtn) {
                    getDirectionsBtn.addEventListener('click', getUserLocationAndDrawRoute);
                } else {
                    console.warn('⚠️ Không tìm thấy nút getDirections');
                }
                
                if (toggleSatelliteBtn) {
                    toggleSatelliteBtn.addEventListener('click', () => {
                        if (isSatellite) {
                            vietMapInstance.removeLayer(satelliteLayer);
                            vietMapInstance.addLayer(defaultLayer);
                            toggleSatelliteBtn.textContent = 'Chuyển sang chế độ vệ tinh';
                        } else {
                            vietMapInstance.removeLayer(defaultLayer);
                            vietMapInstance.addLayer(satelliteLayer);
                            toggleSatelliteBtn.textContent = 'Chuyển sang chế độ bản đồ';
                        }
                        isSatellite = !isSatellite;
                        vietMapInstance.invalidateSize();
                    });
                } else {
                    console.warn('⚠️ Không tìm thấy nút toggleSatellite');
                }
            }
        }, 1000);
    });

    console.log('📋 VietMap Script đã được load');
</script>

{{-- <script src="https://unpkg.com/@turf/polyline@6.x.x/dist/polyline.min.js"></script> --}}

