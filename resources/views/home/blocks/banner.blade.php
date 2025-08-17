<div class="container-fluid" style="padding: 0; margin: 0;">
    <!-- ============================================================== -->
    <!-- Top header  -->
    <!-- ============================================================== -->

    <!-- ============================ Hero Banner Start ================================== -->
    <div class="hero-banner vedio-banner">
        <div class="overlay"></div>

        <video playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
            <source src="{{ asset('assets/client/img/banners.mp4') }}" type="video/mp4">
        </video>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <h1 class="big-header-capt mb-0 text-light">Tìm ngôi nhà tiếp theo của bạn</h1>
                    <p class="text-center mb-4 text-light">Khám phá bất động sản mới & nổi bật tại khu vực của bạn.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="search-container">
                        <!-- Bọc thêm div để căn giữa -->
                        <div class="d-flex justify-content-center">
                            <form method="GET" action="{{ route('search.results') }}"
                                class="d-flex align-items-center flex-wrap bg-light p-3 rounded shadow"
                                style="max-width: 900px; width: 100%;">

                                <div class="me-2">
                                    <label class="text-dark mb-0">Tỉnh/Thành phố</label>
                                    <select id="city" name="city" class="form-select">
                                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                                        @foreach ($cities ?? [] as $city)
                                            <option value="{{ $city }}"
                                                {{ request('city') == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <span class="text-light mx-2 align-self-end">|</span>

                                <div class="me-2">
                                    <label class="text-dark mb-0">Quận/Huyện</label>
                                    <select id="district" name="district" class="form-select" disabled>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                        @if (request('district'))
                                            <option value="{{ request('district') }}" selected>{{ request('district') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <span class="text-light mx-2 align-self-end">|</span>

                                <div class="me-2">
                                    <label class="text-dark mb-0">Phường/Xã</label>
                                    <select id="ward" name="ward" class="form-select" disabled>
                                        <option value="">-- Chọn Phường/Xã --</option>
                                        @if (request('ward'))
                                            <option value="{{ request('ward') }}" selected>{{ request('ward') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary ms-0 align-self-end">Tìm kiếm</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const citySelect = document.getElementById("city");
            const districtSelect = document.getElementById("district");
            const wardSelect = document.getElementById("ward");

            // Vô hiệu hóa quận/huyện và phường/xã ban đầu
            districtSelect.disabled = true;
            wardSelect.disabled = true;

            // Log danh sách tỉnh/thành trong dropdown
            console.log("Danh sách tỉnh/thành trong dropdown:", @json($cities));

            // Hàm load quận/huyện
            function loadDistricts(city) {
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                districtSelect.disabled = !city;
                wardSelect.disabled = true;

                if (city) {
                    fetch(`/search/districts/${encodeURIComponent(city)}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            console.log("Mã trạng thái phản hồi (quận/huyện):", res.status);
                            if (!res.ok) {
                                throw new Error(`Lỗi khi tải quận/huyện: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log("Dữ liệu quận/huyện:", data);
                            districtSelect.innerHTML =
                                '<option value="">-- Chọn Quận/Huyện --</option>';
                            if (!data || data.length === 0) {
                                districtSelect.innerHTML =
                                    '<option value="">Không có quận/huyện</option>';
                                console.warn(`Không tìm thấy quận/huyện cho thành phố: ${city}`);
                                alert(
                                    `Không tìm thấy quận/huyện cho ${city}. Vui lòng thử tỉnh/thành khác.`
                                );
                                return;
                            }
                            data.forEach(d => {
                                const opt = document.createElement("option");
                                opt.value = d;
                                opt.textContent = d;
                                if (d === @json(request('district'))) {
                                    opt.selected = true;
                                }
                                districtSelect.appendChild(opt);
                            });
                            districtSelect.disabled = false;

                            // Nếu đã chọn quận/huyện, load danh sách phường/xã
                            if (@json(request('district'))) {
                                loadWards(@json(request('district')));
                            }
                        })
                        .catch(err => {
                            console.error("Lỗi load quận/huyện:", err.message);
                            districtSelect.innerHTML =
                                '<option value="">Lỗi tải quận/huyện, vui lòng thử lại</option>';
                            districtSelect.disabled = true;
                            alert(`Lỗi tải danh sách quận/huyện cho ${city}: ${err.message}`);
                        });
                }
            }

            // Hàm load phường/xã
            function loadWards(district) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = !district;

                if (district) {
                    fetch(`/search/wards/${encodeURIComponent(district)}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            console.log("Mã trạng thái phản hồi (phường/xã):", res.status);
                            if (!res.ok) {
                                throw new Error(`Lỗi khi tải phường/xã: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log("Dữ liệu phường/xã:", data);
                            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                            if (!data || data.length === 0) {
                                wardSelect.innerHTML =
                                    '<option value="">Không có phường/xã</option>';
                                console.warn(`Không tìm thấy phường/xã cho quận/huyện: ${district}`);
                                wardSelect.disabled = true;
                                return;
                            }
                            data.forEach(w => {
                                const opt = document.createElement("option");
                                opt.value = w;
                                opt.textContent = w;
                                if (w === @json(request('ward'))) {
                                    opt.selected = true;
                                }
                                wardSelect.appendChild(opt);
                            });
                            wardSelect.disabled = false;
                        })
                        .catch(err => {
                            console.error("Lỗi load phường/xã:", err.message);
                            wardSelect.innerHTML =
                                '<option value="">Lỗi tải phường/xã, vui lòng thử lại</option>';
                            wardSelect.disabled = true;
                            alert(`Lỗi tải danh sách phường/xã cho ${district}: ${err.message}`);
                        });
                }
            }

            // Khi chọn thành phố -> load quận/huyện
            citySelect.addEventListener("change", function() {
                loadDistricts(this.value);
            });

            // Khi chọn quận/huyện -> load phường/xã
            districtSelect.addEventListener("change", function() {
                loadWards(this.value);
            });

            // Load quận/huyện và phường/xã nếu đã có giá trị từ request
            if (@json(request('city'))) {
                loadDistricts(@json(request('city')));
            }

            // Gợi ý bài viết gần bạn
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
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
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                },
                                body: JSON.stringify({
                                    lat,
                                    lng
                                })
                            })
                            .then(res => {
                                if (!res.ok) throw new Error(`Lỗi HTTP! Trạng thái: ${res.status}`);
                                return res.json();
                            })
                            .then(data => {
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
                                                    <li><i class="fa fa-road me-1 text-secondary"></i> Cách bạn ~ ${Math.round(post.distance * 100) / 100} km</li>
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
                                console.error("Lỗi fetch:", err.message);
                                container.innerHTML =
                                    `<div class="col-12 text-center"><p class="text-muted">Không thể tải bài viết gần bạn: ${err.message}</p></div>`;
                            });
                    },
                    (error) => {
                        console.error("Lỗi định vị:", error.message);
                        document.getElementById("suggested-posts").innerHTML =
                            `<div class="col-12 text-center"><p class="text-muted">Không thể lấy vị trí của bạn: ${error.message}</p></div>`;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                document.getElementById("suggested-posts").innerHTML =
                    `<div class="col-12 text-center"><p class="text-muted">Trình duyệt không hỗ trợ định vị.</p></div>`;
            }
        });
    </script>
@endpush
