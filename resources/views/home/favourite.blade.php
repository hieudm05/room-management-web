@extends('home.layouts.app')

@section('title', 'Trọ yêu thích')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">❤️ Danh sách trọ bạn đã yêu thích</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @forelse ($favorites as $property)
            @php
                $firstRoom = $property->rooms->first();
                $roomImage = $firstRoom?->photos->first()?->image_url ?? asset('images/no-image.jpg');
                $price = $firstRoom?->rental_price ?? 0;
            @endphp

            <div class="card mb-4 shadow-sm">
                <div class="row g-0">
                    {{-- Ảnh --}}
                    <div class="col-md-4">
                        <img src="{{ $roomImage }}" class="img-fluid rounded-start h-100 object-fit-cover" alt="Ảnh trọ">
                    </div>

                    <div class="col-md-8">
                        <div class="card-body d-flex flex-column justify-content-between h-100">
                            {{-- Tiêu đề + địa chỉ --}}
                            <div>
                                <h5 class="card-title">{{ $property->title ?? 'Không có tiêu đề' }}</h5>
                                <p class="card-text text-muted">{{ $property->address ?? 'Chưa có địa chỉ' }}</p>

                                {{-- Avatar chủ trọ --}}
                                @if ($property->user ?? false)
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <img src="{{ $property->user->avatar ?? asset('images/default-avatar.png') }}"
                                            class="rounded-circle" width="32" height="32" alt="Avatar chủ trọ">
                                        <span class="text-secondary small">{{ $property->user->name ?? 'Chủ trọ' }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Giá + Nút bỏ yêu thích --}}
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="text-success fw-bold">{{ number_format($price) }} VND / tháng</span>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('home.favorites.toggle', $property->property_id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            ❤️ Bỏ yêu thích
                                        </button>
                                    </form>
                                </div>
                                <div class="d-flex gap-2">
                                    {{-- Nút xem chi tiết --}}
                                    @if ($firstRoom)
                                        <a href="{{ route('show2', $firstRoom) }}" class="btn btn-sm btn-primary">
                                            🔍 Xem chi tiết
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Bạn chưa yêu thích trọ nào.
                </div>
        @endforelse
    </div>
@endsection
