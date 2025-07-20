<nav id="navigation" class="navigation navigation-landscape">
    <div class="nav-header">
        <a class="nav-brand static-logo" href="#"><img src="{{ asset('assets/client/img/logo.png') }}" class="logo" alt="" /></a>
        <a class="nav-brand fixed-logo" href="#"><img src="{{ asset('assets/client/img/logo.png') }}" class="logo" alt="" /></a>
        <div class="nav-toggle"></div>
    </div>

    <div class="nav-menus-wrapper" style="transition-property: none;">
        <ul class="nav-menu">
            <li class="active"><a href="#">Home<span class="submenu-indicator"></span></a>
                <ul class="nav-dropdown nav-submenu">
                    <li><a href="index.html">Home 1</a></li>
                </ul>
            </li>

            <li><a href="#">Listings<span class="submenu-indicator"></span></a>
                <ul class="nav-dropdown nav-submenu">
                    <li><a href="#">Listing Grid<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="grid-layout-with-sidebar.html">Grid Style 1</a></li>
                            <li><a href="grid-layout-2.html">Grid Style 2</a></li>
                            <li><a href="grid-layout-3.html">Grid Style 3</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li><a href="#">Property<span class="submenu-indicator"></span></a>
                <ul class="nav-dropdown nav-submenu">
                    <li><a href="#">User Admin<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="dashboard.html">User Dashboard</a></li>
                            <li><a href="my-profile.html">My Profile</a></li>
                            <li><a href="my-property.html">My Property</a></li>
                            <li><a href="messages.html">Messages</a></li>
                            <li><a href="bookmark-list.html">Bookmark Property</a></li>
                            <li><a href="submit-property.html">Submit Property</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Single Property</a></li>
                    <li><a href="compare-property.html">Compare Property</a></li>
                </ul>
            </li>

            <li><a href="#">Pages<span class="submenu-indicator"></span></a>
                <ul class="nav-dropdown nav-submenu">
                    <li><a href="blog.html">Blog Style</a></li>
                </ul>
            </li>
        </ul>

        {{-- Phần bên phải menu: thông báo, avatar, đăng nhập --}}
        <ul class="nav-menu nav-menu-social align-to-right">
            @auth
                @php
                    $unreadCount = auth()->user()->customNotifications()->wherePivot('is_read', false)->count();
                    $latest = auth()->user()->customNotifications()
                                           ->orderByDesc('notification_user.received_at')
                                           ->take(5)
                                           ->get();
                @endphp

                <li class="dropdown ms-2">
                    <a href="#" class="text-white d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}"
                             class="rounded-circle" width="32" height="32" alt="avatar">
                        <span class="ms-2">{{ Auth::user()->name }}</span>
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="dropdown-header fw-bold">🔔 Thông báo</li>
                        @forelse($latest as $n)
                            <li>
                                <a class="dropdown-item small" href="{{ $n->link }}"
                                   onclick="event.preventDefault(); document.getElementById('nav-read-{{ $n->id }}').submit();">
                                    <div class="@if(!$n->pivot->is_read) fw-bold @endif">
                                        {{ Str::limit($n->title, 40) }}<br>
                                        <small class="text-muted">{{ Str::limit($n->message, 60) }}</small>
                                        <small class="text-muted d-block">{{ \Carbon\Carbon::parse($n->pivot->received_at)->diffForHumans() }}</small>
                                    </div>
                                </a>
                                <form id="nav-read-{{ $n->id }}" action="{{ route('notifications.read', $n->id) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @empty
                            <li><span class="dropdown-item text-muted small">Không có thông báo mới</span></li>
                        @endforelse
                        <li>
    <a class="dropdown-item text-primary text-center small fw-bold" href="{{ route('notifications.index') }}">
        📋 Xem tất cả thông báo
    </a>
</li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('tenant.profile.edit') }}">👤 Thông tin cá nhân</a></li>
                        <li><a class="dropdown-item" href="{{ route('home.favorites') }}">❤️ Trọ đã yêu thích</a></li>
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
                    <a href="{{ route('auth.login') }}" class="text-white bg-success px-3 py-2 rounded">
                        <i class="fas fa-sign-in-alt me-1"></i><span class="dn-lg">Đăng nhập người dùng</span>
                    </a>
                </li>
                <li class="add-listing ms-2">
                    <a href="{{ route('auth.register') }}" class="text-white bg-danger px-3 py-2 rounded">
                        <i class="fas fa-plus-circle me-1"></i>Đăng ký chủ trọ
                    </a>
                </li>
            @endauth
        </ul>
    </div>
</nav>
