@extends('landlord.layouts.app')
@section('title', 'Từ chối yêu cầu')

@section('content')
<div class="container mt-4">
    <h4>❌ Nhập lý do từ chối yêu cầu</h4>

    <form method="POST" action="{{ route('landlord.roomleave.reject', $request->id) }}">
        @csrf
        <div class="mb-3">
            <label for="reject_reason">Lý do từ chối:</label>
            <textarea name="reject_reason" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Gửi từ chối</button>
        <a href="{{ route('landlord.roomleave.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection