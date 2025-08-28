@extends('home.layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <!-- Existing Recent Listings Section -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Phòng trọ dành cho bạn</h2>
                    <p>Danh sách các phòng trọ mới được đăng lên hệ thống.</p>
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
                                {{ number_format((int)$post->price, 0, ',', '.') }} đ/tháng
                            </p>

                            <ul class="list-unstyled small mb-3">
                                <li><i class="fa fa-expand me-1 text-secondary"></i> {{ $post->area }} m²</li>
                                <li><i class="fa fa-map-marker-alt me-1 text-secondary"></i>
                                    {{ $post->district }}, {{ $post->city }}
                                </li>
                                <li><i class="fa fa-home me-1 text-secondary"></i> {{ $post->address }}</li>
                                
                            </ul>

                            

                            <div class="d-flex justify-content-between align-items-center mt-auto">
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
        document.addEventListener("DOMContentLoaded", function() {
            console.log("✅ Script đang chạy!");
            console.log("Debug: Browser geolocation support:", !!navigator.geolocation);
            console.log("Debug: Running on HTTPS:", window.location.protocol.includes("https"));
            console.log("Debug: Hostname:", window.location.hostname);

            // Check for HTTPS (excluding localhost for development)
            // if (!window.location.protocol.includes("https") && window.location.hostname !== "localhost") {
            //     console.error("Geolocation requires HTTPS");
            //     document.getElementById("suggested-posts").innerHTML =
            //         `<div class="col-12 text-center">
        //     <p class="text-danger">Yêu cầu HTTPS để sử dụng định vị.</p>
        //     <small>Vui lòng truy cập trang qua HTTPS hoặc liên hệ quản trị viên.</small>
        // </div>`;
            //     return;
            // }

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                console.error("Geolocation not supported by this browser");
                document.getElementById("suggested-posts").innerHTML =
                    `<div class="col-12 text-center">
                <p class="text-danger">Trình duyệt không hỗ trợ định vị.</p>
                <small>Vui lòng sử dụng trình duyệt khác (Chrome, Firefox, Safari) hoặc kiểm tra cài đặt thiết bị.</small>
            </div>`;
                return;
            }

            console.log("Requesting user location...");

            // Request geolocation
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log(`Geolocation success - Latitude: ${lat}, Longitude: ${lng}`);
                    console.log("Debug: Position accuracy:", position.coords.accuracy, "meters");

                    // Show loading spinner
                    const container = document.getElementById("suggested-posts");
                    container.innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                </div>
            `;

                    // Fetch nearby posts
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
                            console.log("Debug: Fetch response status:", res.status);
                            if (!res.ok) {
                                throw new Error(`HTTP error! Status: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log("Debug: Fetched nearby posts:", data);
                            container.innerHTML = '';
                            if (!data || data.length === 0) {
                                container.innerHTML =
                                    `<div class="col-12 text-center">
                            <p class="text-muted">Không tìm thấy bài viết nào gần bạn.</p>
                            <a href="/posts" class="btn btn-sm btn-primary mt-2">Xem tất cả bài viết</a>
                        </div>`;
                                return;
                            }
                            data.forEach(post => {
                                console.log("Debug: Rendering post:", post.post_code);
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
                            console.log("Debug: Full error object:", err);
                            document.getElementById("suggested-posts").innerHTML =
                                `<div class="col-12 text-center">
                        <p class="text-danger">Không thể tải bài viết gần bạn: ${err.message}</p>
                        <small>Vui lòng kiểm tra kết nối mạng hoặc thử lại sau.</small>
                    </div>`;
                        });
                },
                (error) => {
                    console.error("Geolocation error:", error.message);
                    console.log("Debug: Error code:", error.code);
                    let errorMessage = "Không thể lấy vị trí của bạn.";
                    let guidance = "";
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMessage = "Bạn đã từ chối cấp quyền truy cập vị trí.";
                        guidance = `
                    <small>
                        Vui lòng cấp quyền vị trí trong cài đặt trình duyệt:<br>
                        - Chrome: Settings > Privacy and Security > Site Settings > Location<br>
                        - Firefox: Preferences > Privacy & Security > Permissions > Location<br>
                        - Safari: Settings > Websites > Location<br>
                        Hoặc <a href="/posts" class="text-primary">xem tất cả bài viết</a>.
                    </small>`;
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        errorMessage = "Không thể xác định vị trí của bạn.";
                        guidance = "<small>Vui lòng kiểm tra GPS hoặc kết nối mạng của thiết bị.</small>";
                    } else if (error.code === error.TIMEOUT) {
                        errorMessage = "Yêu cầu vị trí mất quá nhiều thời gian.";
                        guidance = "<small>Vui lòng thử lại hoặc kiểm tra kết nối mạng.</small>";
                    }
                    document.getElementById("suggested-posts").innerHTML =
                        `<div class="col-12 text-center">
                    <p class="text-danger">${errorMessage}</p>
                    ${guidance}
                </div>`;
                }, {
enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    </script>
@endpush
