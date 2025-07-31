@extends('landlord.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h4 class="fw-bold mb-0 text-dark fs-2">
                <i class="bi bi-file-text-fill me-2 text-primary"></i> Quản lý bài viết
            </h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert"
                style="animation: slideIn 0.3s ease-in-out;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabs -->
        <ul class="nav nav-tabs nav-tabs-bordered mb-5 border-0 shadow-sm rounded-3 bg-white">
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'pending']) }}"
                    class="nav-link {{ request()->tab == 'pending' || !request()->tab ? 'active' : '' }} fs-6 fw-semibold px-3 py-2 text-truncate"
                    style="max-width: 150px;">
                    <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'approved']) }}"
                    class="nav-link {{ request()->tab == 'approved' ? 'active' : '' }} fs-6 fw-semibold px-3 py-2 text-truncate"
                    style="max-width: 150px;">
                    <i class="bi bi-check-circle-fill me-1"></i> Đã duyệt
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'rejected']) }}"
                    class="nav-link {{ request()->tab == 'rejected' ? 'active' : '' }} fs-6 fw-semibold px-3 py-2 text-truncate"
                    style="max-width: 150px;">
                    <i class="bi bi-x-circle-fill me-1"></i> Từ chối
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('landlord.posts.approval.index', ['tab' => 'hidden']) }}"
                    class="nav-link {{ request()->tab == 'hidden' ? 'active' : '' }} fs-6 fw-semibold px-3 py-2 text-truncate"
                    style="max-width: 150px;">
                    <i class="bi bi-eye-slash me-1"></i> Bài ẩn
                </a>
            </li>
        </ul>

        <div class="card shadow-sm rounded-3 border-0 p-4 bg-white">
            @if (request()->tab == 'approved')
                @include('landlord.posts.approval.partials.list', [
                    'posts' => $approvedPosts,
                    'type' => 'approved',
                ])
            @elseif (request()->tab == 'rejected')
                @include('landlord.posts.approval.partials.list', [
                    'posts' => $rejectedPosts,
                    'type' => 'rejected',
                ])
            @elseif (request()->tab == 'hidden')
                @include('landlord.posts.approval.partials.list', [
                    'posts' => $hiddenPosts,
                    'type' => 'hidden',
                ])
            @else
                @include('landlord.posts.approval.partials.list', [
                    'posts' => $pendingPosts,
                    'type' => 'pending',
                ])
            @endif
        </div>
    </div>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fe;
        }

        .container {
            max-width: 1400px;
        }

        .nav-tabs {
            background-color: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .nav-tabs .nav-link {
            color: #64748b;
            transition: all 0.3s ease;
            border: none;
            padding: 1rem 2rem;
        }

        .nav-tabs .nav-link:hover {
            background-color: #f0f4ff;
            color: #3b82f6;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background-color: #e6fffa;
            color: #0d9488;
            border: 2px solid #14b8a6;
            border-radius: 0.75rem;
            padding: 1.25rem;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .alert-success .btn-close {
            filter: opacity(0.6);
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 1rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        h4 {
            color: #1e3a8a;
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
                padding: 0.75rem 1.25rem;
                font-size: 1rem;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            h4 {
                font-size: 1.5rem;
            }
        }
    </style>
@endsection
