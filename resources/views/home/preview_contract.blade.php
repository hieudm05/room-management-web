@extends('home.layouts.app')

@section('content')
    <div class="container mt-4">
        <h4>Xem trước nội dung hợp đồng</h4>

        <div class="border p-3 bg-light" style="white-space: pre-wrap;">
            {!! nl2br(e($word_content)) !!}
        </div>

        <form action="{{ route('contracts.confirm', $room) }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="temp_path" value="{{ $temp_path }}">
            <input type="hidden" name="tenant_name" value="{{ $tenant_name }}">
            <input type="hidden" name="tenant_email" value="{{ $tenant_email }}">
            <input type="hidden" name="number_of_people" value="{{ $number_of_people }}">
            <input type="hidden" name="max_number_of_people" value="{{ $max_number_of_people }}">

            <button type="submit" class="btn btn-success">Xác nhận lưu hợp đồng</button>
        </form>

        @if (!$tenant_email || !$tenant_name)
            <div class="alert alert-warning mt-3">
                ⚠️ Không thể tự động trích xuất tên hoặc email người thuê từ file Word. Vui lòng kiểm tra lại nội dung hợp
                đồng!
            </div>
        @endif
    </div>
@endsection
