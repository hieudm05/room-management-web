@extends('home.layouts.app')

@section('title', $post->title)

@section('styles')
    <style>
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

        #map {
            height: 400px;
            border-radius: 8px;
            margin-top: 20px;
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

        @media (max-width: 768px) {
            .gallery-main img {
                max-height: 250px;
            }

            .gallery-side {
                display: none;
            }

            #map {
                height: 300px;
            }
        }
    </style>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

                    <!-- Reviews -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Đánh Giá</h4>
                        </div>
                        <div class="card-body">
                            <p>Chưa có đánh giá. Hãy chia sẻ trải nghiệm của bạn!</p>
                        </div>
                    </div>

                    <!-- Write a Review -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Viết Đánh Giá</h4>
                        </div>
                        <div class="card-body">
                            <form action="#" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="reviewMessage" class="form-label">Đánh giá của bạn</label>
                                    <textarea id="reviewMessage" name="review" class="form-control" rows="5" placeholder="Viết đánh giá..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill w-100">Gửi Đánh Giá</button>
                            </form>
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

                                    <!-- THÊM PHẦN NÀY nếu chưa đăng nhập -->
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

                                    <!-- Tổng thanh toán -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Tổng số tiền thanh toán</span>
                                            <h4 class="text-primary mb-0">${{ number_format($post->price, 2) }}</h4>
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

                <!-- Agent Contact -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body text-center">
                        <a href="#" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal"
                            data-bs-target="#agentMessage">
                            <i class="fas fa-comment-alt me-2"></i> Contact Agent
                        </a>
                    </div>
                </div>

                <!-- Similar Properties -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">Similar Properties</h4>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
