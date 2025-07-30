@extends('landlord.layouts.app')
@section('title', 'Yêu cầu đã duyệt bởi nhân viên')

@section('content')
<div class="container mt-4">
    <h4>📄 Danh sách yêu cầu chờ chủ trọ xử lý</h4>

    @foreach ($requests as $req)
        <div class="card mb-3">
            <div class="card-body">
                <p>👤 Người thuê: <strong>{{ $req->user->name }}</strong></p>
                <p>🏠 Phòng: {{ $req->room->name }}</p>
                <p>📅 Ngày yêu cầu: {{ $req->leave_date }}</p>
                <p>📌 Loại: {{ $req->type === 'transfer' ? 'Nhượng quyền' : 'Rời phòng' }}</p>

                <a href="{{ route('landlord.roomleave.show', $req->id) }}" class="btn btn-info btn-sm">
                    🔍 Xem chi tiết
                </a>

                {{-- ✅ Nút Duyệt --}}
                <form action="{{ route('landlord.roomleave.approve', $req->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn duyệt yêu cầu này?')">
                    @csrf
              
                    <button type="submit" class="btn btn-success btn-sm">✅ Duyệt</button>
                </form>

                {{-- ❌ Nút Từ chối --}}
               <a href="{{ route('landlord.roomleave.rejectForm', $req->id) }}" class="btn btn-danger btn-sm">
    ❌ Từ chối
</a>
            </div>
        </div>
    @endforeach
</div>
@endsection