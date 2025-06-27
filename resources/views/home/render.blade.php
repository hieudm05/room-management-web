@extends('home.layouts.app')

@section('title', 'Home')

@section('content')
<div class="container">

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10 text-center">
            <div class="sec-heading center mb-4">
                <h2>Recent Listed Property</h2>
                <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center g-4">

        @foreach ($rooms as $room)
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="property-listing property-2 h-100">

                <div class="listing-img-wrapper">
                    <div class="_exlio_125">{{ $room->status == 1 ? 'For Rent' : 'For Sale' }}</div>
                    <div class="list-img-slide">
                        <div class="click">
                            @if ($room->photos->first())
                            <img src="{{ $room->photos->first()->image_url }}" width="50">
                            @else
                            <span class="text-muted">Chưa có ảnh</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="listing-detail-wrapper">
                    <div class="listing-short-detail-wrap">
                        <div class="_card_list_flex mb-2">
                            <div class="_card_flex_01">
                                <span class="_list_blickes _netork">Network info</span> {{-- Nếu có dữ liệu cụ thể thì thay thế --}}
                                <span class="_list_blickes types">{{ $room->property->type ?? 'Type Unknown' }}</span>
                            </div>
                          @auth
    <div class="prt_saveed_12lk">
        <form action="{{ route('home.favorites.toggle', $room->property->property_id) }}" method="POST">
            @csrf
            @php
                $isFavorited = Auth::user()->favorites->contains('property_id', $room->property->property_id);
            @endphp
            <button type="submit" class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
                ❤️ {{ $isFavorited ? 'Bỏ yêu thích' : 'Yêu thích' }}
            </button>
        </form>
    </div>
@endauth

                        </div>
                        <div class="_card_list_flex">
                            <div class="_card_flex_01">
                                <h4 class="listing-name verified">
                                    <a href="{{ route('show2', $room) }}" class="prt-link-detail">
                                        {{ $room->property->address ?? 'Address not available' }}
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="price-features-wrapper">
                    <div class="list-fx-features">
                        <div class="listing-card-info-icon">
                            <img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="Beds" /> {{ $room->occupants }} Beds
                        </div>
                        <div class="listing-card-info-icon">
                            <img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15" alt="Baths" /> 1 Bath {{-- Thay nếu có dữ liệu thực --}}
                        </div>
                        <div class="listing-card-info-icon">
                            <img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="Area" /> {{ $room->area }} sqft
                        </div>
                    </div>
                </div>

                <div class="listing-detail-footer">
                    <div class="footer-first">
                        <h6 class="listing-card-info-price mb-0 p-0">${{ number_format($room->rental_price) }}</h6>
                    </div>
                    <div class="footer-flex">
                        <a href="{{ route('show2', $room) }}" class="prt-view">View Detail</a>
                    </div>
                </div>

            </div>
        </div>
        @endforeach

    </div>

    <div class="row mt-5">
        <div class="col-lg-12 col-md-12 col-sm-12 text-center">
            {{ $rooms->links() }}
        </div>
    </div>

</div>
@endsection
