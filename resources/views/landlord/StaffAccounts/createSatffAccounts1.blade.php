@extends('landlord.layouts.app')

@section('title', 'Tạo Tài Khoản Nhân Viên')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header" style="background-color: #ff69b4; color: white;">
                <h5 class="mb-0 fw-bold">🌸 Tạo Tài Khoản Cho Nhân Viên</h5>
            </div>
            <div class="card-body">

                {{-- Thông báo thành công --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Thông báo lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('landlords.staff_accounts.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" value="{{ old('email') }}" required
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số CCCD</label>
                        <input type="text" name="identity_number" value="{{ old('identity_number') }}" required
                            class="form-control @error('identity_number') is-invalid @enderror">
                        @error('identity_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tải ảnh CCCD (để tự nhận số & tên)</label>
                        <input type="file" id="cccd_image" accept="image/*" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng Thái</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            @foreach ($statuses as $value)
                                <option value="{{ $value }}" {{ old('is_active') == $value ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">✅ Xác Nhận</button>
                        <a href="{{ route('landlords.staff_accounts.index') }}" class="btn btn-secondary ms-2">⬅ Quay
                            lại</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#cccd_image').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            if (!file.type.match('image.*')) {
                alert('❗ Vui lòng chọn file ảnh hợp lệ.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('❗ Ảnh quá lớn, vui lòng chọn ảnh < 5MB.');
                return;
            }

            var formData = new FormData();
            formData.append('cccd_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: @json(route('landlords.ocr.identity_number')),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.identity_number) {
                        $('input[name="identity_number"]').val(response.identity_number);
                        alert('🎉 Đã nhận diện số CCCD: ' + response.identity_number);
                    } else {
                        alert('❗ Không tìm thấy số CCCD. Vui lòng kiểm tra lại ảnh.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('🚫 Đã xảy ra lỗi khi xử lý OCR.');
                }
            });
        });
    </script>
@endsection
