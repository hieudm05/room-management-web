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
                            <div class="description-content lh-lg text-muted">
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
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Tiền phòng/tháng</span>
                                        <h4 class="text-primary mb-0">{{ number_format($post->price, 2) }} VND</h4>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary rounded-pill w-100">Đặt Ngay</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Contact -->
                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-body text-center">
                            <a href="#" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#agentMessage">
                                <i class="fas fa-comment-alt me-2"></i> Liên Hệ Người Đăng
                            </a>
                        </div>
                    </div>

                    <!-- Similar Properties -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Bài Viết Liên Quan</h4>
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
                                                    {{ Str::limit($similar->title, 50) }}
                                                </a>
                                            </h5>
                                            <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $similar->city }}</p>
                                            <span class="badge bg-primary">For Rent</span>
                                            <h6 class="mt-1">{{ number_format($similar->price, 2) }} VND</h6>
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
