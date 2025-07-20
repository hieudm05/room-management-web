@extends('home.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="h4 fw-semibold text-center mb-4">📨 Gửi khiếu nại</h2>

            {{-- Thông báo lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form gửi --}}
            <form action="{{ route('home.complaints.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Tòa --}}
                <div class="mb-3">
                    <label class="form-label">Tòa đang thuê:</label>
                    <input type="text" class="form-control bg-light" value="{{ $property->name }}" readonly>
                    <input type="hidden" name="property_id" value="{{ $property->property_id }}">
                </div>

                {{-- Phòng --}}
                <div class="mb-3">
                    <label class="form-label">Phòng:</label>
                    <input type="text" class="form-control bg-light" value="{{ $room->room_number }}" readonly>
                    <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                </div>

                {{-- Họ tên --}}
                <div class="mb-3">
                    <label class="form-label">Họ và tên:</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                </div>

                {{-- SĐT --}}
                <div class="mb-3">
                    <label class="form-label">Số điện thoại:</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                {{-- Vấn đề chung --}}
                <div class="mb-3">
                    <label class="form-label">Loại vấn đề:</label>
                    <select name="common_issue_id" class="form-select" required>
                        <option value="">-- Chọn vấn đề --</option>
                        @foreach ($commonIssues as $issue)
                            <option value="{{ $issue->id }}" {{ old('common_issue_id') == $issue->id ? 'selected' : '' }}>
                                {{ $issue->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mô tả chi tiết --}}
                <div class="mb-3">
                    <label class="form-label">Mô tả chi tiết:</label>
                    <textarea name="detail" rows="4" class="form-control" placeholder="Nêu rõ nội dung bạn muốn phản ánh...">{{ old('detail') }}</textarea>
                </div>

                {{-- Ảnh đính kèm --}}
                <div class="mb-3">
                    <label class="form-label">Ảnh đính kèm (nếu có):</label>
                  <input type="file" name="photos[]" multiple accept="image/*" class="form-control" width="200px" height="200px">
                    <div class="form-text">Bạn có thể chọn nhiều ảnh cùng lúc. Tối đa 5MB mỗi ảnh.</div>
                </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">
                        📩 Gửi khiếu nại
                    </button>
                </div>
            </form>
        </div>
      

    </div>
</div>
@endsection
