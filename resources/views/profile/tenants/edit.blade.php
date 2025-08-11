@extends('profile.tenants.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
    <div class="container py-5">
        <div class="row gx-4">
            {{-- Sidebar --}}
            <div class="col-md-3 mb-4">
                <div class="card shadow rounded-4 border-0">
                    <div class="card-body text-center">
                        <form action="{{ route('profile.update.avatar') }}" method="POST" enctype="multipart/form-data"
                            id="avatar-form">
                            @csrf
                            @method('PUT')
                            <label for="avatar-input" class="d-inline-block mb-3" style="cursor: pointer;">
                                <img id="avatar-preview"
                                    src="{{ $tenant->avatar ? asset('storage/' . $tenant->avatar) : asset('assets/client/img/user-3.jpg') }}"
                                    class="rounded-circle border border-3 border-primary shadow-sm"
                                    style="width: 110px; height: 110px; object-fit: cover; transition: transform 0.3s ease;"
                                    alt="Avatar" onmouseover="this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.transform='scale(1)'">
                            </label>
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display:none;"
                                onchange="submitAvatarForm()">

                            <h5 class="fw-semibold text-primary mb-1">{{ $tenant->full_name ?? 'Tên người dùng' }}</h5>
                            <p class="text-muted small">{{ Auth::user()->email }}</p>
                            @error('avatar')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                    <hr class="my-2">
                    <ul class="list-group list-group-flush">
                        <a href="{{ route('tenant.profile.edit') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('tenant.profile') ? 'active bg-primary text-white fw-bold' : '' }}"
                            style="border-radius: 0 0.375rem 0.375rem 0;">
                            <i class="bi bi-person-fill fs-5"></i> Thông tin cá nhân
                        </a>
                        <a href="{{ route('password.change') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-key-fill fs-5"></i> Đổi mật khẩu
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử thanh toán
                        </a>
                        <a href="{{ route('home.complaints.index') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử khiếu nại
                        </a>
                        <a href="{{ route('home.profile.tenants.dashboard') }}"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                            <i class="bi bi-receipt-cutoff fs-5"></i> Thống kê chi tiết
                        </a>
                    </ul>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-md-9">
                <div class="card shadow rounded-4 border-0">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-primary fw-bold border-start border-4 border-primary ps-3">Cập nhật thông tin
                            cá nhân</h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('tenant.profile.update') }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label fw-semibold">Họ và tên <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="full_name" id="full_name"
                                        class="form-control form-control-lg @error('full_name') is-invalid @enderror"
                                        value="{{ old('full_name', $tenant->full_name) }}" required
                                        placeholder="Nhập họ và tên">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label fw-semibold">Số điện thoại <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" name="phone_number" id="phone_number"
                                        class="form-control form-control-lg @error('phone_number') is-invalid @enderror"
                                        value="{{ old('phone_number', $tenant->phone_number) }}" required
                                        placeholder="Nhập số điện thoại">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="identity_number" class="form-label fw-semibold">Số CMND/CCCD <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="identity_number" id="identity_number"
                                    class="form-control form-control-lg @error('identity_number') is-invalid @enderror"
                                    value="{{ old('identity_number', $tenant->identity_number) }}" required
                                    placeholder="Nhập số CMND hoặc CCCD">
                                @error('identity_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="dob" class="form-label fw-semibold">Ngày sinh</label>
                                    <input type="date" name="dob" id="dob"
                                        class="form-control form-control-lg @error('dob') is-invalid @enderror"
                                        value="{{ old('dob', $tenant->dob ? \Carbon\Carbon::parse($tenant->dob)->format('Y-m-d') : '') }}">
                                    @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label fw-semibold">Giới tính <span
                                            class="text-danger">*</span></label>
                                    <select name="gender" id="gender"
                                        class="form-select form-select-lg @error('gender') is-invalid @enderror" required>
                                        <option value="" disabled
                                            {{ old('gender', $tenant->gender) ? '' : 'selected' }}>-- Chọn giới tính --
                                        </option>
                                        <option value="male"
                                            {{ old('gender', $tenant->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female"
                                            {{ old('gender', $tenant->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other"
                                            {{ old('gender', $tenant->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                <input type="text" name="address" id="address"
                                    class="form-control form-control-lg @error('address') is-invalid @enderror"
                                    value="{{ old('address', $tenant->address) }}" placeholder="Nhập địa chỉ hiện tại">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="job" class="form-label fw-semibold">Nghề nghiệp</label>
                                <input type="text" name="job" id="job"
                                    class="form-control form-control-lg @error('job') is-invalid @enderror"
                                    value="{{ old('job', $tenant->job) }}" placeholder="Nhập nghề nghiệp">
                                @error('job')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-gradient-primary px-5 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-save2 me-2"></i> Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom Styles --}}
    <style>
        .btn-gradient-primary {
            background: linear-gradient(45deg, #007bff, #6610f2);
            color: #fff;
            border: none;
            transition: background 0.3s ease;
            border-radius: 50px;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, #6610f2, #007bff);
            color: #fff;
        }

        .list-group-item-action.active {
            background-color: #6610f2 !important;
            border-color: #6610f2 !important;
        }
    </style>

    {{-- JS: Avatar auto-submit --}}
    <script>
        function submitAvatarForm() {
            const input = document.getElementById('avatar-input');
            if (input.files.length > 0) {
                document.getElementById('avatar-form').submit();
            }
        }
    </script>
@endsection
