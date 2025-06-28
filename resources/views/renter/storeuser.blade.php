<form action="{{ route('renter.storeuser') }}" method="POST" class="p-4 shadow rounded bg-white">
    @csrf

    <h5 class="mb-4 fw-bold text-primary">📝 Thêm người vào phòng</h5>

    {{-- Họ tên --}}
    <div class="mb-3">
        <label for="cccd" class="form-label fw-semibold">Họ và Tên</label>
        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Nhập đầy đủ họ tên" required>
    </div>
    {{-- CCCD --}}
    <div class="mb-3">
        <label for="cccd" class="form-label fw-semibold">📇 Số CCCD</label>
        <input type="text" id="cccd" name="cccd" class="form-control" placeholder="Nhập số CCCD" required>
    </div>

    {{-- Số điện thoại --}}
    <div class="mb-3">
        <label for="phone" class="form-label fw-semibold">📱 Số điện thoại</label>
        <input type="text" id="phone" name="phone" class="form-control" placeholder="Nhập số điện thoại" required>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">📧 Email</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email" required>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-success">
            ✅ Gửi yêu cầu thêm người
        </button>
    </div>
</form>
