{{-- filepath: resources/views/landlord/propertyManagement/show.blade.php --}}
@extends('landlord.layouts.app')

@section('title', 'Chi tiết bất động sản')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h4 class="mb-0">Thông tin chi tiết bất động sản</h4>
        </div>
        <div class="card-body">

            {{-- Thông tin bất động sản --}}
            <div class="mb-3">
                <strong>Tên:</strong> {{ $property->name }}<br>
                <strong>Địa chỉ:</strong> {{ $property->address }}<br>
                <strong>Kinh độ:</strong> {{ $property->longitude }}<br>
                <strong>Vĩ độ:</strong> {{ $property->latitude }}<br>
                <strong>Mô tả:</strong> {{ $property->description ?? 'Không có mô tả' }}<br>
                <strong>Chủ trọ:</strong> {{ $property->landlord_name }} (ID: {{ $property->landlord_id }})<br>
            </div>
            <hr>

            {{-- Giấy tờ pháp lý --}}
<h5>Giấy tờ pháp lý</h5>
@if ($legalDocuments->isEmpty())
    <p class="text-muted">Chưa có giấy tờ nào được tải lên.</p>
@else
    <table class="table table-bordered table-hover align-middle">
        <thead class="thead-light">
            <tr>
                <th>Loại giấy tờ</th>
                <th>Trạng thái</th>
                <th>File</th>
                <th>Cập nhật</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($legalDocuments as $doc)
            <tr>
                <td>{{ $doc->document_type }}</td>
                <td>
                    @if ($doc->status == 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @elseif ($doc->status == 'Pending')
                        <span class="badge bg-warning">Chờ duyệt</span>
                    @else
                        <span class="badge bg-danger">Từ chối</span>
                    @endif
                </td>
                <td>
                    <a href="{{ Storage::url($doc->file_path) }}"
                       class="btn btn-sm btn-outline-primary"
                       target="_blank">Xem</a>
                </td>
                <td>
                    <form method="POST" action="{{ route('landlords.properties.uploadDocument.post', $property->property_id) }}" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="document_types[]" value="{{ $doc->document_type }}">
                        <input type="file" name="document_files[]" accept=".pdf,.jpg,.jpeg,.png" class="form-control form-control-sm" required style="max-width:170px;">
                        <button type="submit" class="btn btn-sm btn-primary">Cập nhật</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- Form bổ sung giấy tờ mới --}}
<div class="mt-4">
    <h5>Bổ sung giấy tờ mới</h5>
    <form method="POST" action="{{ route('landlords.properties.uploadDocument.post', $property->property_id) }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="document_types[]" class="form-select" required>
                    <option value="">Chọn loại giấy tờ</option>
                    <option value="Giấy phép kinh doanh">Giấy phép kinh doanh</option>
                    <option value="Giấy chứng nhận PCCC">Giấy chứng nhận PCCC</option>
                    <option value="Khác">Khác</option>
                </select>
            </div>
            <div class="col-md-6">
                <input type="file" name="document_files[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-success">Thêm giấy tờ</button>
            </div>
        </div>
    </form>
</div>

            <a href="{{ route('landlords.properties.list') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
        </div>
    </div>
</div>
@endsection