@extends('landlord.layouts.app')

@section('content')
    <div class="container py-4">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">✏️ Chỉnh sửa bài viết</h2>

            <form action="{{ route('staff.posts.update', $post->post_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tiêu đề bài viết</label>
                        <input type="text" name="title" class="form-control" required
                            value="{{ old('title', $post->title) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="property_id" class="form-label">Chọn khu trọ</label>
                        <select name="property_id" id="property_id" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->property_id }}"
                                    {{ $post->property_id == $property->property_id ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Giá thuê</label>
                        <input type="text" name="price" class="form-control" required
                            value="{{ old('price', $post->price) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diện tích (m²)</label>
                        <input type="number" name="area" class="form-control" required
                            value="{{ old('area', $post->area) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quận/Huyện</label>
                        <input type="text" name="district" class="form-control" required
                            value="{{ old('district', $post->district) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tỉnh/Thành</label>
                        <input type="text" name="city" class="form-control" required
                            value="{{ old('city', $post->city) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Địa chỉ chi tiết</label>
                        <input type="text" name="address" class="form-control" required
                            value="{{ old('address', $post->address) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết</label>
                        <textarea id="editor" name="description" class="form-control" rows="5">{{ old('description', $post->description) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày đăng</label>
                        <input type="date" name="published_at" class="form-control"
                            value="{{ old('published_at', \Carbon\Carbon::parse($post->published_at)->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày hết hạn</label>
                        <input type="date" name="expired_at" class="form-control"
                            value="{{ old('expired_at', \Carbon\Carbon::parse($post->expired_at)->format('Y-m-d')) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        @if ($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Thumbnail" class="img-fluid mt-2"
                                style="max-width: 200px;">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thư viện ảnh (tối đa 5 ảnh)</label>
                        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật bài viết
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
