@extends('landlord.layouts.app')

@section('content')
<div class="container mt-4">
    <h2><span class="me-2">👨‍🔧</span>Ủy quyền xử lý khiếu nại #{{ $complaint->id }}</h2>

    <form method="POST" action="{{ route('landlord.complaints.assign', $complaint->id) }}" class="mt-4">
        @csrf

        <div class="mb-3">
            <label for="staff_id" class="form-label">Chọn nhân viên:</label>
           <select class="form-select" name="staff_id" id="staff_id" required>
    @if($staffList->isEmpty())
        <option value="">Không còn nhân viên khả dụng</option>
    @else
        <option value="">-- Chọn nhân viên --</option>
        @foreach ($staffList as $staff)
            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
        @endforeach
    @endif
</select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">✅ Xác nhận ủy quyền</button>
            <a href="{{ route('landlord.complaints.index') }}" class="btn btn-outline-secondary">❌ Hủy</a>
        </div>
    </form>
</div>
@endsection