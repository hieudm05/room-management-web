 <?php use Illuminate\Support\Facades\Auth;
$user = Auth::user(); ?>
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                           Trang Quản Trị
                        </span>
                        <span class="logo-lg">
                           Trang Quản Trị
                        </span>
                    </a>

                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                           Trang Quản Trị
                        </span>
                        <span class="logo-lg">
                            Trang Quản Trị
                        </span>
                    </a>
                </div>


                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
            </div>

            <div class="d-flex align-items-center">

                @php
                    use Illuminate\Support\Str;

                    $user = auth()->user();
                    $userType = $user->role;

                    $notifications = $user
                        ->customNotifications()
                        ->orderByDesc('notification_user.received_at') // hoặc 'notifications.created_at'
                        ->take(5)
                        ->get();

                    $unreadCount = $user->customNotifications()->wherePivot('is_read', false)->count();
                @endphp


                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        @if ($unreadCount > 0)
                            <span
                                class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div
                            class="p-3 border-bottom border-bottom-dashed d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold">Thông báo</h6>
                            <span class="badge bg-danger-subtle text-danger">{{ $unreadCount }} mới</span>
                        </div>

                        <div data-simplebar style="max-height: 300px;" class="pe-2">
                            @forelse($notifications as $n)
                                <a href="{{ $n->link }}" class="dropdown-item d-flex align-items-start"
                                    onclick="event.preventDefault(); document.getElementById('dropdown-read-{{ $n->id }}').submit();">
                                    <div class="flex-grow-1">
                                        <h6 class="mt-0 mb-1 @if (!$n->pivot->is_read) fw-bold @endif">
                                            {{ Str::limit($n->title, 40) }}
                                        </h6>
                                        <div class="text-muted fs-13">{{ Str::limit($n->message, 60) }}</div>
                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                            <i class="mdi mdi-clock-outline"></i>
                                            {{ \Carbon\Carbon::parse($n->pivot->received_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                                <form id="dropdown-read-{{ $n->id }}"
                                    action="{{ route(($userType === 'staff' ? 'staff' : 'landlord') . '.notifications.read', $n->id) }}"
                                    method="POST" class="d-none">
                                    @csrf
                                </form>
                            @empty
                                <div class="text-center p-3 text-muted">Không có thông báo mới</div>
                            @endforelse
                        </div>

                        <div class="p-2 border-top border-top-dashed text-center">
                            <a class="btn btn-soft-primary btn-sm"
                                href="{{ route(($userType === 'staff' ? 'staff' : 'landlord') . '.notifications.index') }}">
                                Xem tất cả thông báo <i class="ri-arrow-right-line align-middle"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn shadow-none" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{$user->name}}</span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">{{$user->role === "Landlord" ? "Chủ trọ" : "Nhân viên"}}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a class="dropdown-item" href="{{ route('auth.logout') }}">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle" data-key="t-logout">Logout</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
