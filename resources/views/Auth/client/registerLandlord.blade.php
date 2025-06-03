@extends('landlord.layouts.app')

@section('title', 'Đăng ký làm chủ trọ')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">📝 Đăng ký làm chủ trọ</h2>

    @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div id="loading" style="display: none;" class="text-center">Đang tải...</div>
    <div class="card">
        <div class="card-body">
            <form id="registerLandlordForm" action="{{ route('landlords.register.submit') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- input ẩn để chứa địa chỉ đầy đủ -->
                <input type="hidden" name="address" id="address">

                <!-- Pass old values via data attributes -->
                <div id="address-data"
                    data-province="{{ old('province_code', '') }}"
                    data-district="{{ old('district_code', '') }}"
                    data-ward="{{ old('ward_code', '') }}"
                    class="d-none"></div>

                <h4 class="mt-4">📍 Địa chỉ</h4>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="province_code" id="province_code" class="form-select" required>
                                <option value="">-- Chọn Tỉnh/Thành --</option>
                            </select>
                            <label for="province_code">Tỉnh/Thành</label>
                            @error('province_code')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="district_code" id="district_code" class="form-select" required>
                                <option value="">-- Chọn Quận/Huyện --</option>
                            </select>
                            <label for="district_code">Quận/Huyện</label>
                            @error('district_code')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="ward_code" id="ward_code" class="form-select" required>
                                <option value="">-- Chọn Phường/Xã --</option>
                            </select>
                            <label for="ward_code">Phường/Xã</label>
                            @error('ward_code')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-floating">
                            <input type="text" name="full_address" id="full_address" class="form-control" value="{{ old('full_address') }}" required>
                            <label for="full_address">Số nhà, đường (Chi tiết)</label>
                            @error('full_address')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <h4 class="mt-4">🪪 Căn cước công dân (2 mặt)</h4>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="file" name="cccd_front" id="cccd_front" class="form-control" accept="image/*" required>
                            <label for="cccd_front">Ảnh mặt trước CCCD</label>
                            @error('cccd_front')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="file" name="cccd_back" id="cccd_back" class="form-control" accept="image/*" required>
                            <label for="cccd_back">Ảnh mặt sau CCCD</label>
                            @error('cccd_back')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">✅ Xác nhận đăng ký</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province_code');
        const districtSelect = document.getElementById('district_code');
        const wardSelect = document.getElementById('ward_code');
        const addressData = document.getElementById('address-data');
        const loading = document.getElementById('loading');
        const form = document.getElementById('registerLandlordForm');

        const oldProvince = addressData.dataset.province;
        const oldDistrict = addressData.dataset.district;
        const oldWard = addressData.dataset.ward;

        function showLoading() {
            loading.style.display = 'block';
        }

        function hideLoading() {
            loading.style.display = 'none';
        }

        function loadProvinces() {
            showLoading();
            fetch('/provinces', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                    if (Array.isArray(data)) {
                        data.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.code;
                            option.text = province.name;
                            if (province.code == oldProvince) option.selected = true;
                            provinceSelect.appendChild(option);
                        });
                    }
                    hideLoading();
                    if (oldProvince) loadDistricts(oldProvince);
                })
                .catch(() => {
                    alert('Không thể tải danh sách tỉnh/thành phố.');
                    hideLoading();
                });
        }

        function loadDistricts(provinceCode) {
            showLoading();
            fetch(`/districts/${provinceCode}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                    if (Array.isArray(data)) {
                        data.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.code;
                            option.text = district.name;
                            if (district.code == oldDistrict) option.selected = true;
                            districtSelect.appendChild(option);
                        });
                    }
                    hideLoading();
                    if (oldDistrict) loadWards(oldDistrict);
                })
                .catch(() => hideLoading());
        }

        function loadWards(districtCode) {
            showLoading();
            fetch(`/wards/${districtCode}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.ok ? response.json() : Promise.reject(response))
                .then(data => {
                    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                    if (Array.isArray(data)) {
                        data.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.code;
                            option.text = ward.name;
                            if (ward.code == oldWard) option.selected = true;
                            wardSelect.appendChild(option);
                        });
                    }
                    hideLoading();
                })
                .catch(() => hideLoading());
        }

        loadProvinces();

        provinceSelect.addEventListener('change', function() {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            if (this.value) loadDistricts(this.value);
        });

        districtSelect.addEventListener('change', function() {
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            if (this.value) loadWards(this.value);
        });

        // Thêm xử lý trước submit form để tổng hợp địa chỉ
        form.addEventListener('submit', function(e) {
            const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
            const districtText = districtSelect.options[districtSelect.selectedIndex]?.text || '';
            const wardText = wardSelect.options[wardSelect.selectedIndex]?.text || '';
            const fullAddress = document.getElementById('full_address').value || '';

            // Nối chuỗi địa chỉ: Số nhà, đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố
            const fullAddressString = `${fullAddress}, ${wardText}, ${districtText}, ${provinceText}`;
            console.log('Full address:', fullAddressString);
            // Gán vào input ẩn
            document.getElementById('address').value = fullAddressString;
        });
    });
</script>
@endsection
