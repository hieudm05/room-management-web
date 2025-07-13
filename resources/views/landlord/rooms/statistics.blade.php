@extends('landlord.layouts.app')

@section('title', 'Thống kê hợp đồng của phòng ' . $room->room_number)

@section('content')
    <div class="container mt-4">
        <h4>📊 Thống kê hợp đồng - Phòng: {{ $room->room_number }}</h4>

        @if ($contracts->isEmpty())
            <p class="text-muted">Không có hợp đồng nào.</p>
        @else
            <canvas id="contractChart" width="400" height="200"></canvas>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('contractChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($contracts->keys()) !!},
                    datasets: [{
                        label: 'Số lượng hợp đồng',
                        data: {!! json_encode($contracts->values()) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
