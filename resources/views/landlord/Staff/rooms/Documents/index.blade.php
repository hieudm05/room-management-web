{{-- filepath: c:\laragon\www\room-management-web\resources\views\landlord\Staff\rooms\Documents\index.blade.php --}}
@extends('landlord.layouts.app')

@section('title', 'Danh sách người thuê phòng')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bage-primary text-white">
            <h4 class="mb-0">Danh sách người thuê phòng {{ $roomDatas->room_number ?? "Chưa có tên phòng" }}</h4>
        </div>
        <div class="card-body">
            @if(isset($roomInfoS) && count($roomInfoS))
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Họ và tên</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>CCCD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomInfoS as $i => $tenant)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $tenant->full_name ?? $tenant->name }}</td>
                                    <td>{{ $tenant->phone ?? '-' }}</td>
                                    <td>{{ $tenant->email ?? '-' }}</td>
                                    <td>{{ $tenant->cccd ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center mb-0">
                    Chưa có người thuê phòng này.
                </div>
            @endif
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    ← Trở lại
                </a>
            </div>
        </div>
    </div>
</div>
@endsection