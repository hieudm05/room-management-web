@extends('home.layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">

        <!-- Verified Properties Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Verified Properties</h2>
                    <p>Explore our selection of verified properties, ensuring quality and trust.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            @foreach ($rooms->where('property.verified', true) as $room)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="property-listing property-2 h-100">

                        <div class="listing-img-wrapper">
                            <div class="_exlio_125">{{ $room->status == 1 ? 'For Rent' : 'For Sale' }}</div>
                            <div class="list-img-slide">
                                <div class="click">
                                    @if ($room->photos->first())
                                        <img src="{{ $room->photos->first()->image_url }}" width="50">
                                    @else
                                        <span class="text-muted">Chưa có ảnh</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="listing-detail-wrapper">
                            <div class="listing-short-detail-wrap">
                                <div class="_card_list_flex mb-2">
                                    <div class="_card_flex_01">
                                        <span class="_list_blickes _netork">Network info</span>
                                        <span
                                            class="_list_blickes types">{{ $room->property->type ?? 'Type Unknown' }}</span>
                                    </div>
                                    @auth
                                        <div class="prt_saveed_12lk">
                                            <form action="{{ route('home.favorites.toggle', $room->property->property_id) }}"
                                                method="POST">
                                                @csrf
                                                @php
                                                    $isFavorited = Auth::user()->favorites->contains(
                                                        'property_id',
                                                        $room->property->property_id,
                                                    );
                                                @endphp
                                                <button type="submit"
                                                    class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
                                                    ❤️ {{ $isFavorited ? 'Bỏ yêu thích' : 'Yêu thích' }}
                                                </button>
                                            </form>
                                        </div>
                                    @endauth
                                </div>
                                <div class="_card_list_flex">
                                    <div class="_card_flex_01">
                                        <h4 class="listing-name verified">
                                            <a href="{{ route('show2', $room) }}" class="prt-link-detail">
                                                {{ $room->property->address ?? 'Address not available' }}
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="price-features-wrapper">
                            <div class="list-fx-features">
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="Beds" />
                                    {{ $room->occupants }} Beds
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15"
                                        alt="Baths" />
                                    1 Bath
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="Area" />
                                    {{ $room->area }} sqft
                                </div>
                            </div>
                        </div>

                        <div class="listing-detail-footer">
                            <div class="footer-first">
                                <h6 class="listing-card-info-price mb-0 p-0">${{ number_format($room->rental_price) }}</h6>
                            </div>
                            <div class="footer-flex">
                                <a href="{{ route('show2', $room) }}" class="prt-view">View Detail</a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
            @if ($rooms->where('property.verified', true)->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">No verified properties available at the moment.</p>
                </div>
            @endif
        </div>

        <!-- Existing Recent Listings Section -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Recent Listed Property</h2>
                    <p>Danh sách các phòng trọ mới được đăng lên hệ thống.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center g-4">

            @foreach ($rooms as $room)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="property-listing property-2 h-100">

                        <div class="listing-img-wrapper">
                            <div class="_exlio_125">{{ $room->status == 1 ? 'For Rent' : 'For Sale' }}</div>
                            <div class="list-img-slide">
                                <div class="click">
                                    @if ($room->photos->first())
                                        <img src="{{ $room->photos->first()->image_url }}" width="50">
                                    @else
                                        <span class="text-muted">Chưa có ảnh</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="listing-detail-wrapper">
                            <div class="listing-short-detail-wrap">
                                <div class="_card_list_flex mb-2">
                                    <div class="_card_flex_01">
                                        <span class="_list_blickes _netork">Network info</span>
                                        <span
                                            class="_list_blickes types">{{ $room->property->type ?? 'Type Unknown' }}</span>
                                    </div>
                                    @auth
                                        <div class="prt_saveed_12lk">
                                            <form action="{{ route('home.favorites.toggle', $room->property->property_id) }}"
                                                method="POST">
                                                @csrf
                                                @php
                                                    $isFavorited = Auth::user()->favorites->contains(
                                                        'property_id',
                                                        $room->property->property_id,
                                                    );
                                                @endphp
                                                <button type="submit"
                                                    class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
                                                    ❤️ {{ $isFavorited ? 'Bỏ yêu thích' : 'Yêu thích' }}
                                                </button>
                                            </form>
                                        </div>
                                    @endauth
                                </div>
                                <div class="_card_list_flex">
                                    <div class="_card_flex_01">
                                        <h4 class="listing-name verified">
                                            <a href="{{ route('show2', $room) }}" class="prt-link-detail">
                                                {{ $room->property->address ?? 'Address not available' }}
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="price-features-wrapper">
                            <div class="list-fx-features">
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="Beds" />
                                    {{ $room->occupants }} Beds
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15"
                                        alt="Baths" />
                                    1 Bath
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="Area" />
                                    {{ $room->area }} sqft
                                </div>
                            </div>
                        </div>

                        <div class="listing-detail-footer">
                            <div class="footer-first">
                                <h6 class="listing-card-info-price mb-0 p-0">${{ number_format($room->rental_price) }}</h6>
                            </div>
                            <div class="footer-flex">
                                <a href="{{ route('show2', $room) }}" class="prt-view">View Detail</a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

        <div class="row mt-5">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                {{ $rooms->links() }}
            </div>
        </div>

        <!-- Approved Posts Section -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Bài Đăng Cho Thuê Đã Duyệt</h2>
                    <p>Danh sách các bài viết cho thuê đã được duyệt và công khai.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse ($posts as $post)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card h-100 shadow-sm rounded-3 border-0">

                        {{-- Thumbnail --}}
                        @if ($post->thumbnail && file_exists(public_path('storage/' . $post->thumbnail)))
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="card-img-top rounded-top-3"
                                alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="d-flex justify-content-center align-items-center bg-light" style="height: 200px;">
                                <span class="text-muted">Chưa có ảnh</span>
                            </div>
                        @endif

                        {{-- Nội dung --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold text-truncate mb-2" style="max-width: 100%;" title="{{ $post->title }}">
                                {{ Str::limit($post->title, 60) }}
                            </h5>

                            <p class="text-danger fw-semibold mb-2">
                                {{ number_format($post->price, 0, ',', '.') }} đ/tháng
                            </p>

                            <ul class="list-unstyled small mb-3">
                                <li><i class="fa fa-expand me-1 text-secondary"></i> {{ $post->area }} m²</li>
                                <li><i class="fa fa-map-marker-alt me-1 text-secondary"></i>
                                    {{ $post->district }}, {{ $post->city }}
                                </li>
                                <li><i class="fa fa-home me-1 text-secondary"></i> {{ $post->address }}</li>
                                <li><i class="fa fa-phone me-1 text-secondary"></i>
                                    {{ $post->property->phone ?? 'Liên hệ chủ trọ' }}
                                </li>
                            </ul>

                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit(strip_tags($post->description), 100) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">
                                    Mã tin: {{ $post->post_code ?? '---' }}
                                </small>
                                <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Hiện chưa có bài đăng cho thuê nào được duyệt.</p>
                </div>
            @endforelse
        </div>
        {{-- Phân trang --}}
        <div class="row mt-5">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                {{ $posts->links() }}
            </div>
        </div>



        <!-- Gợi ý bài viết gần bạn -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Bài viết gần bạn</h2>
                    <p>Chúng tôi đã tìm thấy một số bài viết gần vị trí hiện tại của bạn.</p>
                </div>
            </div>
        </div>
        <div class="row g-4" id="suggested-posts">
            <!-- Render từ JS -->
        </div>



        <div class="row mt-5">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                {{ $posts->links() }}
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        console.log("✅ Script đang chạy!");

        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM fully loaded - Starting geolocation check");

            // Check for HTTPS (excluding localhost for development)
            // if (!window.location.protocol.includes("https") && window.location.hostname !== "localhost") {
            //     console.error("Geolocation requires HTTPS");
            //     alert("Lỗi: Vui lòng truy cập trang qua HTTPS để sử dụng định vị.");
            //     document.getElementById("suggested-posts").innerHTML =
            //         `<div class="col-12 text-center"><p class="text-muted">Yêu cầu HTTPS để tải bài viết gần bạn.</p></div>`;
            //     return;
            // }

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                console.error("Geolocation not supported by this browser");
                alert("Lỗi: Trình duyệt không hỗ trợ định vị.");
                document.getElementById("suggested-posts").innerHTML =
                    `<div class="col-12 text-center"><p class="text-muted">Trình duyệt không hỗ trợ định vị.</p></div>`;
                return;
            }

            console.log("Requesting user location...");

            // Request geolocation
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log(`Geolocation success - Latitude: ${lat}, Longitude: ${lng}`);
                    alert(`Vị trí của bạn là:\nLatitude: ${lat}\nLongitude: ${lng}`);

                    // Optional: Load nearby posts
                    const container = document.getElementById("suggested-posts");
                    container.innerHTML = `
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                    `;

                    fetch("{{ route('posts.suggestNearby') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                lat,
                                lng
                            })
                        })
                        .then(res => {
                            console.log("Fetch response status:", res.status);
                            if (!res.ok) throw new Error(`HTTP error! Status: ${res.status}`);
                            return res.json();
                        })
                        .then(data => {
                            console.log("Fetched nearby posts:", data);
                            container.innerHTML = '';
                            if (!data || data.length === 0) {
                                container.innerHTML =
                                    `<div class="col-12 text-center"><p class="text-muted">Không có bài viết nào gần bạn.</p></div>`;
                                return;
                            }
                            data.forEach(post => {
                                const html = `
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="card h-100 shadow-sm rounded-3 border-0">
                                        ${post.thumbnail
                                            ? `<img src="/storage/${post.thumbnail}" class="card-img-top rounded-top-3" style="height:200px;object-fit:cover;">`
                                            : `<div class="d-flex justify-content-center align-items-center bg-light" style="height:200px;"><span class="text-muted">Chưa có ảnh</span></div>`}
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="fw-bold text-truncate mb-2" title="${post.title}">${post.title}</h5>
                                            <p class="text-danger fw-semibold mb-2">${Number(post.price).toLocaleString('vi-VN')} đ/tháng</p>
                                            <ul class="list-unstyled small mb-3">
                                                <li><i class="fa fa-expand me-1 text-secondary"></i> ${post.area} m²</li>
                                                <li><i class="fa fa-map-marker-alt me-1 text-secondary"></i> ${post.district}, ${post.city}</li>
                                                <li><i class="fa fa-home me-1 text-secondary"></i> ${post.address}</li>
                                                <li><i class="fa fa-road me-1 text-secondary"></i> Cách bạn ~ ${post.distance} km</li>
                                            </ul>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <small class="text-muted">Mã tin: ${post.post_code}</small>
                                                <a href="/posts/${post.slug}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                                container.insertAdjacentHTML("beforeend", html);
                            });
                        })
                        .catch(err => {
                            console.error("Fetch error:", err.message);
                            container.innerHTML =
                                `<div class="col-12 text-center"><p class="text-muted">Không thể tải bài viết gần bạn: ${err.message}</p></div>`;
                        });
                },
                (error) => {
                    console.error("Geolocation error:", error.message);
                    alert(`Không thể lấy vị trí: ${error.message}`);
                    document.getElementById("suggested-posts").innerHTML =
                        `<div class="col-12 text-center"><p class="text-muted">Không thể lấy vị trí của bạn: ${error.message}</p></div>`;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    </script>
@endpush
