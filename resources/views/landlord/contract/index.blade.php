@extends('landlord.layouts.app')

@section('title', 'Nội dung hợp đồng')

@section('content')
<div class="container my-4">
    <h3 class="mb-4">📄 Nội dung hợp đồng</h3>

   @if (!$rentalAgreement)
    <div class="alert alert-danger">
        <strong>⚠ Không tìm thấy hợp đồng.</strong>
         <div class="">
            <a  href="{{route("landlords.rooms.index")}}">Quay lại</a>
        </div>
    </div>
@else
    {{-- Trạng thái --}}
    <div class="alert 
        @if ($rentalAgreement->status === 'Approved') alert-success 
        @elseif ($rentalAgreement->status === 'Rejected') alert-danger 
        @else alert-warning 
        @endif">
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

    {{-- Nút xác nhận --}}
    @if ($rentalAgreement->status === 'Pending')
        <form action="{{ route('landlords.rooms.contract.confirmLG', $room) }}" method="POST" class="mt-3">
            @csrf
            <input type="hidden" name="rental_id" value="{{ $rental_id }}">
            <input type="hidden" name="tenant_name" value="{{ $tenant_name }}">
            <input type="hidden" name="tenant_email" value="{{ $tenant_email }}">
            <input type="hidden" value="{{$room->occupants}}" name="occupants" >
                                                <input type="hidden" value="{{$room->people_renter}}" name="people_renter" >
            <button type="submit" class="btn btn-success">✅ Xác nhận lưu hợp đồng</button>
        </form>
    @endif
@endif
 <table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Tên người thuê</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>CCCD</th>
            <th>ID Phòng</th>
            <th>ID Hợp đồng</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roomUsers as $index => $user)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>{{ $user->cccd }}</td>
            <td>{{ $user->room_id }}</td>
            <td>{{ $user->rental_id }}</td>
            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <form action="{{ route('landlords.rooms.room_users.suscess', $user->user_id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                    <button type="submit" class="btn btn-success btn-sm">
                        ✅ Xác nhận
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection
