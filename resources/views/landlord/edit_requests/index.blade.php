@extends('landlord.layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách yêu cầu chỉnh sửa phòng</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Phòng</th>
                <th>Nhân viên</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->room->room_number }}</td>
                    <td>{{ $request->staff->name }}</td>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->created_at }}</td>
                    <td>
                        <a href="{{ route('landlords.room_edit_requests.show', $request->id) }}" class="btn btn-info btn-sm">Chi tiết</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
