@extends('landlord.layouts.app')

@section('title', 'Phân quyền nhân viên')

@section('content')
    <div class="container bg-white p-4 rounded shadow-sm">
        <h4 class="mb-4">👥 Phân quyền cho phòng: <strong>{{ $room->room_number }}</strong></h4>

        <form method="POST" action="{{ route('landlords.rooms.staffs.update', $room->room_id) }}">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>✔️ Chọn</th>
                            <th>Tên nhân viên</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Khoá / Mở</th>
                            <th>Lượt sửa</th>
                            <th>Chi tiết</th>
                            <th>Xoá quyền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffs as $staff)
                            @php
                                $assignedRecord = $room->staffs->firstWhere('id', $staff->id);
                                $status = $assignedRecord ? $assignedRecord->pivot->status : null;
                                $editCount = \App\Models\Landlord\RoomEditRequest::where('room_id', $room->room_id)
                                    ->where('staff_id', $staff->id)
                                    ->count();
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="staffs[{{ $staff->id }}][assign]"
                                        {{ $assignedRecord ? 'checked' : '' }}>
                                </td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td class="text-center">
                                    @if ($status === 'active')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @elseif ($status === 'inactive')
                                        <span class="badge bg-danger">Đã khoá</span>
                                    @else
                                        <span class="text-muted">Chưa phân quyền</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($assignedRecord)
                                        <select name="staffs[{{ $staff->id }}][status]"
                                            class="form-select form-select-sm">
                                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Mở khoá
                                            </option>
                                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>🔒 Khoá
                                            </option>
                                        </select>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $editCount }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($editCount > 0)
                                        <a href="{{ route('landlords.room_edit_requests.index') }}?room_id={{ $room->room_id }}&staff_id={{ $staff->id }}"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Xem các yêu cầu chỉnh sửa">👁️</a>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($assignedRecord)
                                        <button type="submit" name="remove_staff_id" value="{{ $staff->id }}"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xoá phân quyền nhân viên này">🗑️</button>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    💾 Lưu cập nhật
                </button>
            </div>
        </form>
    </div>
@endsection
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
</script>
