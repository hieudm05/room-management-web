@extends('landlord.layouts.app')

@section('title', 'Yêu cầu tái ký hợp đồng')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">📄 Danh sách yêu cầu tái ký hợp đồng</h3>

        @if ($renewals->isEmpty())
            <div class="alert alert-info">Hiện chưa có yêu cầu tái ký hợp đồng nào.</div>
        @else
            @foreach ($renewals as $renewal)
                <div class="card mb-3 p-3 shadow-sm">
                    <h5>Phòng: {{ $renewal->room->room_number ?? 'N/A' }}</h5>
                    <p>
                        👤 Người thuê: <strong>{{ $renewal->user->name }}</strong><br>
                        📅 Hết hạn:
                        {{ $renewal->room->currentAgreement?->end_date
                            ? \Carbon\Carbon::parse($renewal->room->currentAgreement->end_date)->format('d/m/Y')
                            : 'Không có hợp đồng hiện tại' }}
                        <br>
                        ⏳ Ngày yêu cầu: {{ $renewal->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            @endforeach
        @endif
    </div>
@endsection
