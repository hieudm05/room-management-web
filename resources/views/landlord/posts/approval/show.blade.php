@extends('landlord.layouts.app')

@section('content')
    <div class="container-fluid py-5 px-4">
        <div class="mb-5">
            <h2 class="text-primary fw-bold fs-2 animate__animated animate__fadeInDown">
                <i class="bi bi-file-earmark-text-fill me-2"></i> Chi tiết bài viết cần duyệt
            </h2>
        </div>

        <div class="card shadow-xxl border-0 rounded-4 bg-gradient-light p-5">
            <div class="row g-5">

                {{-- Left: Info --}}
                <div class="col-lg-6 pe-lg-5 border-end">
                    <h3 class="fw-bold text-success mb-4 fs-3 animate__animated animate__fadeInUp">
                        <i class="bi bi-house-door-fill me-3"></i> {{ $post->title ?? 'Không có tiêu đề' }}
                    </h3>

                    {{-- Basic Info --}}
                    <div class="mb-5">
                        @foreach ([['icon' => 'bi-tags-fill', 'label' => 'Chuyên mục', 'value' => $post->category->name ?? 'Không xác định'], ['icon' => 'bi-currency-dollar', 'label' => 'Giá thuê', 'value' => $post->price ? number_format((float) $post->price, 0, ',', '.') . ' VNĐ' : 'Không xác định', 'text_class' => 'text-danger fs-6'], ['icon' => 'bi-bounding-box-circles', 'label' => 'Diện tích', 'value' => $post->area ? $post->area . ' m²' : 'Không xác định'], ['icon' => 'bi-geo-alt-fill', 'label' => 'Địa chỉ', 'value' => $post->address ? $post->address . ', ' . $post->district . ', ' . $post->city : 'Không xác định']] as $info)
                            <div class="d-flex align-items-center p-3 bg-white rounded-4 shadow-sm mb-3 animate__animated animate__fadeInUp"
                                style="animation-delay: {{ $loop->index * 0.1 }}s">
                                <i class="bi {{ $info['icon'] }} text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block mb-1 fs-6">{{ $info['label'] }}</small>
                                    <p class="fw-bold mb-0 {{ $info['text_class'] ?? 'text-dark fs-6' }}">
                                        {{ $info['value'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Features --}}
                    <h5 class="fw-bold mb-3 text-dark fs-5 animate__animated animate__fadeInUp">
                        <i class="bi bi-stars me-2"></i> Tiện ích & Đặc điểm nổi bật
                    </h5>
                    <ul class="list-unstyled mb-5">
                        @forelse ($post->features ?? [] as $feature)
                            <li class="mb-2 fs-6 animate__animated animate__fadeInUp d-flex align-items-center"
                                style="animation-delay: {{ $loop->index * 0.1 }}s">
                                <input class="form-check-input me-3" type="checkbox" checked disabled>
                                <label class="form-check-label text-dark fs-6">
                                    {{ $feature->name ?? 'Không xác định' }}
                                </label>
                            </li>
                        @empty
                            <li class="text-muted fs-6">Không có tiện ích hoặc đặc điểm nổi bật</li>
                        @endforelse
                    </ul>

                    {{-- Description --}}
                    <h5 class="fw-bold mb-3 text-dark fs-5 animate__animated animate__fadeInUp">
                        <i class="bi bi-card-text me-2"></i> Mô tả chi tiết
                    </h5>
                    <div class="text-muted mb-5 bg-white p-4 rounded-4 shadow-sm fs-6 animate__animated animate__fadeInUp">
                        {!! $post->description ?? '<p>Không có mô tả</p>' !!}
                    </div>

                    {{-- Status --}}
                    <h5 class="fw-bold mb-3 text-dark fs-5 animate__animated animate__fadeInUp">
                        <i class="bi bi-info-circle-fill me-2"></i> Trạng thái
                    </h5>
                    <p>
                        @switch($post->status)
                            @case(0)
                                <span class="badge bg-secondary fs-6">Chờ duyệt</span>
                            @break

                            @case(1)
                                <span class="badge bg-success fs-6">Đã duyệt</span>
                            @break

                            @default
                                <span class="badge bg-danger fs-6">Đã từ chối</span><br>
                                <strong class="text-danger">Lý do từ chối:</strong> {{ $post->rejected_reason ?? 'Không xác định' }}
                        @endswitch
                    </p>

                    {{-- Actions --}}
                    @if ($post->status === 0)
                        <div class="d-flex justify-content-start gap-4 mt-4">
                            <form action="{{ route('landlord.posts.approval.approve', $post) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success px-5 py-3 rounded-pill hover-btn fs-6">
                                    <i class="bi bi-check-circle me-2"></i> Duyệt bài viết
                                </button>
                            </form>

                            <button class="btn btn-danger px-5 py-3 rounded-pill hover-btn fs-6" data-bs-toggle="modal"
                                data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-2"></i> Từ chối bài viết
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Right: Images --}}
                <div class="col-lg-6 ps-lg-5">
                    {{-- Thumbnail --}}
                    @if ($post->thumbnail && file_exists(public_path('storage/' . $post->thumbnail)))
                        <h5 class="fw-bold mb-3 text-dark fs-5 animate__animated animate__fadeInUp">
                            <i class="bi bi-image-fill me-2"></i> Ảnh đại diện
                        </h5>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                            data-image="{{ asset('storage/' . $post->thumbnail) }}">
                            <img src="{{ asset('storage/' . $post->thumbnail) }}"
                                class="img-fluid rounded-4 shadow hover-scale mb-4"
                                style="width: 100%; object-fit: cover; max-height: 380px;" alt="Thumbnail">
                        </a>
                    @else
                        <p class="text-muted">Không có ảnh đại diện</p>
                    @endif

                    {{-- Gallery --}}
                    @if ($post->gallery)
                        @php
                            $gallery = json_decode($post->gallery, true);
                            $gallery = is_array($gallery) ? $gallery : [];
                        @endphp
                        <h5 class="fw-bold mb-3 text-dark fs-5 animate__animated animate__fadeInUp">
                            <i class="bi bi-images me-2"></i> Album ảnh
                        </h5>
                        @if (!empty($gallery))
                            <div class="row row-cols-2 g-4">
                                @foreach ($gallery as $index => $image)
                                    @if (file_exists(public_path('storage/' . $image)))
                                        <div class="col animate__animated animate__zoomIn"
                                            style="animation-delay: {{ $loop->index * 0.1 }}s">
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                                data-image="{{ asset('storage/' . $image) }}">
                                                <img src="{{ asset('storage/' . $image) }}"
                                                    class="img-fluid rounded-4 shadow-sm hover-scale"
                                                    style="object-fit: cover; height: 180px; width: 100%;"
                                                    alt="Gallery image {{ $index + 1 }}">
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-muted">Ảnh không tồn tại: {{ $image }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Không có ảnh trong album</p>
                        @endif
                    @else
                        <p class="text-muted">Không có album ảnh</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xxl modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <img src="" id="modalImage" class="img-fluid rounded-4 w-100" style="object-fit: contain;">
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('landlord.posts.approval.reject', $post) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Lý do từ chối bài viết</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="rejected_reason" class="form-control" rows="4" required placeholder="Nhập lý do từ chối..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imageSrc = button.getAttribute('data-image');
                const modalImage = imageModal.querySelector('#modalImage');
                modalImage.src = imageSrc;
            });
        });
    </script>
    <style>
        .bg-gradient-light {
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }

        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
        }

        .hover-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endsection
