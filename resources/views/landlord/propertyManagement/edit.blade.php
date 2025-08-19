@extends('landlord.layouts.app')
@section('title', 'Chỉnh sửa bất động sản')

@section('content')
    <style>
        :root {
            --danger-color: #fff3f3;
        }

        .border-dashed {
            border: 1px dashed red;
            transition: border-color 0.3s ease;
        }

        .preview-container {
            width: 100%;
            max-width: 150px;
            height: 100px;
            margin: 5px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border-radius: 8px;
            position: relative;
        }

        .preview-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .remove-btn {
            cursor: pointer;
            color: red;
            margin-left: 10px;
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.8);
            padding: 2px 5px;
            border-radius: 3px;
        }

        .document-preview {
            margin: 5px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
    </style>

    <div class="container card my-4">
        <div class="card-header align-items-center d-flex justify-content-between">
            <h3 class="card-title mb-0">Chỉnh sửa bất động sản</h3>
            <a href="{{ route('landlords.properties.list') }}" class="btn btn-secondary btn-sm">← Danh sách</a>
        </div>
        <div class="card-body">
            <form id="propertyForm" method="POST" action="{{ route('landlords.properties.update', $property->property_id) }}"
                enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row g-4">
                    <!-- Ảnh đại diện -->
                    <div class="col-12">
                        <h5 class="mb-3 text-info">Ảnh đại diện bất động sản</h5>
                        <div class="mb-3">
                            <label for="main_images" class="form-label">Chọn ảnh đại diện <span
                                    class="text-danger">*</span></label>
                            <input type="file" id="main_images" name="image_urls[]" multiple accept="image/*"
                                class="form-control @error('image_urls.*') is-invalid @enderror">
                            @error('image_urls.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="mainImagesPreview" class="d-flex flex-wrap">
                            <!-- Ảnh chính -->
                            @if ($property->image_url)
                                <div class="preview-container">
                                    <img src="{{ $property->image_url }}" alt="Ảnh chính">
                                    <span class="remove-btn" data-type="main">Xóa</span>
                                </div>
                            @endif
                            <!-- Ảnh phụ -->
                            @foreach ($property->images as $index => $image)
                                <div class="preview-container">
                                    <img src="{{ $image->image_path }}" alt="Ảnh phụ">
                                    <span class="remove-btn" data-type="extra" data-id="{{ $image->id }}">Xóa</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Thông tin bất động sản -->
                    <div class="col-12">
                        <h5 class="mb-3 text-primary">1. Thông tin bất động sản</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Tên bất động sản <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="VD: Khu trọ Nguyễn Văn A" value="{{ old('name', $property->name) }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea id="description" name="description" class="form-control" rows="3"
                                    placeholder="Giới thiệu chung về khu trọ...">{{ old('description', $property->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Nội quy -->
                    <div class="col-12">
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <label for="rules" class="form-label">Nội quy<span
                                                class="text-danger">*</span></label>
                                        <input type="hidden" id="rules" name="rules"
                                            value="{{ old('rules', $property->rules) }}"
                                            class="form-control @error('rules') is-invalid @enderror" required>
                                        <div id="quill-editor" style="height: 350px">
                                            {!! old('rules', $property->rules) !!}
                                        </div>
                                        @error('rules')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Địa chỉ chi tiết -->
                    <div class="col-12">
                        <h5 class="mb-3 text-success">2. Địa chỉ chi tiết</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="province" class="form-label">Tỉnh/Thành phố <span
                                        class="text-danger">*</span></label>
                                <select id="province" name="province"
                                    class="form-select @error('province') is-invalid @enderror" required>
                                    <option value="">-- Chọn tỉnh --</option>
                                </select>
                                @error('province')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="district" class="form-label">Quận/Huyện <span
                                        class="text-danger">*</span></label>
                                <select id="district" name="district"
                                    class="form-select @error('district') is-invalid @enderror" required disabled>
                                    <option value="">-- Chọn huyện --</option>
                                </select>
                                @error('district')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="ward" class="form-label">Phường/Xã <span
                                        class="text-danger">*</span></label>
                                <select id="ward" name="ward"
                                    class="form-select @error('ward') is-invalid @enderror" required disabled>
                                    <option value="">-- Chọn xã --</option>
                                </select>
                                @error('ward')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="detailed_address" class="form-label">Địa chỉ cụ thể <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="detailed_address" name="detailed_address"
                                    class="form-control @error('detailed_address') is-invalid @enderror"
                                    placeholder="Số nhà, đường..."
                                    value="{{ old('detailed_address', $parsedAddress['detailed_address']) }}" required>
                                @error('detailed_address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Xác định vị trí trên bản đồ</label>
                                <div id="map" style="height: 350px; border: 1px solid #ccc;"></div>
                                <input type="hidden" id="latitude" name="latitude"
                                    value="{{ old('latitude', $property->latitude) }}">
                                <input type="hidden" id="longitude" name="longitude"
                                    value="{{ old('longitude', $property->longitude) }}">
                            </div>
                        </div>
                    </div>


                    <!-- Submit Button -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">📤 Cập nhật</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Thư viện -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.6/purify.min.js"></script>

    <script>
        // Bootstrap validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#propertyForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                }
            }, false);
        });

        // Main images preview
        const mainImagesInput = document.getElementById('main_images');
        const mainImagesPreview = document.getElementById('mainImagesPreview');
        mainImagesInput.addEventListener('change', function(e) {
            Array.from(e.target.files).forEach((file, index) => {
                const div = document.createElement('div');
                div.classList.add('preview-container');
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                const removeBtn = document.createElement('span');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = 'Xóa';
                removeBtn.addEventListener('click', function() {
                    URL.revokeObjectURL(img.src);
                    div.remove();
                    const dt = new DataTransfer();
                    Array.from(mainImagesInput.files).forEach((f, i) => {
                        if (i !== index) dt.items.add(f);
                    });
                    mainImagesInput.files = dt.files;
                });
                div.appendChild(img);
                div.appendChild(removeBtn);
                mainImagesPreview.appendChild(div);
            });
        });

        // Remove existing images
        document.querySelectorAll('#mainImagesPreview .remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.preview-container').remove();
            });
        });

        // Document previews
        function updateDocumentPreviews() {
            const documentPreviews = document.getElementById('documentPreviews');
            documentPreviews.innerHTML = '';
            const documentFiles = document.querySelectorAll('input[name="document_files[]"]');
            documentFiles.forEach((input, index) => {
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const typeSelect = input.closest('.document-row').querySelector(
                        'select[name="document_types[]"]').value;
                    const div = document.createElement('div');
                    div.classList.add('document-preview');
                    div.innerHTML =
                        `Giấy tờ: ${typeSelect || 'Chưa chọn'} - Tệp: ${file.name} <span class="remove-btn" data-index="${index}">Xóa</span>`;
                    documentPreviews.appendChild(div);
                }
            });

            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = this.getAttribute('data-index');
                    const documentRows = document.querySelectorAll('.document-row');
                    if (documentRows[index]) {
                        documentRows[index].remove();
                        updateDocumentPreviews();
                    }
                });
            });
        }


        // Quill Editor
        document.addEventListener('DOMContentLoaded', function() {
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            const initialContent = @json(old('rules', $property->rules));
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            const rulesInput = document.querySelector('#rules');
            quill.on('text-change', function() {
                rulesInput.value = DOMPurify.sanitize(quill.root.innerHTML);
            });

            const form = document.querySelector('#propertyForm');
            form.addEventListener('submit', function(e) {
                rulesInput.value = DOMPurify.sanitize(quill.root.innerHTML);
                console.log('Rules value before submit:', rulesInput.value);

                const documentTypes = document.querySelectorAll('select[name="document_types[]"]');
                const documentFiles = document.querySelectorAll('input[name="document_files[]"]');
                let valid = true;

                documentTypes.forEach((select, index) => {
                    if (!select.value && documentFiles[index].files.length) {
                        valid = false;
                        select.classList.add('is-invalid');
                    }
                });

                if (!quill.getText().trim() || quill.root.innerHTML === '<p><br></p>') {
                    valid = false;
                    rulesInput.classList.add('is-invalid');
                    let errorDiv = rulesInput.nextElementSibling;
                    if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                        errorDiv = document.createElement('div');
                        errorDiv.classList.add('invalid-feedback', 'd-block');
                        errorDiv.textContent = 'Nội quy không được để trống.';
                        rulesInput.parentNode.appendChild(errorDiv);
                    }
                }

                if (!valid) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ các trường bắt buộc.');
                }
            });
        });

        // Map and address
        // Map and address
        document.addEventListener('DOMContentLoaded', async function() {
            var vietmapApiKey = '{{ config('services.viet_map.key') }}'; // Sử dụng API key của Vietmap
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
                        // BẮT SỰ KIỆN THAY ĐỔI HUYỆN
                districtSelect.addEventListener('change', async function () {
                    const selectedDistrictCode = districtSelect.value;
                    wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';
                    wardSelect.disabled = true;

                    if (!selectedDistrictCode) return;

                    try {
                        const wardsResponse = await fetch(`https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`);
                        const wardData = await wardsResponse.json();

                        wardSelect.disabled = false;
                        wardData.wards.forEach(ward => {
                            const option = new Option(ward.name, ward.code);
                            wardSelect.add(option);
                        });

                        // ✅ Cập nhật bản đồ sau khi chọn huyện
                        setTimeout(updateMapWithAddress, 600);
                    } catch (error) {
                        console.error('Lỗi tải xã:', error);
                        alert('Không thể tải danh sách xã.');
                    }
                });
            const wardSelect = document.getElementById('ward');
            const detailedAddressInput = document.getElementById('detailed_address');
            const oldProvince = '{{ old('province', $parsedAddress['province']) }}';
            const oldDistrict = '{{ old('district', $parsedAddress['district']) }}';
            const oldWard = '{{ old('ward', $parsedAddress['ward']) }}';
            const oldDetailedAddress = '{{ old('detailed_address', $parsedAddress['detailed_address']) }}';

            // Tải và gán dữ liệu địa phương ngay lập tức
            try {
                const provincesResponse = await fetch("https://provinces.open-api.vn/api/p/");
                if (!provincesResponse.ok) throw new Error('Lỗi tải tỉnh');
                const provinces = await provincesResponse.json();

                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh --</option>';
                provinceSelect.addEventListener('change', async function() {
                    const selectedProvinceCode = provinceSelect.value;
                    districtSelect.innerHTML = '<option value="">-- Chọn huyện --</option>';
                    districtSelect.disabled = true;
                    wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';
                    wardSelect.disabled = true;
                    detailedAddressInput.value = '';
                    if (!selectedProvinceCode) return;

                    try {
                        const districtsResponse = await fetch(
                            `https://provinces.open-api.vn/api/p/${selectedProvinceCode}?depth=2`
                        );
                        const provinceData = await districtsResponse.json();

                        provinceData.districts.forEach(district => {
                            const option = new Option(district.name, district.code);
                            districtSelect.add(option);
                        });

                        districtSelect.disabled = false;
                        updateMapWithAddress(); // Cập nhật bản đồ sau khi chọn tỉnh
                    } catch (error) {
                        console.error('Lỗi tải huyện:', error);
                        alert('Không thể tải danh sách huyện.');
                    }
                });

                let selectedProvinceCode = null;
                provinces.forEach(province => {
                    const option = new Option(province.name, province.code);
                    if (province.name === oldProvince) {
                        option.selected = true;
                        selectedProvinceCode = province.code;
                    }
                    provinceSelect.add(option);
                });

                if (selectedProvinceCode) {
                    const districtsResponse = await fetch(
                        `https://provinces.open-api.vn/api/p/${selectedProvinceCode}?depth=2`);
                    if (!districtsResponse.ok) throw new Error('Lỗi tải huyện');
                    const districts = await districtsResponse.json();

                    districtSelect.innerHTML = '<option value="">-- Chọn huyện --</option>';
                    districtSelect.disabled = false;
                    let selectedDistrictCode = null;
                    districts.districts.forEach(district => {
                        const option = new Option(district.name, district.code);
                        if (district.name === oldDistrict) {
                            option.selected = true;
                            selectedDistrictCode = district.code;
                        }
                        districtSelect.add(option);
                    });

                    if (selectedDistrictCode) {
                        const wardsResponse = await fetch(
                            `https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`);
                        const wards = await wardsResponse.json();

                        wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';
                        wardSelect.disabled = false;

                        wards.wards.forEach(ward => {
                            const option = new Option(ward.name, ward.code);
                            if (ward.name === oldWard) {
                                option.selected = true;
                            }
                            wardSelect.add(option);
                        });
                    }
                }

                // Gán giá trị địa chỉ chi tiết
                if (oldDetailedAddress) {
                    detailedAddressInput.value = oldDetailedAddress;
                }

            } catch (error) {
                console.error(error);
                alert('Lỗi tải danh sách địa phương. Vui lòng thử lại sau.');
            }

            // Khởi tạo bản đồ Vietmap
            let map = L.map('map').setView([{{ old('latitude', $property->latitude) }},
                {{ old('longitude', $property->longitude) }}
            ], 13);
            let marker = L.marker([{{ old('latitude', $property->latitude) }},
                {{ old('longitude', $property->longitude) }}
            ], {
                draggable: true
            }).addTo(map);

            // Thêm tile layer từ Vietmap
            L.tileLayer(`https://maps.vietmap.vn/api/tm/{z}/{x}/{y}.png?apikey=${vietmapApiKey}`, {
                maxZoom: 18,
                attribution: '&copy; <a href="https://www.vietmap.vn/">VietMap</a>'
            }).addTo(map);

            // Cập nhật bản đồ khi kéo marker
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                map.setView(pos);
                reverseGeocodeAndUpdateAddress(pos.lat, pos.lng);
            });

            // Hàm reverse geocoding với Vietmap
           async function reverseGeocodeAndUpdateAddress(lat, lon) {
    try {
        const res = await fetch(`https://maps.vietmap.vn/api/reverse/v3?apikey=${vietmapApiKey}&point.lat=${lat}&point.lng=${lon}`);
        const data = await res.json();

        if (data && data.address) {
            const addr = data.address;
            detailedAddressInput.value = addr.address || addr.display || '';

            // ===== Tìm tỉnh phù hợp =====
            const matchedProvince = [...provinceSelect.options].find(opt =>
                addr.city && opt.text.trim().includes(addr.city.trim())
            );
            if (matchedProvince) {
                provinceSelect.value = matchedProvince.value;
                await provinceSelect.dispatchEvent(new Event('change'));

                // === Đợi tỉnh load xong huyện ===
                setTimeout(async () => {
                    const matchedDistrict = [...districtSelect.options].find(opt =>
                        addr.district && opt.text.trim().includes(addr.district.trim())
                    );
                    if (matchedDistrict) {
                        districtSelect.value = matchedDistrict.value;
                        await districtSelect.dispatchEvent(new Event('change'));

                        // === Đợi huyện load xong xã ===
                        setTimeout(() => {
                            const matchedWard = [...wardSelect.options].find(opt =>
                                addr.ward && opt.text.trim().includes(addr.ward.trim())
                            );
                            if (matchedWard) {
                                wardSelect.value = matchedWard.value;
                            }
                        }, 600); // đợi ward load
                    }
                }, 600); // đợi district load
            }

            // Cập nhật lại tọa độ
            document.querySelector('#latitude').value = lat;
            document.querySelector('#longitude').value = lon;
        }
    } catch (error) {
        console.error('Lỗi reverse geocode:', error);
        // alert('Không thể định vị địa chỉ bạn vừa kéo.');
    }
}




            function updateMapWithAddress() {
                let detail = detailedAddressInput.value.trim();
                let provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
                let districtText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
                let wardText = wardSelect.options[wardSelect.selectedIndex]?.text || '';

                if (!detail && (!wardText || wardText === '-- Chọn xã --')) return;

                let fullAddress =
                    `${detail ? detail + ', ' : ''}${wardText}, ${districtText}, ${provinceText}, Việt Nam`;

                if (fullAddress.length < 10) return;

                fetch(
                        `https://maps.vietmap.vn/api/search/v3?apikey=${vietmapApiKey}&text=${encodeURIComponent(fullAddress)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0 && data[0].ref_id) {
                            let refId = data[0].ref_id;
                            return fetch(
                                    `https://maps.vietmap.vn/api/place/v3?apikey=${vietmapApiKey}&refid=${refId}`
                                    )
                                .then(res => res.json())
                                .then(place => {
                                    if (place.lat && place.lng) {
                                        const lat = parseFloat(place.lat);
                                        const lon = parseFloat(place.lng);

                                        if (marker) map.removeLayer(marker);
                                        marker = L.marker([lat, lon], {
                                            draggable: true
                                        }).addTo(map);
                                        map.setView([lat, lon], 16);

                                        document.querySelector('#latitude').value = lat;
                                        document.querySelector('#longitude').value = lon;

                                        // Gắn lại sự kiện kéo marker sau khi thêm mới
                                        marker.on('dragend', function() {
                                            const pos = marker.getLatLng();
                                            map.setView(pos);
                                            reverseGeocodeAndUpdateAddress(pos.lat, pos.lng);
                                        });
                                    } else {
                                        throw new Error("Không tìm thấy tọa độ.");
                                    }
                                });
                        } else {
                            throw new Error("Không tìm thấy ref_id.");
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi định vị:", err);
                        document.querySelector('#latitude').value =
                            {{ old('latitude', $property->latitude) }};
                        document.querySelector('#longitude').value =
                            {{ old('longitude', $property->longitude) }};
                        // alert("Không tìm thấy vị trí với địa chỉ bạn nhập.");
                    });
            }


            // Cập nhật bản đồ khi load trang
            provinceSelect.addEventListener('change', function() {
                setTimeout(updateMapWithAddress, 500);
            });
            districtSelect.addEventListener('change', function() {
                setTimeout(updateMapWithAddress, 500);
            });
            wardSelect.addEventListener('change', function() {
                detailedAddressInput.value = ''; // Reset địa chỉ cụ thể khi đổi xã
                setTimeout(updateMapWithAddress, 800); 
                // updateMapWithAddress();
            });

            let debounceTimer;
            detailedAddressInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(updateMapWithAddress, 1500);
            });

            // Cập nhật ban đầu khi load trang
            if (oldDetailedAddress || oldProvince || oldDistrict || oldWard) {
                setTimeout(updateMapWithAddress, 1000);
            }
        });
    </script>
@endsection
