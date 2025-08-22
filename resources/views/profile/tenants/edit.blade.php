@extends('profile.tenants.layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
    <div class="container py-4 py-md-5">
        <div class="row g-4">
            {{-- Sidebar --}}
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card shadow-sm rounded-3 border-0">
                    <div class="card-body text-center p-4">
                        <form action="{{ route('profile.update.avatar') }}" method="POST" enctype="multipart/form-data"
                            id="avatar-form">
                            @csrf
                            @method('PUT')
                            <label for="avatar-input" class="avatar-label">
                                <img id="avatar-preview"
                                    src="{{ $profile['avatar'] ?? asset('assets/client/img/user-3.jpg') }}"
                                    class="rounded-circle border border-2 border-primary shadow-sm avatar-img"
                                    alt="Avatar">
                            </label>
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" hidden>
                            <h5 class="fw-semibold text-primary mt-3 mb-1">{{ $profile['full_name'] ?? 'Tên người dùng' }}
                            </h5>
                            <p class="text-muted small mb-0">{{ $profile['email'] ?? 'Chưa có email' }}</p>
                            @error('avatar')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                    <ul class="list-group list-group-flush">

                        <li>
                            <a href="{{ route('tenant.profile.edit') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('tenant.profile') ? 'active' : '' }}">
                                <i class="bi bi-person-fill fs-5"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-key-fill fs-5"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử thanh toán
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home.complaints.index') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử khiếu nại
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home.profile.tenants.dashboard') }}"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                                <i class="bi bi-receipt-cutoff fs-5"></i> Thống kê chi tiết
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('tenant.profile.edit') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2 {{ request()->routeIs('tenant.profile') ? 'active bg-primary text-white fw-bold' : '' }}"
                    style="border-radius: 0 0.375rem 0.375rem 0;">
                    <i class="bi bi-person-fill fs-5"></i> Thông tin cá nhân
                </a>
                <a href="{{ route('password.change') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-key-fill fs-5"></i> Đổi mật khẩu
                </a>
                <a href="{{ route('home.profile.tenants.history') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử thanh toán các hóa đơn
                </a>
                <a href="{{ route('home.complaints.index') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-5"></i> Lịch sử khiếu nại
                </a>
                <a href="{{ route('home.profile.tenants.dashboard') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-5"></i> Thống kê chi tiết
                </a>
                <a href="{{ route('home.roomleave.deposits') }}"
                    class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff fs-5"></i> Cọc tiền nhà
                </a>
                </ul>
            </div>

            <hr class="my-2">



        </div>

        {{-- Main Content --}}
        <div class="col-lg-9 col-md-8">
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-primary fw-bold border-start border-4 border-primary ps-3">Cập nhật thông tin
                        cá nhân</h4>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    value="{{ old('full_name', $profile['full_name'] ?? '') }}" required
                                    placeholder="Nhập họ và tên">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Số điện thoại <span
                                        class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="phone"
                                    class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $profile['phone'] ?? '') }}" required
                                    placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="cccd" class="form-label fw-semibold">Số CMND/CCCD <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="cccd_display" class="form-control form-control-lg"
                                    value="{{ $profile['cccd'] ? substr($profile['cccd'], 0, 3) . 'XXXXXXXX' : '' }}"
                                    readonly disabled>
                                <input type="hidden" name="cccd" id="cccd"
                                    value="{{ old('cccd', $profile['cccd'] ?? '') }}">
                                @error('cccd')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    value="{{ old('email', $profile['email'] ?? '') }}" required
                                    placeholder="Nhập email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="created_at" class="form-label fw-semibold">Ngày tạo</label>
                                <input type="text" id="created_at" class="form-control form-control-lg"
                                    value="{{ old('created_at', isset($profile['created_at']) && $profile['created_at'] ? \Carbon\Carbon::parse($profile['created_at'])->format('d/m/Y H:i:s') : '') }}"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="days_stayed" class="form-label fw-semibold">Số ngày đã ở</label>
                                <input type="text" id="days_stayed" class="form-control form-control-lg"
                                    value="{{ $profile['days_stayed'] ?? 0 }} ngày" readonly disabled>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient-primary px-4 py-2 fw-semibold">
                            <i class="bi bi-save2 me-2"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        .avatar-label {
            display: inline-block;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .avatar-label:hover .avatar-img {
            transform: scale(1.05);
        }

        .btn-gradient-primary {
            background: linear-gradient(45deg, #007bff, #6610f2);
            color: white;
            border: none;
            border-radius: 0.5rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, #6610f2, #007bff);
            transform: translateY(-2px);
        }

        .list-group-item-action {
            transition: background-color 0.2s ease;
        }

        .list-group-item-action.active {
            background-color: #6610f2;
            color: white;
            border-color: #6610f2;
        }

        .list-group-item-action:hover:not(.active) {
            background-color: #f8f9fa;
        }

        @media (max-width: 767px) {
            .avatar-img {
                width: 80px;
                height: 80px;
            }

            .card-body {
                padding: 1.5rem;
            }
        }
    </style>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('avatar-form').submit();
            }
        });
    </script>
@endsection
