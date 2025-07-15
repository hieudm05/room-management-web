@extends('home.layouts.app')

@section('title', 'Thông báo')

@section('content')
<div class="container py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
    <h4 class="mb-4">🔔 Thông báo của bạn</h4>

    @if($notifications->count())
    <form id="bulk-delete-form" method="POST" action="{{ route('notifications.bulk-delete') }}">
        @csrf

        <div class="mb-3">
            <button type="submit" class="btn btn-danger btn-sm">🗑️ Xoá đã chọn</button>
        </div>

        <div class="list-group">
            @foreach($notifications as $notification)
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="d-flex">
                        <div class="form-check me-2">
                            <input type="checkbox" name="ids[]" value="{{ $notification->id }}" class="form-check-input">
                        </div>
                        <div>
                            <a href="{{ $notification->link ?? '#' }}"
                               onclick="event.preventDefault(); document.getElementById('read-{{ $notification->id }}').submit();"
                               class="@if(!$notification->pivot->is_read) fw-bold @endif text-decoration-none text-dark">
                                <div>{{ $notification->title }}</div>
                                <div class="text-muted small">{{ $notification->message }}</div>
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($notification->pivot->received_at)->diffForHumans() }}</div>
                            </a>

                            <form id="read-{{ $notification->id }}" method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-end">
                        @if(!$notification->pivot->is_read)
                            <span class="badge bg-primary mb-2">Mới</span>
                        @endif

                        {{-- Nút xoá từng thông báo --}}
                        <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xoá thông báo này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">🗑 Xoá</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
    @else
        <div class="alert alert-info">Bạn chưa có thông báo nào.</div>
    @endif
</div>
@endsection
