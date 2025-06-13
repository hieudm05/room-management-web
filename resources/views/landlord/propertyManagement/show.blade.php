@extends('landlord.layouts.app')

@section('title', 'Chi tiết bất động sản')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header  text-white">
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

            {{-- Ảnh bất động sản nếu có --}}
            @if ($property->image_url)
                <div class="mb-3 text-center">
                   <img src="{{ Storage::url($property->image_url) }}" alt="Ảnh bất động sản">
                </div>
            @endif

            <hr>

            {{-- Giấy tờ pháp lý --}}
            <h5>Giấy tờ pháp lý</h5>
            @if ($legalDocuments->isEmpty())
                <p class="text-muted">Chưa có giấy tờ nào được tải lên.</p>
            @else
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Loại giấy tờ</th>
                            <th>Trạng thái</th>
                            <th>File</th>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <a href="{{ route('landlords.properties.list') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>

        </div>
    </div>
</div>
@endsection
