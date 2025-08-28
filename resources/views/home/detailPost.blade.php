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
        <div class="row g-2">
            <!-- Ảnh chính -->
            <div class="col-lg-8 col-md-7">
                <div class="gallery-main position-relative">
                    <a href="{{ asset('storage/' . $post->thumbnail) }}" class="mfp-gallery d-block h-100">
                        <img src="{{ asset('storage/' . $post->thumbnail) }}" 
                             alt="{{ $post->title }}"
                             class="img-fluid rounded w-100 h-100"
                             style="object-fit: cover; max-height: 500px;"
                             loading="lazy">
                    </a>
                </div>
            </div>

            <!-- Album phụ -->
            <div class="col-lg-4 col-md-5">
                <div class="gallery-side row g-2">
                    @php
                        $galleryImages = $post->gallery ? array_slice(json_decode($post->gallery, true), 0, 3) : [];
                    @endphp

                    @foreach ($galleryImages as $image)
                        <div class="col-12">
                            <a href="{{ asset('storage/' . $image) }}" class="mfp-gallery d-block">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="Gallery Image"
                                     class="img-fluid rounded w-100"
                                     style="object-fit: cover; height: 160px;"
                                     loading="lazy">
                            </a>
                        </div>
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
                                    {{ $post->district }}, {{ $post->city }}</li>
                                <li><strong>Ngày đăng:</strong>
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Chưa xác định' }}
                                </li>
                                <li><strong>Hết hạn:</strong>
                                    {{ $post->expired_at ? $post->expired_at->format('M d, Y') : 'Không giới hạn' }}</li>
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
                            <button id="getDirections" class="btn btn-primary mt-2  btn btn-sm">Hiển thị đường đi từ vị trí của bạn</button>
                            <button id="toggleSatellite" class="btn btn-secondary mt-2  btn btn-sm">Chuyển sang chế độ vệ tinh</button>
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
                        <div class="card-header  border-0 rounded-top-3" style="background-color: #a70a29 !important; ">
                            <h4 class="mb-0 text-white">{{ number_format($post->price, 2) }} VND/tháng</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('bookings') }}" method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                                <input type="hidden" name="room_id" value="{{ $post->room_id }}">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
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
                                        <button type="submit" class="btn btn-primary rounded-pill w-100 " style="background-color: #a70a29 !important;">Đặt lịch
                                            ngay</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Similar Properties -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Bài viết liên quan</h4>
                        </div>
                        <div class="card-body">
    <h5 class="mb-3 fw-bold">Bài viết tương tự</h5>
    <div class="property-list">
        @foreach ($post->category->posts()
            ->where('post_id', '!=', $post->post_id)
            ->where('status', 1)
            ->where('is_public', 1)
            ->take(4)
            ->get() as $similar)
            <div class="card mb-3 border-0 shadow-sm">
                <div class="row g-0 align-items-center">
                    <div class="col-4">
                        <a href="{{ route('posts.show', $similar->slug) }}">
                            <img src="{{ asset('storage/' . $similar->thumbnail) }}"
                                 class="img-fluid rounded-start"
                                 alt="{{ $similar->title }}"
                                 loading="lazy"
                                 style="height: 100px; object-fit: cover; width: 100%;">
                        </a>
                    </div>
                    <div class="col-8">
                        <div class="card-body py-2">
                            <h6 class="card-title mb-1">
                                <a href="{{ route('posts.show', $similar->slug) }}" 
                                   class="text-decoration-none text-dark small fw-semibold">
                                    {{ $similar->title }}
                                </a>
                            </h6>
                            <p class="card-text mb-1 text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $similar->city }}
                            </p>
                            <span class="badge bg-primary">For Rent</span>
                            <p class="card-text fw-bold text-success mt-1 mb-0">
                                ${{ number_format($similar->price, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
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

    function getUserLocationAndDrawRoute() {
        console.log('🔍 Bắt đầu lấy vị trí người dùng...');
        
        // Kiểm tra xem trình duyệt có hỗ trợ geolocation không
        if (!navigator.geolocation) {
            showLocationError('Trình duyệt của bạn không hỗ trợ Geolocation. Vui lòng sử dụng trình duyệt khác.');
            return;
        }

        // Hiển thị loading
        const getDirectionsBtn = document.getElementById('getDirections');
        const originalText = getDirectionsBtn.innerHTML;
        getDirectionsBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lấy vị trí...';
        getDirectionsBtn.disabled = true;

        const options = {
            enableHighAccuracy: true,    // Yêu cầu độ chính xác cao
            timeout: 15000,              // Timeout 15 giây
            maximumAge: 300000           // Cache trong 5 phút
        };

        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                
                console.log(`📍 Vị trí hiện tại: ${userLat}, ${userLng} (độ chính xác: ${accuracy}m)`);
                
                // Reset button
                getDirectionsBtn.innerHTML = originalText;
                getDirectionsBtn.disabled = false;
                
                drawRoute(userLat, userLng);
            },
            // Error callback
            function(error) {
                console.error('❌ Lỗi khi lấy vị trí:', error);
                
                // Reset button
                getDirectionsBtn.innerHTML = originalText;
                getDirectionsBtn.disabled = false;
                
                let message = '';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        message = `
                            <div style="text-align: left;">
                                <strong>⚠️ Quyền truy cập vị trí bị từ chối</strong><br><br>
                                <strong>Để sử dụng tính năng này, vui lòng:</strong><br>
                                1. Nhấp vào biểu tượng 🔒 trên thanh địa chỉ<br>
                                2. Chọn "Cho phép" cho Location/Vị trí<br>
                                3. Refresh trang và thử lại<br><br>
                                <small><em>Hoặc vào Settings → Privacy → Location để bật quyền truy cập vị trí.</em></small>
                            </div>
                        `;
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = `
                            <strong>📡 Không thể xác định vị trí</strong><br>
                            Vui lòng kiểm tra:<br>
                            • GPS đã được bật<br>
                            • Kết nối internet ổn định<br>
                            • Không ở trong tòa nhà che chắn tín hiệu
                        `;
                        break;
                    case error.TIMEOUT:
                        message = `
                            <strong>⏱️ Hết thời gian chờ</strong><br>
                            Việc lấy vị trí mất quá nhiều thời gian.<br>
                            Vui lòng thử lại hoặc kiểm tra kết nối mạng.
                        `;
                        break;
                    default:
                        message = `
                            <strong>❌ Lỗi không xác định</strong><br>
                            Không thể lấy vị trí hiện tại.<br>
                            Vui lòng thử lại sau.
                        `;
                }
                // showLocationError(message);
            },
            options
        );
    }

    function showLocationError(message) {
        // Hiển thị modal hoặc alert với thông tin chi tiết
        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);
        
        // Tự động ẩn sau 10 giây
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 10000);
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
