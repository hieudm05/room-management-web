<style>
    /* Hover vào chuông hoặc avatar */
    /* Khi hover chuông hoặc avatar - dùng tone đỏ */


    /* Hover vào tên -> đỏ */
    .nav-menu-social .dropdown a:hover .user-name {
        color: #C62828;
        /* Đỏ đậm */
        transition: color 0.3s ease;
    }

    /* Khi cuộn xuống, tone đen + đỏ nhẹ */
    .navigation.scrolled .user-name,
    .navigation.scrolled .dropdown a {
        color: #C62828 !important;
        /* giữ tone đỏ khi scroll */
    }


    /* Ban đầu trên nền tối: trắng */
    .navigation:not(.scrolled) .user-name,
    .navigation:not(.scrolled) .dropdown a {
        /* color: #fff !important; */
    }

    .navigation:not(.scrolled) .user-avatar,
    .navigation:not(.scrolled) i.fa-bell {
        filter: invert(100%);
    }
</style>
<nav id="navigation" class="navigation navigation-landscape">
    <div class="nav-header">
        <a class="nav-brand static-logo" href="{{ url('/') }}">
            <img src="{{ asset('assets/client/img/logoL.png') }}" class="logo" alt="" />
        </a>
        <a class="nav-brand fixed-logo" href="#">
            <img src="{{ asset('assets/client/img/logoD.png') }}" class="logo" alt="" />
            
        </a>
        <div class="nav-toggle"></div>
    </div>

    <div class="nav-menus-wrapper" style="transition-property: none;">
        <ul class="nav-menu" style="margin-top: 9px;" >
            <li class="active"><a href="#">Trang chủ</a></li>
            <li class="active"><a href="#">Trang giới thiệu</a></li>
            <li class="active"><a href="#">Bài viết</a></li>
            <li class="active"><a href="#">Liên hệ</a></li>
        </ul>

        {{-- Phần bên phải: thông báo + avatar --}}
        <ul class="nav-menu nav-menu-social align-to-right">
            @auth
                @php
                    $unreadCount = auth()->user()->customNotifications()->wherePivot('is_read', false)->count();
                    $latest = auth()
                        ->user()
                        ->customNotifications()
                        ->orderByDesc('notification_user.received_at')
                        ->take(5)
                        ->get();
                @endphp

                <li class="dropdown d-flex align-items-center gap-3 ms-2">

                    {{-- Chuông thông báo --}}
                    <a href="#" class="position-relative" data-bs-toggle="dropdown" id="notificationDropdown"
                        aria-expanded="false">
                        <i class="fas fa-bell fa-lg text-dark"></i>

                        @if ($unreadCount > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger text-white borde"
                                style="font-size: 0.7rem; width: 1.6em; height: 1.6em; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 2px white;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                <span class="visually-hidden">thông báo chưa đọc</span>
                            </span>
                        @endif
                    </a>


                    {{-- Danh sách thông báo --}}

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="notificationDropdown"
                        style="min-width: 300px;">
                        <li class="dropdown-header fw-bold">🔔 Thông báo gần đây</li>
                        @forelse($latest as $n)
                            <li>
                                <a class="dropdown-item small" href="{{ $n->link }}"
                                    onclick="event.preventDefault(); document.getElementById('nav-read-{{ $n->id }}').submit();">
                                    <div class="@if (!$n->pivot->is_read) fw-bold @endif">
                                        {{ Str::limit($n->title, 40) }}
                                        <br><small class="text-muted">{{ Str::limit($n->message, 50) }}</small>
                                        <br><small
                                            class="text-muted">{{ \Carbon\Carbon::parse($n->pivot->received_at)->diffForHumans() }}</small>
                                    </div>
                                </a>
                                <form id="nav-read-{{ $n->id }}" action="{{ route('notifications.read', $n->id) }}"
                                    method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @empty
                            <li><span class="dropdown-item text-muted small">Không có thông báo mới</span></li>
                        @endforelse
                        <li>
                            <a class="dropdown-item text-center small text-primary fw-bold"
                                href="{{ route('notifications.index') }}">
                                📋 Xem tất cả
                            </a>
                        </li>
                    </ul>

                    {{-- Avatar + tên người dùng --}}
                    <a href="#" class="user-dropdown d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('assets/images/default-avatar.jpg') }}"
                            class="user-avatar rounded-circle" width="32" height="32" alt="avatar"
                            style="object-fit: cover;">
                        <span class="ms-2 user-name d-none d-lg-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('tenant.profile.edit') }}">👤 Thông tin cá nhân</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('renter.addUserRequest.create') }}">📋 Thêm các thành
                                viên</a></li>
                        <li><a class="dropdown-item" href="{{ route('home.favorites') }}">❤️ Trọ đã yêu thích</a></li>
                        <li><a class="dropdown-item" href="{{ route('user.bookings.index') }}">📝 Đặt lịch hẹn</a></li>
                        <li><a class="dropdown-item" href="{{ route('home.complaints.create') }}">📝 Đơn khiếu nại</a></li>
                        <li><a class="dropdown-item" href="{{ route('my-room') }}">🏠 Phòng của tôi</a></li>
                        <li><a class="dropdown-item" href="{{ route('auth.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                🚪 Đăng xuất</a></li>
                        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>
            @else
                <li>
                    <a href="{{ route('auth.login') }}" class="text-white bg-danger px-3 py-2 rounded">
                        <i class="fas fa-sign-in-alt me-1"></i><span class="dn-lg">Đăng nhập</span>
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</nav>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nav = document.getElementById("navigation");
        window.addEventListener("scroll", function() {
            if (window.scrollY > 20) {
                nav.classList.add("scrolled");
            } else {
                nav.classList.remove("scrolled");
            }
        });
    });
</script>
