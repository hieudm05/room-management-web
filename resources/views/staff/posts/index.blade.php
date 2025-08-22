@extends('landlord.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold text-primary display-5">
                <i class="bi bi-list-ul me-2"></i> Danh sách bài đăng
            </h2>
            <a href="{{ Auth::user()->role === 'Landlord' ? route('landlord.posts.create') : route('staff.posts.create') }}"
                class="btn btn-gradient-primary px-5 py-3 rounded-pill fw-bold">
                <i class="bi bi-plus-circle-fill me-2"></i> Tạo bài đăng mới
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-lg rounded-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4 border-0" id="postsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-dark" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                    type="button" role="tab" aria-controls="all" aria-selected="true">
                    <i class="bi bi-list-ul me-2"></i> Tất cả
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab" aria-controls="pending" aria-selected="false">
                    <i class="bi bi-hourglass-split me-2"></i> Chờ duyệt
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved"
                    type="button" role="tab" aria-controls="approved" aria-selected="false">
                    <i class="bi bi-check-circle-fill me-2"></i> Đã duyệt
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-dark" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected"
                    type="button" role="tab" aria-controls="rejected" aria-selected="false">
                    <i class="bi bi-x-circle-fill me-2"></i> Từ chối
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="postsTabContent">
            <!-- All Posts Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                @if ($posts->count() > 0)
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Tiêu đề</th>
                                        <th>Giá thuê</th>
                                        <th>Diện tích</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts as $key => $post)
                                        <tr>
                                            <td class="ps-4">{{ $posts->firstItem() + $key }}</td>
                                            <td>{{ Str::limit($post->title, 40) }}</td>
                                            <td>{{ number_format((float) $post->price, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ $post->area }} m²</td>
                                            <td>
                                                @if ($post->status == 0)
                                                    <span class="badge bg-warning text-dark px-3 py-2">Chờ duyệt</span>
                                                @elseif ($post->status == 1)
                                                    <span class="badge bg-success px-3 py-2">Đã duyệt</span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-2">Từ chối</span>
                                                @endif
                                            </td>
                                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.posts.show', $post->post_id) }}"
                                                        class="btn btn-outline-primary btn-sm rounded-start-pill">
                                                        <i class="bi bi-eye-fill me-1"></i> Xem
                                                    </a>
                                                    @if ($post->status == 0 || $post->status == 2)
                                                        <a href="{{ route('staff.posts.edit', $post->post_id) }}"
                                                            class="btn btn-outline-warning btn-sm">
                                                            <i class="bi bi-pencil-fill me-1"></i> Sửa
                                                        </a>
                                                    @endif
                                                    @if ($post->status == 0)
                                                        <form action="{{ route('staff.posts.destroy', $post->post_id) }}"
                                                            method="POST" class="d-inline-block"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-sm rounded-end-pill">
                                                                <i class="bi bi-trash3-fill me-1"></i> Xóa
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="alert alert-info shadow-lg rounded-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> Không có bài đăng nào.
                    </div>
                @endif
            </div>

            <!-- Pending Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                @if ($posts->where('status', 0)->count() > 0)
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Tiêu đề</th>
                                        <th>Giá thuê</th>
                                        <th>Diện tích</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts->where('status', 0) as $key => $post)
                                        <tr>
                                            <td class="ps-4">{{ $key + 1 }}</td>
                                            <td>{{ Str::limit($post->title, 40) }}</td>
                                            <td>{{ number_format((float) $post->price, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ $post->area }} m²</td>
                                            <td><span class="badge bg-warning text-dark px-3 py-2">Chờ duyệt</span></td>
                                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.posts.show', $post->post_id) }}"
                                                        class="btn btn-outline-primary btn-sm rounded-start-pill">
                                                        <i class="bi bi-eye-fill me-1"></i> Xem
                                                    </a>
                                                    <a href="{{ route('staff.posts.edit', $post->post_id) }}"
                                                        class="btn btn-outline-warning btn-sm">
                                                        <i class="bi bi-pencil-fill me-1"></i> Sửa
                                                    </a>
                                                    <form action="{{ route('staff.posts.destroy', $post->post_id) }}"
                                                        method="POST" class="d-inline-block"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm rounded-end-pill">
                                                            <i class="bi bi-trash3-fill me-1"></i> Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info shadow-lg rounded-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> Không có bài đăng nào đang chờ duyệt.
                    </div>
                @endif
            </div>

            <!-- Approved Tab -->
            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                @if ($posts->where('status', 1)->count() > 0)
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Tiêu đề</th>
                                        <th>Giá thuê</th>
                                        <th>Diện tích</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts->where('status', 1) as $key => $post)
                                        <tr>
                                            <td class="ps-4">{{ $key + 1 }}</td>
                                            <td>{{ Str::limit($post->title, 40) }}</td>
                                            <td>{{ number_format((float) $post->price, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ $post->area }} m²</td>
                                            <td><span class="badge bg-success px-3 py-2">Đã duyệt</span></td>
                                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('staff.posts.show', $post->post_id) }}"
                                                    class="btn btn-outline-primary btn-sm rounded-pill">
                                                    <i class="bi bi-eye-fill me-1"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info shadow-lg rounded-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> Không có bài đăng nào đã được duyệt.
                    </div>
                @endif
            </div>

            <!-- Rejected Tab -->
            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                @if ($posts->where('status', 2)->count() > 0)
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Tiêu đề</th>
                                        <th>Giá thuê</th>
                                        <th>Diện tích</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($posts->where('status', 2) as $key => $post)
                                        <tr>
                                            <td class="ps-4">{{ $key + 1 }}</td>
                                            <td>{{ Str::limit($post->title, 40) }}</td>
                                            <td>{{ number_format((float) $post->price, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ $post->area }} m²</td>
                                            <td><span class="badge bg-danger px-3 py-2">Từ chối</span></td>
                                            <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('staff.posts.show', $post->post_id) }}"
                                                        class="btn btn-outline-primary btn-sm rounded-start-pill">
                                                        <i class="bi bi-eye-fill me-1"></i> Xem
                                                    </a>
                                                    <a href="{{ route('staff.posts.edit', $post->post_id) }}"
                                                        class="btn btn-outline-warning btn-sm rounded-end-pill">
                                                        <i class="bi bi-pencil-fill me-1"></i> Sửa
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info shadow-lg rounded-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> Không có bài đăng nào bị từ chối.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .btn-gradient-primary {
            background-color: orangered;
            color: #fff;
            border: none;
            border-radius: 3px;
            transition: all 0.2s ease-in-out;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-gradient-primary:hover {
            background-color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th,
        .table td {
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
            padding: 0.75rem 1.25rem;
            font-size: 0.95rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafd;
            transition: all 0.2s ease-in-out;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-weight: 600;
        }

        .card {
            transition: transform 0.2s ease-in-out;
            border-radius: 16px;
            border: 1px solid #f1f1f1;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .nav-tabs .nav-link:hover {
            border-bottom: 3px solid #4facfe;
            color: #4facfe;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #4facfe;
            color: #4facfe;
            background-color: #f8fafd;
            border-radius: 8px 8px 0 0;
        }

        .btn-outline-primary {
            border-color: #4facfe;
            color: #4facfe;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background: #4facfe;
            color: #fff;
        }

        .btn-outline-warning {
            border-color: #f0ad4e;
            color: #f0ad4e;
            font-weight: 600;
        }

        .btn-outline-warning:hover {
            background: #f0ad4e;
            color: #fff;
        }

        .btn-outline-danger {
            border-color: #ff5b5b;
            color: #ff5b5b;
            font-weight: 600;
        }

        .btn-outline-danger:hover {
            background: #ff5b5b;
            color: #fff;
        }
    </style>
@endsection
