@extends('landlord.layouts.app')
@section('title', 'Đăng ký bất động sản')

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
            <h3 class="card-title mb-0">Đăng ký bất động sản mới</h3>
            <a href="{{ route('landlords.properties.list') }}" class="btn btn-secondary btn-sm">← Danh sách</a>
        </div>
        <div class="card-body">
            <form id="propertyForm" method="POST" action="{{ route('landlords.properties.store') }}"
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
                                class="form-control @error('image_urls.*') is-invalid @enderror" required>
                            @error('image_urls.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="mainImagesPreview" class="d-flex flex-wrap"></div>
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
                                    placeholder="VD: Khu trọ Nguyễn Văn A" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea id="description" name="description" class="form-control" rows="3"
                                    placeholder="Giới thiệu chung về khu trọ...">{{ old('description') }}</textarea>
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
                                        <input type="hidden" id="rules" name="rules" value="{{ old('rules') }}"
                                            class="form-control @error('rules') is-invalid @enderror" required>
                                        <div id="quill-editor" style="height: 350px"></div>
                                        @error('rules')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div>
                            <!-- end col -->
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
                                    placeholder="Số nhà, đường..." value="{{ old('detailed_address') }}" required>
                                @error('detailed_address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Xác định vị trí trên bản đồ</label>
                                <div id="map" style="height: 350px; border: 1px solid #ccc;"></div>
                                {{-- <input id="autocompleteResults" class="form-control mt-2"
                                    style="display:none; position:absolute; z-index:9999;" />
                                <div id="autocomplete-list" class="list-group position-absolute w-50 bg-white border"
                                    style="z-index: 1000; display:none;"></div> --}}

                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Giấy tờ pháp lý -->
                    <div class="col-12">
                        <h5 class="mb-3 text-warning">3. Giấy tờ pháp lý</h5>
                        <div id="documentFields">
                            <div class="row g-3 document-row mb-3">
                                <div class="col-md-4">
                                    <select name="document_types[]" class="form-select" required>
                                        <option value="">Chọn loại giấy tờ</option>
                                        <option value="Giấy phép kinh doanh">Giấy phép kinh doanh</option>
                                        <option value="Giấy chứng nhận PCCC">Giấy chứng nhận PCCC</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="file" name="document_files[]" class="form-control"
                                        accept="image/*,application/pdf" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-document">Xóa</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addDocument" class="btn btn-primary mb-3">Thêm giấy tờ</button>
                        <div id="documentPreviews"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">📤 Gửi đăng ký</button>
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
    <!-- MapLibre GL -->
    <link href="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@2.4.0/dist/maplibre-gl.js"></script>


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
            mainImagesPreview.innerHTML = '';
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

        // Document previews
        // Document previews
function updateDocumentPreviews() {
    const documentPreviews = document.getElementById('documentPreviews');
    documentPreviews.innerHTML = '';
    const documentFiles = document.querySelectorAll('input[name="document_files[]"]');

    documentFiles.forEach((input, index) => {
        if (input.files.length > 0) {
            const file = input.files[0];
            const typeSelect = input.closest('.document-row').querySelector('select[name="document_types[]"]').value;
            const div = document.createElement('div');
            div.classList.add('document-preview');
            div.style.position = 'relative'; // Để căn chỉnh nút xóa

            // Kiểm tra loại file và tạo preview
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                div.appendChild(img);
            } else if (file.type === 'application/pdf') {
                const pdfIcon = document.createElement('span');
                pdfIcon.textContent = '📄 PDF: ' + file.name;
                pdfIcon.style.display = 'block';
                div.appendChild(pdfIcon);
            } else {
                div.textContent = `Hỗ trợ ảnh hoặc PDF chỉ. Tệp: ${file.name}`;
                div.appendChild(document.createElement('br'));
            }

            // Thêm thông tin loại giấy tờ
            const typeInfo = document.createElement('span');
            typeInfo.textContent = ` (Loại: ${typeSelect || 'Chưa chọn'})`;
            div.appendChild(typeInfo);

            // Nút xóa
            const removeBtn = document.createElement('span');
            removeBtn.classList.add('remove-btn');
            removeBtn.innerHTML = 'Xóa';
            removeBtn.setAttribute('data-index', index);
            removeBtn.style.position = 'absolute';
            removeBtn.style.top = '5px';
            removeBtn.style.right = '5px';
            removeBtn.style.background = 'rgba(255, 255, 255, 0.8)';
            removeBtn.style.padding = '2px 5px';
            removeBtn.style.borderRadius = '3px';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.color = 'red';
            div.appendChild(removeBtn);

            documentPreviews.appendChild(div);
        }
    });

    // Xử lý sự kiện xóa
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

        // Add new document field
        document.getElementById('addDocument').addEventListener('click', function() {
            const documentFields = document.getElementById('documentFields');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'document-row', 'mb-3');
            newRow.innerHTML = `
                <div class="col-md-4">
                    <select name="document_types[]" class="form-select" required>
                        <option value="">Chọn loại giấy tờ</option>
                        <option value="Giấy phép kinh doanh">Giấy phép kinh doanh</option>
                        <option value="Giấy chứng nhận PCCC">Giấy chứng nhận PCCC</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="file" name="document_files[]" class="form-control" accept="image/*,application/pdf" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-document">Xóa</button>
                </div>
            `;
            documentFields.appendChild(newRow);

            newRow.querySelector('input[type="file"]').addEventListener('change', updateDocumentPreviews);
            newRow.querySelector('.remove-document').addEventListener('click', function() {
                newRow.remove();
                updateDocumentPreviews();
            });
        });

        document.querySelectorAll('input[name="document_files[]"]').forEach(input => {
            input.addEventListener('change', updateDocumentPreviews);
        });

        document.querySelectorAll('.remove-document').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.document-row').remove();
                updateDocumentPreviews();
            });
        });

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

            const initialContent = @json(old('rules', ''));
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

                // ✅ LẤY TÂM BẢN ĐỒ HIỆN TẠI TRƯỚC KHI SUBMIT
                const pos = marker.getLatLng();
                document.getElementById('latitude').value = pos.lat;
                document.getElementById('longitude').value = pos.lng;


                const documentTypes = document.querySelectorAll('select[name="document_types[]"]');
                const documentFiles = document.querySelectorAll('input[name="document_files[]"]');
                let valid = true;

                documentTypes.forEach((select, index) => {
                    if (!select.value || !documentFiles[index].files.length) {
                        valid = false;
                        select.classList.add('is-invalid');
                        documentFiles[index].classList.add('is-invalid');
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

        var vietmapApiKey = "{{ config('services.viet_map.key') }}"; // hoặc hardcode key khi test
        // Map and address
        document.addEventListener('DOMContentLoaded', async function() {
            let userIsEditingAddress = false;
            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');
            let oldProvince = '{{ old('province') }}';
            let oldDistrict = '{{ old('district') }}';
            let oldWard = '{{ old('ward') }}';

            const addressInput = document.querySelector('#detailed_address');
            const suggestionBox = document.createElement('div');
            suggestionBox.classList.add('autocomplete-list');
            suggestionBox.id = 'autocomplete-suggestions';
            addressInput.parentNode.appendChild(suggestionBox);
            // const vietmapApiKey = "{{ config('services.viet_map.key') }}"; // Lấy từ config

            addressInput.addEventListener('input', async function() {
                if (userIsEditingAddress)
                    return; // Không hiển thị gợi ý khi người dùng đang chỉnh sửa
                suggestionBox.innerHTML = '';
                suggestionBox.style.display = 'none';
                // const vietmapApiKey = "{{ config('services.viet_map.key') }}"; // Lấy từ config
                const query = this.value.trim();
                if (!query) return;

                const res = await fetch(
                    `https://maps.vietmap.vn/api/search/v3?apikey=${vietmapApiKey}&text=${encodeURIComponent(query)}`
                );
                const data = await res.json();

                suggestionBox.innerHTML = '';
                suggestionBox.style.display = 'block';

                data.slice(0, 5).forEach(suggest => {
                    const item = document.createElement('div');
                    item.classList.add('list-group-item', 'list-group-item-action');
                    // Cắt phần chi tiết từ display_name trước dấu phẩy đầu tiên
                    let detailOnly = suggest.display.split(',')[0];
                    item.textContent = detailOnly;

                    item.addEventListener('click', () => {
                        addressInput.value = detailOnly;
                        userIsEditingAddress = false;
                        marker.setLatLng([suggest.lat, suggest.lon]);
                        map.setView([suggest.lat, suggest.lon], 16);
                        document.getElementById('latitude').value = suggest.lat;
                        document.getElementById('longitude').value = suggest.lon;
                        suggestionBox.style.display = 'none';
                    });
                    suggestionBox.appendChild(item);
                });
            });

            document.addEventListener('click', () => {
                suggestionBox.style.display = 'none';
            });


            try {
                const provinces = await fetch("https://provinces.open-api.vn/api/p/").then(res => {
                    if (!res.ok) throw new Error('Lỗi tải tỉnh');
                    return res.json();
                });
                provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh --</option>';
                provinces.forEach(province => {
                    const option = new Option(province.name, province.code);
                    if (province.code == oldProvince) option.selected = true;
                    provinceSelect.add(option);
                });

                provinceSelect.addEventListener('change', async function() {
                    const provinceCode = this.value;
                    districtSelect.innerHTML = '<option value="">-- Chọn huyện --</option>';
                    wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';
                    districtSelect.disabled = true;
                    wardSelect.disabled = true;

                    if (provinceCode) {
                        try {
                            const districts = await fetch(
                                    `https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                                .then(res => {
                                    if (!res.ok) throw new Error('Lỗi tải huyện');
                                    return res.json();
                                });
                            districts.districts.forEach(d => {
                                const option = new Option(d.name, d.code);
                                if (d.code == oldDistrict) option.selected = true;
                                districtSelect.add(option);
                            });
                            districtSelect.disabled = false;
                            updateMapWithAddress();
                        } catch (error) {
                            console.error(error);
                            alert('Lỗi tải danh sách huyện.');
                        }
                    }
                });

                districtSelect.addEventListener('change', async function() {
                    const districtCode = this.value;
                    wardSelect.innerHTML = '<option value="">-- Chọn xã --</option>';
                    wardSelect.disabled = true;

                    if (districtCode) {
                        try {
                            const wards = await fetch(
                                    `https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                                .then(res => {
                                    if (!res.ok) throw new Error('Lỗi tải xã');
                                    return res.json();
                                });
                            wards.wards.forEach(w => {
                                const option = new Option(w.name, w.code);
                                if (w.code == oldWard) option.selected = true;
                                wardSelect.add(option);
                            });
                            wardSelect.disabled = false;
                            updateMapWithAddress();
                        } catch (error) {
                            console.error(error);
                            alert('Lỗi tải danh sách xã.');
                        }
                    }
                });

                wardSelect.addEventListener('change', updateMapWithAddress);

                if (oldProvince) {

                    await provinceSelect.dispatchEvent(new Event('change'));
                }
                if (oldDistrict) {
                    await districtSelect.dispatchEvent(new Event('change'));
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi tải danh sách tỉnh. Vui lòng thử lại sau.');
            }

            let map = L.map('map').setView([21.028511, 105.804817], 13);
            // Tạo marker có thể kéo
            let marker = L.marker([21.028511, 105.804817], {
                draggable: true
            }).addTo(map);

            // Khi kéo mũi tên, cập nhật địa chỉ hành chính
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                map.setView(pos);
                reverseGeocodeAndUpdateAddress(pos.lat, pos.lng);
            });

            // Khi người dùng zoom/di chuyển map → tự động reverse
            map.on('moveend', function() {
                const pos = marker.getLatLng();
                marker.setLatLng(pos);
                reverseGeocodeAndUpdateAddress(pos.lat, pos.lng);
            });

            // Hàm reverse geocoding
            async function reverseGeocodeAndUpdateAddress(lat, lon) {
                try {
                    const res = await fetch(
                        `https://maps.vietmap.vn/api/reverse/v3?apikey=${vietmapApiKey}&point.lat=${lat}&point.lng=${lon}`
                    );
                    const data = await res.json();
                    if (!data || !data.address) return;

                    const addr = data.address;

                    // Địa chỉ chi tiết
                    if (!userIsEditingAddress || !document.querySelector('#detailed_address').value
                        .trim()) {
                        document.querySelector('#detailed_address').value = addr.road || addr
                            .display_name || '';
                    }

                    const provinceText = addr.state;
                    const districtText = addr.county;
                    const wardText = addr.suburb || addr.village;

                    // --- Cập nhật tỉnh ---
                    let provinceMatched = [...provinceSelect.options].find(opt => provinceText && opt.text
                        .includes(provinceText.trim()));
                    if (!provinceMatched) return;

                    provinceSelect.value = provinceMatched.value;
                    await provinceSelect.dispatchEvent(new Event('change'));

                    // --- Đợi huyện tải về rồi mới gán huyện ---
                    const waitForDistrictOptions = () => new Promise(resolve => {
                        const interval = setInterval(() => {
                            if (districtSelect.options.length > 1) {
                                clearInterval(interval);
                                resolve();
                            }
                        }, 100);
                    });
                    await waitForDistrictOptions();

                    let districtMatched = [...districtSelect.options].find(opt => districtText && opt.text
                        .includes(districtText.trim()));
                    if (districtMatched) {
                        provinceSelect.value = provinceMatched.value;

                        // ✅ GÁN LẠI GIÁ TRỊ "OLD" để hệ thống hiểu là bạn đã chọn lại
                        // ⚠️ PHẢI khai báo let thay vì const ở phía trên!
                        oldProvince = provinceMatched.value;
                        oldDistrict = '';
                        oldWard = '';

                        await provinceSelect.dispatchEvent(new Event('change'));

                    }

                    // --- Đợi xã tải về rồi mới gán xã ---
                    const waitForWardOptions = () => new Promise(resolve => {
                        const interval = setInterval(() => {
                            if (wardSelect.options.length > 1) {
                                clearInterval(interval);
                                resolve();
                            }
                        }, 100);
                    });
                    await waitForWardOptions();

                    let wardMatched = [...wardSelect.options].find(opt => wardText && opt.text.includes(
                        wardText.trim()));
                    if (wardMatched) {
                        wardSelect.value = wardMatched.value;
                    }

                    // Gán lat lon
                    document.querySelector('#latitude').value = lat;
                    document.querySelector('#longitude').value = lon;
                } catch (error) {
                    console.error('Reverse geocoding error:', error);
                }
            }
            L.tileLayer(`https://maps.vietmap.vn/api/tm/{z}/{x}/{y}.png?apikey=${vietmapApiKey}`, {
                maxZoom: 18,
                attribution: '&copy; <a href="https://www.vietmap.vn/">VietMap</a>'
            }).addTo(map);
            function updateMapWithAddress() {
                let detail = document.querySelector('#detailed_address').value.trim();
                let provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text;
                let districtText = districtSelect.options[districtSelect.selectedIndex]?.text;
                let wardText = wardSelect.options[wardSelect.selectedIndex]?.text;

                if (
                    !provinceText || provinceText.includes('Chọn') ||
                    !districtText || districtText.includes('Chọn') ||
                    !wardText || wardText.includes('Chọn')
                ) return;

                let parts = [];
                if (detail) parts.push(detail);
                if (wardText) parts.push(wardText);
                if (districtText) parts.push(districtText);
                if (provinceText) parts.push(provinceText);
                parts.push("Việt Nam");

                let fullAddress = parts.join(', ');

                if (fullAddress.length < 10) return;

                // Gọi search API
                fetch(
                        `https://maps.vietmap.vn/api/search/v3?apikey=${vietmapApiKey}&text=${encodeURIComponent(fullAddress)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) throw new Error("Không tìm thấy kết quả");

                        const refId = data[0].ref_id;

                        // Gọi tiếp place API để lấy tọa độ
                        return fetch(
                            `https://maps.vietmap.vn/api/place/v3?apikey=${vietmapApiKey}&refid=${encodeURIComponent(refId)}`
                            );
                    })
                    .then(res => res.json())
                    .then(place => {
                        const lat = parseFloat(place.lat);
                        const lon = parseFloat(place.lng);

                        if (marker) {
                            marker.setLatLng([lat, lon]);
                        } else {
                            marker = L.marker([lat, lon], {
                                draggable: true
                            }).addTo(map);
                        }

                        map.setView([lat, lon], 16);
                        document.querySelector('#latitude').value = lat;
                        document.querySelector('#longitude').value = lon;
                    })
                    .catch(err => {
                        console.error("Lỗi khi định vị:", err);
                        alert("Không thể định vị địa chỉ bạn nhập.");
                        document.querySelector('#latitude').value = 21.028511;
                        document.querySelector('#longitude').value = 105.804817;
                    });
            }



            let debounceTimer;
            document.querySelector('#detailed_address').addEventListener('input', function() {
                userIsEditingAddress = true;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    updateMapWithAddress(); // Đã có sẵn rồi
                }, 2500); // giảm xuống cho mượt hơn
            });

        });
    </script>
@endsection
