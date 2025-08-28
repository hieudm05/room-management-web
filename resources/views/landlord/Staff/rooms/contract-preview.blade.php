@extends('landlord.layouts.app')

@section('title', 'Xem trước hợp đồng')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center mx-0">
        <div class="col-12">
            <div class="card border-0 rounded-0 shadow-none">
                <div class="card-header bage-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">📄 Xem trước hợp đồng thuê</h4>
                </div>

                <div class="card-body bg-light p-0">
                    {{-- Hiển thị hợp đồng PDF --}}
                    <iframe
                        src="{{ $publicUrl }}"
                        style="width: 100%; height: 90vh; border: none; display: block;"></iframe>

                    {{-- Form xác nhận --}}
                    <div class="text-center my-4">
                        <form method="POST" action="{{ route('landlords.staff.contract.confirm', $room) }}">
                            @csrf
                            <input type="hidden" name="temp_path" value="{{ $tempPath }}">
                            <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
                                ✅ Xác nhận lưu hợp đồng
                            </button>
                            <a href="{{ route('landlords.staff.contract.index', $room) }}" class="btn btn-secondary px-4 py-2 fw-bold ms-3">
                                ❌ Hủy bỏ
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
