<style>
    /* Z-index chuẩn Bootstrap để modal nằm trên backdrop */
    .modal {
        z-index: 1050 !important;
    }

    .modal-backdrop {
        z-index: 4 !important;
    }

    /* Gợi ý tìm kiếm: đủ cao hơn video nhưng thấp hơn modal */
    .suggestions-box {
        border: 1px solid #ccc;
        background: #fff;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1030;
        /* < 1040 để không đè modal */
        max-height: 250px;
        overflow-y: auto;
        display: none;
    }

    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background: #f0f0f0;
    }

    /* Khóa cuộn nền khi mở modal (Bootstrap sẽ gắn class .modal-open vào body) */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
        /* tránh lệch layout khi ẩn scrollbar */
    }

    /* Modal căn giữa, rộng và chỉ cuộn bên trong */
    .modal-dialog {
        margin: auto;
    }

    .modal-content {
        max-height: 90vh;
        /* modal không vượt quá chiều cao màn hình */
        overflow-y: auto;
        /* chỉ cuộn bên trong modal */
        border-radius: 12px;
    }
</style>

<div class="container-fluid" style="padding: 0; margin: 0;">
    <div class="hero-banner vedio-banner">
        <div class="overlay"></div>
        <video playsinline autoplay muted loop>
            <source src="{{ asset('assets/client/img/banners.mp4') }}" type="video/mp4">
        </video>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <h1 class="big-header-capt mb-0 text-light">Tìm ngôi nhà tiếp theo của bạn</h1>
                    <p class="text-center mb-4 text-light">Khám phá bất động sản mới & nổi bật tại khu vực của bạn.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="search-container my-4">
                        <div class="d-flex justify-content-center">

                            {{-- ✅ Form CHÍNH duy nhất --}}
                            <form id="searchForm" action="{{ route('search.results') }}" method="GET"
                                class="d-flex w-75 position-relative">

                                {{-- Ô nhập tìm kiếm --}}
                                <input type="text" name="keyword" id="search-input"
                                    class="form-control form-control-lg me-2"
                                    placeholder="Nhập từ khóa (VD: quận, phường, thành phố...)"
                                    value="{{ request('keyword') }}" autocapitalize="off" autocomplete="off">

                                <div id="suggestions-box" class="suggestions-box"></div>

                                {{-- Nút mở bộ lọc (chỉ mở modal) --}}
                                <button type="button" class="btn btn-secondary btn-lg ms-2" data-bs-toggle="modal"
                                    data-bs-target="#filterModal">
                                    <i class="bi bi-funnel"></i> Bộ lọc
                                </button>

                                {{-- Nút submit CHÍNH --}}
                                <button type="submit" class="btn btn-lg btn-danger ms-2">Tìm kiếm</button>
                            </form>

                        </div>
                    </div>

                    {{-- ✅ Modal Bộ lọc (KHÔNG có <form>) --}}
                    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">

                                {{-- Header --}}
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Bộ lọc</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                {{-- Body --}}
                                <div class="modal-body">
                                    {{-- Danh mục cho thuê - SỬA: thành category_id --}}
                                    <div class="mb-4">
                                        <label class="fw-bold d-block mb-2">Danh mục cho thuê</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <input type="radio" class="btn-check" name="category_id" id="phongtro"
                                                value="1" form="searchForm"
                                                {{ request('category_id') == '1' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="phongtro">Phòng trọ</label>

                                            <input type="radio" class="btn-check" name="category_id" id="oghep"
                                                value="2" form="searchForm"
                                                {{ request('category_id') == '2' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="oghep">Ở ghép</label>

                                            <input type="radio" class="btn-check" name="category_id" id="canhomini"
                                                value="3" form="searchForm"
                                                {{ request('category_id') == '3' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="canhomini">Căn hộ mini</label>
                                        </div>
                                    </div>

                                    {{-- Khoảng diện tích --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Khoảng diện tích</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <input type="radio" class="btn-check" name="area" id="area_all"
                                                value="" form="searchForm"
                                                {{ !request('area') == '' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2" for="area_all">Tất
                                                cả</label>

                                            <input type="radio" class="btn-check" name="area" id="area1"
                                                value="0-20" form="searchForm"
                                                {{ request('area') == '0-20' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2" for="area1">Dưới
                                                20m²</label>

                                            <input type="radio" class="btn-check" name="area" id="area2"
                                                value="20-30" form="searchForm"
                                                {{ request('area') == '20-30' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2"
                                                for="area2">20–30m²</label>

                                            <input type="radio" class="btn-check" name="area" id="area3"
                                                value="30-50" form="searchForm"
                                                {{ request('area') == '30-50' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2"
                                                for="area3">30–50m²</label>

                                            <input type="radio" class="btn-check" name="area" id="area4"
                                                value="50-70" form="searchForm"
                                                {{ request('area') == '50-70' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2"
                                                for="area4">50–70m²</label>

                                            <input type="radio" class="btn-check" name="area" id="area5"
                                                value="70-90" form="searchForm"
                                                {{ request('area') == '70-90' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2"
                                                for="area5">70–90m²</label>

                                            <input type="radio" class="btn-check" name="area" id="area6"
                                                value="90-9999" form="searchForm"
                                                {{ request('area') == '90-9999' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded px-2" for="area6">Trên
                                                90m²</label>
                                        </div>
                                    </div>

                                    {{-- Khoảng giá - SỬA: giá trị từ triệu thành VND --}}
                                    <div class="mb-4">
                                        <label class="fw-bold d-block mb-2">Khoảng giá</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <input type="radio" class="btn-check" name="price" id="price_all"
                                                value="" form="searchForm"
                                                {{ !request('price') == '' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price_all">Tất cả</label>

                                            <input type="radio" class="btn-check" name="price" id="price1"
                                                value="0-1000000" form="searchForm"
                                                {{ request('price') == '0-1000000' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price1">Dưới 1 triệu</label>

                                            <input type="radio" class="btn-check" name="price" id="price2"
                                                value="1000000-2000000" form="searchForm"
                                                {{ request('price') == '1000000-2000000' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price2">1–2 triệu</label>

                                            <input type="radio" class="btn-check" name="price" id="price3"
                                                value="2000000-3000000" form="searchForm"
                                                {{ request('price') == '2000000-3000000' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price3">2–3 triệu</label>

                                            <input type="radio" class="btn-check" name="price" id="price4"
                                                value="3000000-5000000" form="searchForm"
                                                {{ request('price') == '3000000-5000000' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price4">3–5 triệu</label>

                                            <input type="radio" class="btn-check" name="price" id="price5"
                                                value="5000000-7000000" form="searchForm"
                                                {{ request('price') == '5000000-7000000' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger" for="price5">5–7 triệu</label>
                                        </div>
                                    </div>

                                    {{-- Đặc điểm nổi bật - SỬA: value phù hợp với backend --}}
                                    <div class="mb-4">
                                        <label class="fw-bold d-block mb-2">Đặc điểm nổi bật</label>
                                        <div class="d-flex flex-wrap gap-2">

                                            {{-- Tất cả (reset filter) --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities_all" value="" form="searchForm">
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities_all">Tất cả</label>


                                            {{-- Đầy đủ nội thất --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities1" value="Đầy đủ nội thất" form="searchForm"
                                                {{ in_array('Đầy đủ nội thất', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities1">Đầy đủ nội thất</label>

                                            {{-- Có máy giặt --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities2" value="Có máy giặt" form="searchForm"
                                                {{ in_array('Có máy giặt', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities2">Có máy giặt</label>

                                            {{-- Có máy lạnh --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities3" value="Có máy lạnh" form="searchForm"
                                                {{ in_array('Có máy lạnh', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities3">Có máy lạnh</label>

                                            {{-- Không chung chủ --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities4" value="Không chung chủ" form="searchForm"
                                                {{ in_array('Không chung chủ', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities4">Không chung chủ</label>

                                            {{-- Có thang máy --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities5" value="Có thang máy" form="searchForm"
                                                {{ in_array('Có thang máy', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities5">Có thang máy</label>

                                            {{-- Bảo vệ 24/24 --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities6" value="Bảo vệ 24/24" form="searchForm"
                                                {{ in_array('Bảo vệ 24/24', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities6">Bảo vệ 24/24</label>

                                            {{-- Hầm để xe --}}
                                            <input type="checkbox" class="btn-check" name="amenities[]"
                                                id="amenities7" value="Hầm để xe" form="searchForm"
                                                {{ in_array('Hầm để xe', (array) request('amenities', [])) ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger rounded-pill px-3"
                                                for="amenities7">Hầm để xe</label>

                                        </div>
                                    </div>

                                </div>

                                {{-- Footer: Áp dụng chỉ đóng modal, KHÔNG submit --}}
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                        Áp dụng
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{-- End Modal --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm xóa tất cả bộ lọc
    function clearFilters() {
        // Reset tất cả radio buttons về trạng thái mặc định
        document.querySelectorAll('input[name="category_id"]').forEach(input => input.checked = false);
        document.getElementById('area_all').checked = true;
        document.getElementById('price_all').checked = true;
        document.getElementById('amenities_all').checked = true;
    }


            // Log danh sách tỉnh/thành trong dropdown
          
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const keyword = document.getElementById('search-input').value.trim();
        const amenitiesChecked = document.querySelectorAll('input[name="amenities[]"]:checked').length;
        const categoryChecked = document.querySelector('input[name="category_id"]:checked');
        const areaChecked = document.querySelector('input[name="area"]:checked');
        const priceChecked = document.querySelector('input[name="price"]:checked');

        if (!keyword && !amenitiesChecked && !categoryChecked && !areaChecked && !priceChecked) {
            e.preventDefault(); // Ngăn form submit
            // Không cần alert
        }
    });
</script>
