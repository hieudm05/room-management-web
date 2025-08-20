<style>
    .suggestions-box {
        border: 1px solid #ccc;
        background: #fff;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1000;
        max-height: 250px;
        overflow-y: auto;
        display: none;
        /* Ẩn mặc định */
    }

    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background: #f0f0f0;
    }
</style>

<div class="container-fluid" style="padding: 0; margin: 0;">
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
                    <div class="search-container my-4">
                        <div class="d-flex justify-content-center">
                            <form action="{{ route('search.results') }}" method="GET"
                                class="d-flex w-75 position-relative">
                                <input type="text" name="keyword" id="search-input"
                                    class="form-control form-control-lg me-2"
                                    placeholder="Nhập từ khóa (VD: quận, phường, thành phố...)"
                                    value="{{ request('keyword') }}" autocapitalize="off" autocomplete="off">
                                <div id="suggestions-box" class="suggestions-box"></div>
                                <button type="submit" class="btn btn-lg btn-danger ms-2">Tìm kiếm</button>
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
        $(document).ready(function() {
            // Khi người dùng nhập
            $('#search-input').on('input', function() {
                let query = $(this).val().trim();
                if (!query) {
                    $('#suggestions-box').empty().hide();
                    return;
                }

                $.get("{{ route('search.api-suggestions') }}", {
                    query: query
                }, function(data) {
                    console.log("Query gửi đi:", query); // Debug: Kiểm tra từ khóa
                    console.log("Response:", data); // Debug: Kiểm tra response
                    if (data.length > 0) {
                        let html = data.map(item => '<div class="suggestion-item">' + item +
                            '</div>').join('');
                        $('#suggestions-box').html(html).show();
                    } else {
                        $('#suggestions-box').empty().hide();
                    }
                }).fail(function(xhr, status, error) {
                    console.error("Lỗi AJAX:", error); // Debug: Kiểm tra lỗi
                    $('#suggestions-box').empty().hide();
                });
            });

            // Click chọn gợi ý
            $(document).on('click', '.suggestion-item', function() {
                $('#search-input').val($(this).text());
                $('#suggestions-box').empty().hide();
            });

            // Click ra ngoài → ẩn gợi ý
            $(document).click(function(e) {
                if (!$(e.target).closest('#search-input, #suggestions-box').length) {
                    $('#suggestions-box').empty().hide();
                }
            });
        });
    </script>
@endpush
