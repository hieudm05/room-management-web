@extends('landlord.layouts.app')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">🔔 Thông báo</h4>

    @php
        use Carbon\Carbon;
    @endphp

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($notifications->count())
        {{-- FORM BULK DELETE --}}
        <form method="POST" action="{{ route('landlord.notifications.bulk-delete') }}">
            @csrf
            <div class="mt-3 mb-3">
                <button type="submit" class="btn btn-danger btn-sm">🗑️ Xoá đã chọn</button>
            
            <ul class="list-group">
                @foreach($notifications as $n)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start flex-grow-1">
                                {{-- Checkbox để xoá hàng loạt --}}
                                <div class="form-check me-3 mt-1">
                                    <input type="checkbox" class="form-check-input" name="ids[]" value="{{ $n->id }}">
                                </div>

                                <div>
                                    {{-- Link đánh dấu đã đọc --}}
                                    <form id="read-form-{{ $n->id }}" action="{{ route('landlord.notifications.read', $n->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 m-0 text-start @if(!$n->pivot->is_read) fw-bold @endif text-decoration-none text-dark">
                                            {{ $n->title }}
                                        </button>
                                    </form>

                                    <div class="text-muted small">{{ $n->message }}</div>
                                    <small class="text-muted">{{ Carbon::parse($n->pivot->received_at)->diffForHumans() }}</small>
                                </div>
                            </div>

                            {{-- Form xoá đơn --}}
                            <form action="{{ route('landlord.notifications.destroy', $n->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá thông báo này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>

           
        </form>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <p>Không có thông báo nào.</p>
    @endif
</div>
@endsection
