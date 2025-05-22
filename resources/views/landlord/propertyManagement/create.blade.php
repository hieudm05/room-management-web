@extends('landlord.layouts.app')

@section('title', 'List Properties ')

@section('content')
{{-- <form id="propertyForm" method="POST" action="{{ route('landlord.properties.store') }}">
    @csrf
    <input type="text" name="name" placeholder="Tên bất động sản" required>

    <div>
        <select id="province" name="province" required>
            <option value="">-- Chọn tỉnh --</option>
        </select>

        <select id="district" name="district" required disabled>
            <option value="">-- Chọn huyện --</option>
        </select>

        <select id="ward" name="ward" required disabled>
            <option value="">-- Chọn xã --</option>
        </select>

        <input type="text" id="detailed_address" name="detailed_address" placeholder="Số nhà, đường..." required>
    </div>

    <!-- Hidden inputs để lưu tọa độ -->
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">

    <div id="map" style="height: 400px; margin-top: 15px;"></div>

    <button type="submit">Thêm bất động sản</button>
</form> --}}
    <div class="container card">
        <div class="card-header align-items-center d-flex justify-content-center">
            <h4 class="card-title mb-0">Đăng ký bất động sản mới</h4>
            {{-- <a href="{{ route('landlord.properties.list') }}" class="btn btn-secondary btn-sm">← Danh sách</a> --}}
        </div>

        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-lg-8"> {{-- ⬅ Giới hạn chiều rộng nội dung --}}

                    {{-- FORM bắt đầu từ đây --}}
                    <form id="propertyForm" method="POST" action="{{ route('landlords.properties.store') }}" enctype="multipart/form-data">
                        @csrf

                            <form id="propertyForm" method="POST" action="{{ route('landlords.properties.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                {{-- Nhóm 1: Thông tin bất động sản --}}
                <div class="mb-4">
                    <h5 class="mb-3 text-primary">1. Thông tin bất động sản</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Tên bất động sản <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Khu trọ Nguyễn Văn A" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Giới thiệu chung về khu trọ..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Nhóm 2: Địa chỉ --}}
                <div class="mb-4">
                    <h5 class="mb-3 text-success">2. Địa chỉ chi tiết</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                            <select id="province" name="province" class="form-select" required>
                                <option value="">-- Chọn tỉnh --</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                            <select id="district" name="district" class="form-select" required disabled>
                                <option value="">-- Chọn huyện --</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                            <select id="ward" name="ward" class="form-select" required disabled>
                                <option value="">-- Chọn xã --</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="detailed_address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                            <input type="text" id="detailed_address" name="detailed_address" class="form-control" placeholder="Số nhà, đường..." required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Xác định vị trí trên bản đồ</label>
                            <div id="map" style="height: 350px; border: 1px solid #ccc;"></div>
                        </div>
                    </div>

                    <!-- Hidden toạ độ -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>

                {{-- Nhóm 3: Giấy tờ pháp lý --}}
                <div class="mb-4">
                    <h5 class="mb-3 text-warning">3. Giấy tờ pháp lý</h5>
                    <div class="alert alert-info">
                        Bạn có thể <strong>bổ sung các giấy tờ không quan trọng sau</strong>. Tuy nhiên, ít nhất 1 giấy tờ cơ bản nên được tải lên ngay.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sổ đỏ (nếu có)</label>
                        <input type="file" name="document_files[so_do]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giấy phép xây dựng (nếu có)</label>
                        <input type="file" name="document_files[giay_phep_xay_dung]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giấy chứng nhận PCCC (nếu có)</label>
                        <input type="file" name="document_files[pccc]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giấy phép kinh doanh (nếu có)</label>
                        <input type="file" name="document_files[giay_phep_kinh_doanh]" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">📤 Gửi đăng ký</button>
                </div>
            </form>

                    </form>
                    {{-- FORM kết thúc tại đây --}}

                </div>
            </div>
        </div>
    </div>
<!-- Thư viện -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script>
    let map = L.map('map').setView([21.028511, 105.804817], 13); // Hà Nội mặc định
    let marker = null;
    const apiKey =  '{{ config("services.locationiq.key") }}'

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    function updateMapWithAddress() {
        let detail = $("#detailed_address").val().trim();
        let provinceText = $("#province option:selected").text();
        let districtText = $("#district option:selected").text();
        let wardText = $("#ward option:selected").text();

        if (!detail && (!wardText || wardText === '-- Chọn xã --')) return;

        let fullAddress = `${detail ? detail + ', ' : ''}${wardText}, ${districtText}, ${provinceText}, Việt Nam`;

        if (fullAddress.length < 10) return;

        fetch(`https://us1.locationiq.com/v1/search.php?key=${apiKey}&q=${encodeURIComponent(fullAddress)}&format=json`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let lat = parseFloat(data[0].lat);
                    let lon = parseFloat(data[0].lon);

                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lon]).addTo(map);
                    map.setView([lat, lon], 16);

                    // Gán tọa độ vào input ẩn
                    $('#latitude').val(lat);
                    $('#longitude').val(lon);
                } else {
                    alert("Không tìm thấy vị trí với địa chỉ bạn nhập.");
                }
            })
            .catch(() => {
                alert("Đã xảy ra lỗi khi định vị bản đồ.");
            });
    }

    $(document).ready(function () {
        // Load danh sách tỉnh
        $.get('/provinces', function (data) {
            data.forEach(function (province) {
                $('#province').append(`<option value="${province.code}">${province.name}</option>`);
            });
        });

        $('#province').on('change', function () {
            let provinceCode = $(this).val();
            $('#district').html('<option value="">-- Chọn huyện --</option>').prop('disabled', true);
            $('#ward').html('<option value="">-- Chọn xã --</option>').prop('disabled', true);

            if (!provinceCode) return;

            $.get(`/districts/${provinceCode}`, function (data) {
                if (data.length > 0) {
                    data.forEach(function (district) {
                        $('#district').append(`<option value="${district.code}">${district.name}</option>`);
                    });
                    $('#district').prop('disabled', false);
                }
                updateMapWithAddress();
            });
        });

        $('#district').on('change', function () {
            let districtCode = $(this).val();
            $('#ward').html('<option value="">-- Chọn xã --</option>').prop('disabled', true);

            if (!districtCode) return;

            $.get(`/wards/${districtCode}`, function (data) {
                if (data.length > 0) {
                    data.forEach(function (ward) {
                        $('#ward').append(`<option value="${ward.code}">${ward.name}</option>`);
                    });
                    $('#ward').prop('disabled', false);
                }
                updateMapWithAddress();
            });
        });

        $('#ward').on('change', updateMapWithAddress);

        let debounceTimer;
        $('#detailed_address').on('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(updateMapWithAddress, 1000);
        });
    });
</script>
@endsection
