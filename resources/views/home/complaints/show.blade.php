@extends('home.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow mt-10">
    <h2 class="text-2xl font-semibold mb-4 text-center">Chi tiết khiếu nại</h2>

    <div class="space-y-2">
        <p><strong>ID:</strong> {{ $complaint->id }}</p>
        <p><strong>Họ tên:</strong> {{ $complaint->full_name }}</p>
        <p><strong>SĐT:</strong> {{ $complaint->phone }}</p>
        <p><strong>Tòa:</strong> {{ $complaint->property->name ?? '---' }}</p>
        <p><strong>Phòng:</strong> {{ $complaint->room->room_number ?? '---' }}</p>
        <p><strong>Vấn đề:</strong> {{ $complaint->commonIssue->name ?? '---' }}</p>
        <p><strong>Mô tả:</strong> {{ $complaint->detail ?? '(Không có)' }}</p>
        <p><strong>Trạng thái:</strong> {{ ucfirst($complaint->status) }}</p>
        @if ($complaint->staff)
        <p><strong>Nhân viên xử lý:</strong> 
        {{ $complaint->staff->name }} 
        @if ($complaint->staff->email)
            ({{ $complaint->staff->email }})
        @endif
    </p>
@endif
        <p><strong>Ngày gửi:</strong> {{ $complaint->created_at->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Ảnh --}}
    <div class="mt-6">
        <h3 class="text-lg font-medium mb-2">Hình ảnh đính kèm:</h3>

        @if ($complaint->photos->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach ($complaint->photos as $photo)
                    <div class="border rounded overflow-hidden">
                        <a href="{{ asset('storage/' . $photo->photo_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Ảnh khiếu nại"
                                 class="w-full h-40 object-cover hover:opacity-90 transition">
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">Không có ảnh đính kèm.</p>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('home.complaints.index') }}" class="text-blue-600 hover:underline">← Quay lại danh sách</a>
    </div>
</div>
@endsection
