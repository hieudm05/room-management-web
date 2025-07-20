@extends('home.layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">

        <!-- Verified Properties Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Verified Properties</h2>
                    <p>Explore our selection of verified properties, ensuring quality and trust.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            @foreach ($rooms->where('property.verified', true) as $room)
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
                                        <span class="_list_blickes _netork">Network info</span>
                                        <span
                                            class="_list_blickes types">{{ $room->property->type ?? 'Type Unknown' }}</span>
                                    </div>
                                    @auth
                                        <div class="prt_saveed_12lk">
                                            <form action="{{ route('home.favorites.toggle', $room->property->property_id) }}"
                                                method="POST">
                                                @csrf
                                                @php
                                                    $isFavorited = Auth::user()->favorites->contains(
                                                        'property_id',
                                                        $room->property->property_id,
                                                    );
                                                @endphp
                                                <button type="submit"
                                                    class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
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
                                    <img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="Beds" />
                                    {{ $room->occupants }} Beds
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15"
                                        alt="Baths" />
                                    1 Bath
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="Area" />
                                    {{ $room->area }} sqft
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
            @if ($rooms->where('property.verified', true)->isEmpty())
                <div class="col-12 text-center">
                    <p class="text-muted">No verified properties available at the moment.</p>
                </div>
            @endif
        </div>

        <!-- Existing Recent Listings Section -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Recent Listed Property</h2>
                    <p>Danh sách các phòng trọ mới được đăng lên hệ thống.</p>
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
                                        <span class="_list_blickes _netork">Network info</span>
                                        <span
                                            class="_list_blickes types">{{ $room->property->type ?? 'Type Unknown' }}</span>
                                    </div>
                                    @auth
                                        <div class="prt_saveed_12lk">
                                            <form action="{{ route('home.favorites.toggle', $room->property->property_id) }}"
                                                method="POST">
                                                @csrf
                                                @php
                                                    $isFavorited = Auth::user()->favorites->contains(
                                                        'property_id',
                                                        $room->property->property_id,
                                                    );
                                                @endphp
                                                <button type="submit"
                                                    class="btn btn-sm {{ $isFavorited ? 'btn-danger' : 'btn-outline-danger' }}">
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
                                    <img src="{{ asset('assets/client/img/bed.svg') }}" width="15" alt="Beds" />
                                    {{ $room->occupants }} Beds
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/bathtub.svg') }}" width="15"
                                        alt="Baths" />
                                    1 Bath
                                </div>
                                <div class="listing-card-info-icon">
                                    <img src="{{ asset('assets/client/img/move.svg') }}" width="15" alt="Area" />
                                    {{ $room->area }} sqft
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

        <!-- Approved Posts Section -->
        <div class="row justify-content-center mt-5">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center mb-4">
                    <h2>Bài Đăng Cho Thuê Đã Duyệt</h2>
                    <p>Danh sách các bài viết cho thuê đã được duyệt và public.</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse ($posts as $post)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card h-100 shadow-sm rounded-3 border-0">

                        @if ($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="card-img-top rounded-top-3"
                                alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="d-flex justify-content-center align-items-center bg-light" style="height: 200px;">
                                <span class="text-muted">Chưa có ảnh</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold text-truncate mb-2" title="{{ $post->title }}">
                                {{ $post->title }}
                            </h5>

                            <p class="text-danger fw-semibold mb-2">
                                {{ number_format($post->price, 0, ',', '.') }} đ/tháng
                            </p>

                            <ul class="list-unstyled small mb-3">
                                <li><i class="fa fa-expand me-1 text-secondary"></i> {{ $post->area }} m²</li>
                                <li><i class="fa fa-map-marker-alt me-1 text-secondary"></i> {{ $post->district }},
                                    {{ $post->city }}</li>
                                <li><i class="fa fa-home me-1 text-secondary"></i> {{ $post->address }}</li>
                                <li><i class="fa fa-phone me-1 text-secondary"></i>
                                    {{ $post->property->phone ?? 'Liên hệ chủ trọ' }}</li>
                            </ul>

                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit(strip_tags($post->description), 100) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">Mã tin: {{ $post->post_code }}</small>
                                <a href="{{ route('posts.show', $post->slug) }}"
                                    class="btn btn-sm btn-outline-primary">Xem chi tiết</a>

                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Hiện chưa có bài đăng cho thuê nào được duyệt.</p>
                </div>
            @endforelse
        </div>





        <div class="row mt-5">
            <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                {{ $posts->links() }}
            </div>
        </div>

    </div>
@endsection
