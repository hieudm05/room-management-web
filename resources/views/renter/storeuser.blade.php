@extends('home.layouts.app')

@section('title', 'Thêm người ở ghép')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-bold text-primary text-center">📝 Thêm người vào phòng</h5>

                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('renter.storeuser') }}" method="POST" id="addUserForm" enctype="multipart/form-data">
                        @csrf

                        <div id="member-list">
                            <div class="member border rounded p-3 mb-3 bg-light">
                                <div class="form-group">
                                    <label for="rental_id_display">Mã hợp đồng</label>
                                    <input type="text" class="form-control" id="rental_id_display"
                                        value="{{ $rental?->rental_id ?? 'Không có hợp đồng' }}" readonly>
                                </div>
                                <input type="hidden" name="rental_id" value="{{ $rentalId }}">

                                {{-- Upload ảnh CCCD --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">📄 Ảnh CCCD</label>
                                    <input type="file" name="cccd_image[]" class="form-control cccd-upload" accept="image/*" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Họ và Tên</label>
                                    <input type="text" name="full_name[]" class="form-control" placeholder="Nhập đầy đủ họ tên" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">📇 Số CCCD</label>
                                    <input type="text" name="cccd[]" class="form-control" placeholder="Nhập số CCCD" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">📱 Số điện thoại</label>
<input type="text" name="phone[]" class="form-control" placeholder="Nhập số điện thoại" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">📧 Email</label>
                                    <input type="email" name="email[]" class="form-control" placeholder="Nhập email" required>
                                </div>

                                <button type="button" class="btn btn-danger btn-sm remove-member d-none">❌ Xóa người này</button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" id="add-member" class="btn btn-outline-primary">
                                ➕ Thêm người vào phòng
                            </button>
                            <button type="submit" class="btn btn-success">
                                ✅ Gửi yêu cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberList = document.getElementById('member-list');
    const addBtn = document.getElementById('add-member');

    if (!memberList || !addBtn) {
        console.error('Không tìm thấy #member-list hoặc #add-member');
        return;
    }

    // Clone member
    addBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const firstMember = memberList.querySelector('.member');
        if (!firstMember) return;

        const newMember = firstMember.cloneNode(true);
        newMember.querySelectorAll('input').forEach(input => {
            if(input.type !== 'hidden') input.value = '';
        });
        newMember.querySelector('.remove-member').classList.remove('d-none');

        memberList.appendChild(newMember);
    });

    // Xóa member
    memberList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-member')) {
            e.target.closest('.member').remove();
        }
    });

    // Upload ảnh CCCD -> tự fill form
    memberList.addEventListener('change', async function(e) {
        if (!e.target.classList.contains('cccd-upload')) return;

        const fileInput = e.target;
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('cccd_image', file);

        try {
            const res = await fetch('{{ route("renter.parseCCCD") }}', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: formData
            });
            const data = await res.json();

            if (data.success) {
const memberDiv = fileInput.closest('.member');
                memberDiv.querySelector('input[name="full_name[]"]').value = data.full_name ?? '';
                memberDiv.querySelector('input[name="cccd[]"]').value = data.cccd ?? '';
            } else {
                alert('Không đọc được thông tin CCCD. Vui lòng thử lại.');
            }
        } catch (err) {
            console.error(err);
            alert('Lỗi khi gửi ảnh lên server.');
        }
    });
});
</script>
@endsection