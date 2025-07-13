@extends('landlord.layouts.app')

@section('title', 'Phân quyền nhân viên')

@section('content')
<div class="container mt-4">
    <h4>Phân quyền cho phòng: <strong>{{ $room->room_number }}</strong></h4>

    <form method="POST" action="{{ route('landlords.rooms.staffs.update', $room->room_id) }}">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Chọn</th>
                    <th>Tên nhân viên</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Khoá / Mở khoá</th>
                    <th>Số lần sửa</th>
                    <th>Chi tiết sửa</th>
                    <th>Xoá phân quyền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                    @php
                        $assignedRecord = $room->staffs->firstWhere('id', $staff->id);
                        $status = $assignedRecord ? $assignedRecord->pivot->status : null;

                        // Đếm số lần sửa của nhân viên này cho phòng này
                        $editCount = \App\Models\Landlord\RoomEditRequest::where('room_id', $room->room_id)
                            ->where('staff_id', $staff->id)->count();
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="staffs[{{ $staff->id }}][assign]"
                                   {{ $assignedRecord ? 'checked' : '' }}>
                        </td>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>
                            {{ $status ?? 'Chưa phân quyền' }}
                        </td>
                        <td>
                            @if ($assignedRecord)
                                <select name="staffs[{{ $staff->id }}][status]" class="form-select">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Mở khoá</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>🔒 Khoá</option>
                                </select>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $editCount }}</span>
                        </td>
                        <td>
                            @if ($editCount > 0)
                                <a href="{{ route('landlords.room_edit_requests.index') }}?room_id={{ $room->room_id }}&staff_id={{ $staff->id }}" class="btn btn-sm btn-secondary">
                                    👁️ Xem
                                </a>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                        <td>
                            @if ($assignedRecord)
                                <button type="submit" name="remove_staff_id" value="{{ $staff->id }}"
                                        class="btn btn-sm btn-outline-danger">🗑️ Xoá</button>
                            @else
                                <span class="text-muted">--</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">💾 Lưu cập nhật</button>
    </form>
</div>
@endsection
