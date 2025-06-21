@extends('profile.admin.layouts.app')

@section('title', 'Chỉnh sửa Profile Admin')

@section('content')
    <div class="container-fluid">

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Cover --}}
        <div class="position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg profile-setting-img">
                <img src="{{ asset('assets/admin/images/profile-bg.jpg') }}" class="profile-wid-img" alt="Profile Background">
            </div>
        </div>

        <div class="row">
            {{-- Left: Avatar --}}
            <div class="col-xxl-3">
                <div class="card mt-n5">
                    <div class="card-body text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                            @if (!empty($profile->avatar))
                                <img src="{{ asset('storage/' . $profile->avatar) }}"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="Avatar">
                            @else
                                <img src="{{ asset('assets/admin/images/users/avatar-1.jpg') }}"
                                    class="rounded-circle avatar-xl img-thumbnail user-profile-image shadow" alt="Avatar">
                            @endif
                        </div>
                        <h5 class="fs-16 mb-1">{{ $profile->full_name ?? 'Admin' }}</h5>
                        <p class="text-muted mb-0">{{ auth()->user()->role ?? 'Chưa cập nhật chức vụ' }}</p>
                    </div>
                </div>
            </div>

            {{-- Right: Form chỉnh sửa --}}
            <div class="col-xxl-9">
                <div class="card mt-xxl-n5">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#profileDetails" role="tab">
                                    <i class="fas fa-user-circle"></i> Thông tin cá nhân
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content">
                            <div class="tab-pane active" id="profileDetails" role="tabpanel">
                                <form action="{{ route('admin.profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- Nếu cần profile_id để xác định update --}}
                                    <input type="hidden" name="profile_id" value="{{ $profile->id ?? '' }}">

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="full_name" class="form-label">Họ và tên</label>
                                                <input type="text"
                                                    class="form-control @error('full_name') is-invalid @enderror"
                                                    id="full_name" name="full_name" required
                                                    value="{{ old('full_name', default: $profile->full_name ?? '') }}">
                                                @error('full_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="position" class="form-label">Chức vụ</label>
                                                <input type="text"
                                                    class="form-control @error('position') is-invalid @enderror"
                                                    id="position" name="position"
                                                    value="{{ old('position', auth()->user()->role ?? '') }} " disabled>
                                                @error('position')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Địa chỉ</label>
                                                <input type="text"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    id="address" name="address" required
                                                    value="{{ old('address', $profile->address ?? '') }}">
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Số điện thoại</label>
                                                <input type="text"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone" name="phone" required
                                                    value="{{ old('phone', $profile->phone ?? '') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" required
                                                    value="{{ old('email', auth()->user()->email ?? '') }}" readonly>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Email không thể thay đổi</small>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label for="avatar" class="form-label">Ảnh đại diện</label>
                                                <input type="file"
                                                    class="form-control @error('avatar') is-invalid @enderror"
                                                    id="avatar" name="avatar" accept="image/*">
                                                @error('avatar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                                        </div>
                                    </div>
                                </form>
                            </div> {{-- tab-pane --}}
                        </div> {{-- tab-content --}}
                    </div> {{-- card-body --}}
                </div> {{-- card --}}
            </div> {{-- col-xxl-9 --}}
        </div> {{-- row --}}

    </div>
@endsection
