@extends('home.layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
    <div class="container mt-4">

        {{-- Tiêu đề --}}
        <div class="row justify-content-center mb-4">
            <div class="col-lg-7 col-md-10 text-center">
                <div class="sec-heading center">
                    <h2>Kết quả tìm kiếm</h2>
                    <p>
                        @if (!empty($keyword))
                            Kết quả tìm kiếm cho:
                            <span class="fw-bold text-primary">"{{ $keyword }}"</span>
                        @else
                            Tất cả kết quả hiển thị bên dưới
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Kết quả --}}
        <div class="row g-4">
            @forelse ($posts as $post)
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="card h-100 shadow-sm rounded-3 border-0">

                        {{-- Thumbnail --}}
                        @if ($post->thumbnail && file_exists(public_path('storage/' . $post->thumbnail)))
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" class="card-img-top rounded-top-3"
                                alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="d-flex justify-content-center align-items-center bg-light" style="height: 200px;">
                                <span class="text-muted">Chưa có ảnh</span>
                            </div>
                        @endif

                        {{-- Nội dung --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold text-truncate mb-2" title="{{ $post->title }}">
                                {{ Str::limit($post->title, 60) }}
                            </h5>

                            <p class="text-danger fw-semibold mb-2">
                                {{ number_format($post->price, 0, ',', '.') }} đ/tháng
                            </p>

                            <ul class="list-unstyled small mb-3">
                                <li><i class="fa fa-expand me-1 text-secondary"></i> {{ $post->area }} m²</li>
                                <li><i class="fa fa-map-marker-alt me-1 text-secondary"></i>
                                    {{ $post->district }}, {{ $post->city }}
                                </li>
                                <li><i class="fa fa-home me-1 text-secondary"></i> {{ $post->address }}</li>
                                <li><i class="fa fa-phone me-1 text-secondary"></i>
                                    {{ $post->property->phone ?? 'Liên hệ chủ trọ' }}
                                </li>
                            </ul>

                            <p class="text-muted small flex-grow-1">
                                {{ Str::limit(strip_tags($post->description), 100) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">
                                    Mã tin: {{ $post->post_code ?? '---' }}
                                </small>
                                <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Không tìm thấy kết quả nào phù hợp.</p>
                </div>
            @endforelse
        </div>

        {{-- Phân trang --}}
        <div class="row mt-5">
            <div class="col-12 text-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
