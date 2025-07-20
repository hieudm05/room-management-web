@extends('landlord.layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-danger">🔧 Xử lý khiếu nại #{{ $complaint->id }}</h2>

    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Thông tin khiếu nại</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Tòa:</strong> {{ $complaint->property->name ?? '---' }}</p>
                    <p><strong>Phòng:</strong> {{ $complaint->room->room_number ?? '---' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Người gửi:</strong> {{ $complaint->full_name }}</p>
                    <p><strong>SĐT:</strong> {{ $complaint->phone }}</p>
                </div>
                <div class="col-12 mt-2">
                    <p><strong>Tiêu đề:</strong> {{ $complaint->title }}</p>
                    <p><strong>Nội dung:</strong> {{ $complaint->detail }}</p>
                </div>
            </div>

            @if ($complaint->photos && $complaint->photos->count())
                <div class="mb-3">
                    <strong>📎 Ảnh kèm theo:</strong>
                    <div class="row mt-2">
                        @foreach ($complaint->photos as $photo)
                            <div class="col-md-3 col-sm-4 col-6 mb-3">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                     class="img-fluid rounded border"
                                     alt="Ảnh khiếu nại">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Form xử lý --}}
    <form action="{{ route('landlord.staff.complaints.resolve', $complaint->id) }}"
          method="POST"
          enctype="multipart/form-data"
          class="card">
        @csrf
        <div class="card-header bg-light fw-bold">Thông tin xử lý</div>
        <div class="card-body">

            <div class="mb-3">
                <label for="user_cost" class="form-label">💰 Chi phí người thuê chịu (VNĐ):</label>
                <input type="number" name="user_cost" id="user_cost"
                  class="form-control" min="0" step="1000"
                 value="{{ old('user_cost') }}"
                   placeholder="Không nhập nếu không tính phí">
            </div>

            <div class="mb-3">
                <label for="landlord_cost" class="form-label">💼 Chi phí chủ trọ chịu (VNĐ):</label>
               <input type="number" name="landlord_cost" id="landlord_cost"
                 class="form-control" min="0" step="1000"
                 value="{{ old('landlord_cost') }}"
                 placeholder="Không nhập nếu không tính phí">
            </div>

            <div class="mb-3">
                <label for="note" class="form-label">📝 Ghi chú xử lý:</label>
                <textarea name="note" id="note" rows="4"
                          class="form-control"
                          placeholder="Nhập mô tả cách xử lý nếu cần..."></textarea>
            </div>

            <div class="mb-3">
                <label for="photos" class="form-label">🖼️ Ảnh xử lý (tùy chọn):</label>
                <input type="file" name="photos[]" id="photos"
                       class="form-control" multiple accept="image/*">
                <div class="form-text">Bạn có thể chọn nhiều ảnh để minh họa việc xử lý.</div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('landlord.staff.complaints.index') }}" class="btn btn-outline-secondary">
                ⬅ Quay lại danh sách
            </a>
            <button type="submit" class="btn btn-success">
                💾 Hoàn tất xử lý
            </button>
        </div>
    </form>
</div>
@endsection
