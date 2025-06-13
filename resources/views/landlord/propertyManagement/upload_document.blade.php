{{-- filepath: resources/views/landlord/propertyManagement/upload_document.blade.php --}}
@extends('landlord.layouts.app')

@section('title', 'Bổ sung giấy tờ')

@section('content')
<div class="container card mt-4">
    <div class="card-header">
        <h4>Bổ sung giấy tờ cho: {{ $property->name }}</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('landlords.properties.uploadDocument.post', $property->property_id) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="giay_phep_kinh_doanh" class="form-label">Giấy phép kinh doanh</label>
                <input type="file" id="giay_phep_kinh_doanh" name="document_files[giay_phep_kinh_doanh]"
                    class="form-control @error('document_files.giay_phep_kinh_doanh') is-invalid @enderror"
                    accept=".pdf,.jpg,.jpeg,.png" required>
                @error('document_files.giay_phep_kinh_doanh')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Gửi giấy tờ</button>
            </div>
        </form>
    </div>
</div>
@endsection