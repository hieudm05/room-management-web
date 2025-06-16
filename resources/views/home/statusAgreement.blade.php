@extends('home.layouts.app')

@section('title', 'Nội dung hợp đồng')

@section('content')
    <div class="container my-4">
        <h3 class="mb-4">📄 Nội dung hợp đồng</h3>

        @if (!$rentalAgreement)
            <div class="alert alert-danger">
                <strong>⚠ Không tìm thấy hợp đồng.</strong>
                {{-- <div class="">
                    <a href="{{ route('landlords.rooms.index') }}">Quay lại</a>
                </div> --}}
            </div>
        @else
            {{-- Trạng thái --}}
            <div
                class="alert 

        @if ($rentalAgreement->status === 'Approved') alert-success 
        @elseif ($rentalAgreement->status === 'Rejected') alert-danger 
        @else alert-warning @endif">
                <strong>Trạng thái hợp đồng:</strong> {{ $rentalAgreement->status }}
            </div>

            {{-- Nội dung hợp đồng --}}
            @if ($wordText)
                <div class="border p-3 bg-light" style="white-space: pre-wrap;">
                    {!! nl2br(e($wordText)) !!}
                </div>
            @else
                <div class="alert alert-warning mt-3">
                    ⚠️ Hợp đồng chưa có file đính kèm hoặc file lỗi.
                </div>
            @endif

           
      
                <form action="{{ route('room-users.create', $room) }}" method="POST" class="mt-3">
                    @csrf 
                    <input type="hidden" name="rental_id" value="{{ $rentalAgreement->rental_id }}">
                    <input type="hidden" name="room_id" value="{{ $rentalAgreement->room_id }}" >
                    {{-- <input type="hidden" name="tenant_name" value="{{ $tenant_name }}">
                    <input type="hidden" name="tenant_email" value="{{ $tenant_email }}">
                    <input type="hidden" value="{{ $room->occupants }}" name="occupants">
                    <input type="hidden" value="{{ $room->people_renter }}" name="people_renter">  --}}
                    <button type="submit" class="btn btn-success">✅ Bổ sung thành viên </button>
                </form>
         
        @endif
      
    </div>
@endsection
