@extends('landlord.layouts.app')

@section('content')
<div class="container mt-4">
    <h3>📄 Chi tiết khiếu nại #{{ $complaint->id }}</h3>
    <hr>

    <p><strong>Khách hàng:</strong> {{ $complaint->full_name }} ({{ $complaint->phone }})</p>
    <p><strong>Vấn đề:</strong> {{ $complaint->commonIssue->name ?? 'N/A' }}</p>
    <p><strong>Phòng:</strong> {{ $complaint->room->room_number ?? '---' }}</p>
    <p><strong>Ghi chú xử lý:</strong> {{ $complaint->note ?? '---' }}</p>
    <p><strong>Trạng thái:</strong>
        <span class="badge bg-success">{{ ucfirst($complaint->status) }}</span>
    </p>

    <p><strong>Ngày xử lý:</strong> <td>{{ \Carbon\Carbon::parse($complaint->updated_at)->format('d/m/Y H:i') }}</td></p>

    @if($complaint->photos->count())
        <h5>📷 Ảnh xử lý:</h5>
        <div class="row">
            @foreach ($complaint->photos as $photo)
                <div class="col-md-3 mb-3">
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" class="img-fluid rounded border" alt="Ảnh xử lý">
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('landlord.staff.complaints.history') }}" class="btn btn-secondary mt-3">⬅️ Quay lại</a>
</div>
@endsection