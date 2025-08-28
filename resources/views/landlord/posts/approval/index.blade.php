@extends('landlord.layouts.app')

@section('content')
    <div class="container py-4 py-md-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark fs-4 mb-0">
                <i class="bi bi-file-text-fill me-2 text-primary"></i> Quản lý bài viết
            </h4>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border border-success-subtle rounded" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4 border-0 bg-white shadow-sm rounded">
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'all']) }}"
                    class="nav-link {{ request()->tab == 'all' || !request()->tab ? 'active' : '' }} fw-medium px-3 py-2 text-dark"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-list me-1"></i> Tất cả
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'pending']) }}"
                    class="nav-link {{ request()->tab == 'pending' ? 'active' : '' }} fw-medium px-3 py-2 text-dark"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'approved']) }}"
                    class="nav-link {{ request()->tab == 'approved' ? 'active' : '' }} fw-medium px-3 py-2 text-dark"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> Đã duyệt
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'rejected']) }}"
                    class="nav-link {{ request()->tab == 'rejected' ? 'active' : '' }} fw-medium px-3 py-2 text-dark"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-x-circle-fill me-1"></i> Từ chối
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'hidden']) }}"
                    class="nav-link {{ request()->tab == 'hidden' ? 'active' : '' }} fw-medium px-3 py-2 text-dark"
                    style="font-size: 0.9rem;">
                    <i class="bi bi-eye-slash me-1"></i> Bài ẩn
                </a>
            </li>
        </ul>

        <!-- Table Card -->
        <div class="card border-0 shadow-sm rounded bg-white">
            <div class="card-body p-0">
                @if (request()->tab == 'all')
                    @include('landlord.posts.approval.partials.list', [
                        'posts' => $allPosts,
                        'type' => 'all',
                    ])
                    {{ $allPosts->appends(['tab' => 'all'])->links('vendor.pagination.bootstrap-5') }}
                @elseif (request()->tab == 'approved')
                    @include('landlord.posts.approval.partials.list', [
                        'posts' => $approvedPosts,
                        'type' => 'approved',
                    ])
                    {{ $approvedPosts->appends(['tab' => 'approved'])->links('vendor.pagination.bootstrap-5') }}
                @elseif (request()->tab == 'rejected')
                    @include('landlord.posts.approval.partials.list', [
                        'posts' => $rejectedPosts,
                        'type' => 'rejected',
                    ])
                    {{ $rejectedPosts->appends(['tab' => 'rejected'])->links('vendor.pagination.bootstrap-5') }}
                @elseif (request()->tab == 'hidden')
                    @include('landlord.posts.approval.partials.list', [
                        'posts' => $hiddenPosts,
                        'type' => 'hidden',
                    ])
                    {{ $hiddenPosts->appends(['tab' => 'hidden'])->links('vendor.pagination.bootstrap-5') }}
                @else
                    @include('landlord.posts.approval.partials.list', [
                        'posts' => $pendingPosts,
                        'type' => 'pending',
                    ])
                    {{ $pendingPosts->appends(['tab' => 'pending'])->links('vendor.pagination.bootstrap-5') }}
                @endif
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="rejectForm" method="POST" class="ajax-form">
                    @csrf
                    <div class="modal-content rounded shadow-sm">
                        <div class="modal-header bg-light border-0">
                            <h6 class="modal-title fs-6 fw-semibold text-dark">📌 Lý do từ chối bài viết</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <select name="rejected_reason" id="rejected_reason" class="form-select form-select-sm mb-3"
                                onchange="toggleCustomReason(this)">
                                <option value="">Chọn lý do...</option>
                                <option value="Nội dung không phù hợp">Nội dung không phù hợp</option>
                                <option value="Hình ảnh không đạt yêu cầu">Hình ảnh không đạt yêu cầu</option>
                                <option value="Thông tin không đầy đủ">Thông tin không đầy đủ</option>
                                <option value="Khác">Khác</option>
                            </select>
                            <textarea name="rejected_reason_detail" id="rejected_reason_detail" class="form-control form-control-sm" rows="3"
                                placeholder="Nhập lý do chi tiết (tùy chọn)..." style="display: none;"></textarea>
                        </div>
                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-sm btn-danger">Xác nhận từ chối</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .nav-tabs {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .nav-tabs .nav-link {
            transition: background-color 0.2s ease, color 0.2s ease;
            border-radius: 0.5rem;
        }

        .nav-tabs .nav-link:hover {
            background-color: #e9ecef;
        }

        .nav-tabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-radius: 0.5rem;
            animation: slideIn 0.3s ease-in-out;
        }

        .card {
            transition: transform 0.1s ease, box-shadow 0.1s ease;
            border-radius: 0.5rem;
            will-change: transform, box-shadow;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 0.5rem;
            transition: opacity 0.2s ease;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .nav-tabs .nav-link {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }

            h4 {
                font-size: 1.25rem;
            }
        }

        /* Tắt hiệu ứng nhấp nháy khi bấm nút */
        button:focus,
        button:active {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection

@section('scripts')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý form ẩn bài
            document.querySelectorAll('.hide-post-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const postId = form.dataset.postId;
                    const modal = bootstrap.Modal.getInstance(document.getElementById('hideModal-' +
                        postId));
                    const formData = new FormData(form);
                    const actionUrl = form.action;

                    // Đợi modal đóng hoàn toàn trước khi gửi yêu cầu
                    modal._element.addEventListener('hidden.bs.modal', function() {
                        fetch(actionUrl, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'input[name="_token"]').value
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Xóa dòng bài viết
                                    const row = form.closest('tr');
                                    row.style.transition = 'opacity 0.3s ease';
                                    row.style.opacity = '0';
                                    setTimeout(() => row.remove(), 300);
                                    showToast('Bài viết đã được ẩn thành công!',
                                        'success');
                                } else {
                                    showToast(data.message ||
                                        'Có lỗi xảy ra, vui lòng thử lại.', 'danger'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                            });
                    }, {
                        once: true
                    });
                    modal.hide();
                });
            });

            // Xử lý form từ chối
            const rejectModal = document.getElementById('rejectModal');
            rejectModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const form = document.getElementById('rejectForm');
                form.action = action;
            });

            document.getElementById('rejectForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                const actionUrl = this.action;

                fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(rejectModal);
                            modal.hide();
                            const row = document.querySelector(`tr[data-post-id="${data.post_id}"]`);
                            if (row) {
                                row.style.transition = 'opacity 0.3s ease';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 300);
                            }
                            showToast('Bài viết đã được từ chối thành công!', 'success');
                        } else {
                            showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Có lỗi xảy ra, vui lòng thử lại.', 'danger');
                    });
            });

            // Hàm hiển thị toast thông báo
            function showToast(message, type) {
                const toastContainer = document.createElement('div');
                toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                toastContainer.style.zIndex = '1050';
                toastContainer.innerHTML = `
                    <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toastContainer);
                const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
                toast.show();
                setTimeout(() => toastContainer.remove(), 3000);
            }

            // Xử lý toggle textarea cho lý do từ chối
            window.toggleCustomReason = function(select) {
                const textarea = document.getElementById('rejected_reason_detail');
                textarea.style.display = select.value === 'Khác' ? 'block' : 'none';
                textarea.required = select.value === 'Khác';
            };
        });
    </script>
@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
