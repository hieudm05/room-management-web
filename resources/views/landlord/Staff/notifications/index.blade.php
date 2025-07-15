@extends('landlord.layouts.app')

@section('content')
<div class="container py-4">
    <h4>Thông báo của nhân viên </h4>
    <form method="POST" action="{{ route('staff.notifications.bulk-delete') }}" onsubmit="return confirm('Bạn có chắc muốn xóa các thông báo đã chọn không?')">
    @csrf
    @method('DELETE')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Thông báo</h4>
        <button type="submit" class="btn btn-sm btn-danger">🗑 Xóa đã chọn</button>
    </div>

    @if($notifications->count())
        <ul class="list-group">
            <li class="list-group-item">
                <input type="checkbox" id="check-all"> <label for="check-all" class="ms-1">Chọn tất cả</label>
            </li>

            @foreach($notifications as $n)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start gap-2">
                        <input type="checkbox" name="ids[]" value="{{ $n->id }}">

                        <div>
                            <a href="{{ route('staff.notifications.read', $n->id) }}"
                               onclick="event.preventDefault(); document.getElementById('read-form-{{ $n->id }}').submit();"
                               class="@if(!$n->pivot->is_read) fw-bold @endif">
                                {{ $n->title }}
                            </a>
                            <div class="text-muted small">{{ $n->message }}</div>
                        </div>
                    </div>

                    <small>{{ $n->pivot->received_at->diffForHumans() }}</small>

                    <form id="read-form-{{ $n->id }}" action="{{ route('staff.notifications.read', $n->id) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            @endforeach
        </ul>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <p>Không có thông báo nào.</p>
    @endif
</form>
    @if($notifications->count())
        <ul class="list-group">
            @foreach($notifications as $n)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <a href="{{ route('staff.notifications.read', $n->id) }}"
                           onclick="event.preventDefault(); document.getElementById('read-form-{{ $n->id }}').submit();"
                           class="@if(!$n->pivot->is_read) fw-bold @endif">
                            {{ $n->title }}
                        </a>
                        <div class="text-muted small">{{ $n->message }}</div>
                    </div>
                    <small>{{ $n->pivot->received_at->diffForHumans() }}</small>
                    <form id="read-form-{{ $n->id }}" action="{{ route('staff.notifications.read', $n->id) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            @endforeach
        </ul>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <p>Không có thông báo nào.</p>
    @endif
</div>
@endsection
