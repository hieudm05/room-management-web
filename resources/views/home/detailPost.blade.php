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
            /* Độ rộng cố định */
            height: 200px;
            /* Chiều cao tối đa */
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
            /* Lệch xuống */
            left: 20px;
            /* Lệch phải */
            z-index: 2;
        }

        .gallery-side img:nth-child(3) {
            top: 40px;
            left: 40px;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .gallery-main img {
                max-height: 250px;
            }

            .gallery-side {
                display: none;
                /* Ẩn ở mobile */
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
                                class="img-fluid rounded w-100 object-fit-cover main-image" loading="lazy">
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
                                <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image"
                                    class="img-fluid rounded side-image" loading="lazy">
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
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">About {{ $post->title }}</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><strong>Category:</strong> {{ $post->category->name }}</li>
                                <li><strong>Price:</strong> ${{ number_format($post->price, 2) }}</li>
                                <li><strong>Area:</strong> {{ $post->area }} sqm</li>
                                <li><strong>Address:</strong> {{ $post->address }}, {{ $post->ward }},
                                    {{ $post->district }}, {{ $post->city }}</li>
                                <li>
                                    <strong>Published At:</strong>
                                    @if ($post->published_at)
                                        {{ $post->published_at->format('M d, Y') }}
                                    @else
                                        Chưa xác định
                                    @endif
                                </li>
                                <li>
                                    <strong>Expires At:</strong>
                                    @if ($post->expired_at)
                                        {{ $post->expired_at->format('M d, Y') }}
                                    @else
                                        Không giới hạn
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4 shadow-sm border-0 rounded-3">
                        <div class="card-header bg-light border-0 rounded-top-3">
                            <h4 class="mb-0 fw-semibold text-primary">Description</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="description-content lh-lg text-muted">
                                {!! $post->description !!}
                            </div>
                        </div>
                    </div>


                    <!-- Amenities -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Amenities</h4>
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
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Reviews</h4>
                        </div>
                        <div class="card-body">
                            <p>No reviews available yet. Be the first to share your experience!</p>
                        </div>
                    </div>

                    <!-- Write a Review -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h4 class="mb-0">Write a Review</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="reviewMessage" class="form-label">Your Review</label>
                                <textarea id="reviewMessage" name="review" class="form-control" rows="5" placeholder="Share your thoughts..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill">Submit Review</button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <!-- Booking Widget -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">${{ number_format($post->price, 2) }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="checkIn" class="form-label">Check In</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="text" id="checkIn" name="check_in" class="form-control"
                                            value="{{ \Carbon\Carbon::today()->format('m/d/Y') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Total Payment</span>
                                        <h4 class="text-primary mb-0">${{ number_format($post->price, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary rounded-pill w-100">Book Now</button>
                                </div>
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
