<?php use Illuminate\Support\Facades\Auth;
$user = Auth::user(); ?>

<div class="container-fluid">
    <div id="two-column-menu"></div>
    <ul class="navbar-nav" id="navbar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="{{ route('landlords.dashboard') }}">
                <i class="mdi mdi-speedometer"></i>
                <span data-key="t-dashboards">Tổng Quan</span>
            </a>
        </li>

        <!-- Property Management -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarProperty" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarProperty">
                    <i class="mdi mdi-home-city"></i>
                    <span data-key="t-properties">Quản lý Bất Động Sản</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarProperty">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.properties.list') }}" class="nav-link">Danh sách Bất Động
                                Sản</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
<!-- Room Management Menu -->
@if ($user->role === 'Landlord')
    <li class="nav-item">
        <a class="nav-link menu-link" href="#sidebarRoom" data-bs-toggle="collapse" role="button"
            aria-expanded="false" aria-controls="sidebarRoom">
            <i class="mdi mdi-view-grid-plus-outline"></i>
            <span data-key="t-apps">Quản Lý Phòng</span>
        </a>
        <div class="collapse menu-dropdown" id="sidebarRoom">
            <ul class="nav nav-sm flex-column">
                @if ($user->role === 'Landlord')
                    <li class="nav-item"><a href="{{ route('landlords.rooms.index') }}" class="nav-link">Danh sách phòng</a></li>
                    <li class="nav-item"><a href="{{ route('landlords.services.index') }}" class="nav-link">Danh sách dịch vụ</a></li>
                    <li class="nav-item"><a href="{{ route('landlords.facilities.index') }}" class="nav-link">Danh sách tiện nghi</a></li>

                @endif

                @if ($user->role === 'Staff')
                    <li class="nav-item"><a href="{{ route('landlords.staff.index') }}" class="nav-link">Quản lý BĐS dành cho nhân viên</a></li>
                    <li class="nav-item"><a href="{{ route('landlord.staff.complaints.index') }}" class="nav-link">Tiếp nhận khiếu nại</a></li>
                    <li class="nav-item"><a href="{{ route('staff.posts.index') }}" class="nav-link">Đăng bài</a></li>
                    <li class="nav-item"><a href="{{ route('landlord.staff.complaints.history') }}" class="nav-link">Lịch sử xử lý khiếu nại</a></li>
                @endif
            </ul>
        </div>
    </li>
@endif

        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarApprovals" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarApprovals">
                    <i class="mdi mdi-check-decagram-outline"></i>
                    <span data-key="t-approvals">Phê Duyệt</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarApprovals">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.index') }}" class="nav-link">Phê duyệt hợp đồng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.users.index') }}" class="nav-link">Phê duyệt người dùng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.room_edit_requests.index') }}" class="nav-link">Yêu cầu chỉnh sửa phòng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlord.posts.approval.index') }}" class="nav-link">Duyệt bài đăng</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Post -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarPost" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarPost">
                <i class="mdi mdi-file-document-outline"></i>
                <span data-key="t-post">Quản Lý Bài Đăng</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarPost">
                <ul class="nav nav-sm flex-column">
                    @if ($user->role === 'Staff')
                        <li class="nav-item">
                            <a href="{{ route('staff.posts.index') }}" class="nav-link">Đăng bài</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('staff.categories.index') }}" class="nav-link">Danh mục bài đăng</a>
                        </li>
                    @endif
                    @if ($user->role === 'Landlord')
                     <li class="nav-item">
                            <a href="{{ route('staff.categories.index') }}" class="nav-link">Danh mục bài đăng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlord.posts.approval.index') }}" class="nav-link">Duyệt bài đăng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('staff.posts.index') }}" class="nav-link">Đăng bài</a>
                        </li>
                    @endif
                </ul>
            </div>
        </li>

  






        <!-- Bank Accounts -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarBank" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarBank">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Tài khoản ngân hàng</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarBank">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.bank_accounts.index') }}"
                                class="nav-link">Tất Cả Tài Khoản</a></li>
                        <li class="nav-item"><a href="{{ route('landlords.bank_accounts.assign') }}"
                                class="nav-link">Phân công cho tài sản</a></li>
                    </ul>
                </div>
            </li>
        @endif
             @if ($user->role === 'Landlord')
          <!-- Staff Accounts -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarStaff" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarStaff">
                    <i class="mdi mdi-account-group"></i>
                    <span data-key="t-staff">Tài Khoản Nhân Viên</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarStaff">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.staff_accounts.index') }}"
                                class="nav-link">Tất cả tài khoản nhân viên</a></li>
                    </ul>
                </div>
            </li>
              @endif
        <!-- Room Management Menu for Staff -->
        @if ($user->role === 'Staff')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarRoomStaff" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarRoomStaff">
                    <i class="mdi mdi-view-grid-plus-outline"></i>
                    <span data-key="t-apps">Quản Lý Phòng</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarRoomStaff">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.staff.index') }}" class="nav-link">Quản lý
                                BĐS dành cho nhân viên</a></li>
                    </ul>
                </div>
            </li>

            <!-- Complaints (Staff) -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarComplaintsStaff" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarComplaintsStaff">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span data-key="t-complaints">Khiếu nại</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarComplaintsStaff">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlord.staff.complaints.index') }}"
                                class="nav-link">Tiếp nhận khiếu nại</a></li>
                        <li class="nav-item"><a href="{{ route('landlord.staff.complaints.history') }}"
                                class="nav-link">Lịch sử xử lý khiếu nại</a></li>
                    </ul>
                </div>
            </li>


            <!-- Post (Staff) -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarPostStaff" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarPostStaff">
                    <i class="mdi mdi-file-document"></i>
                    <span data-key="t-posts">Đăng bài</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarPostStaff">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('staff.posts.index') }}" class="nav-link">Danh
                                sách bài viết của bạn</a></li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Leave Room -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarLeaveRoom" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarLeaveRoom">
                    <i class="ri-door-open-line"></i>
                    <span data-key="t-leave-room">Rời Phòng</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarLeaveRoom">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlord.roomleave.index') }}" class="nav-link">Yêu cầu rời phòng</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlord.roomleave.processed') }}" class="nav-link">Lịch sử duyệt</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Bills -->
        @if ($user->role === 'Staff')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#bills" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="bills">
                    <i class="mdi mdi-receipt"></i>
                    <span data-key="t-bank">Hoá đơn</span>
                </a>
                <div class="collapse menu-dropdown" id="bills">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff.payment.list') }}" class="nav-link">Danh sách hoá
                                đơn</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#billsC" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="billsC">
                    <i class="mdi mdi-file-document-outline"></i>
                    <span data-key="t-bank">Hoá đơn (chủ)</span>
                </a>
                <div class="collapse menu-dropdown" id="billsC">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.bills.index') }}" class="nav-link">Danh sách hoá đơn</a>
                        </li>
                    </ul>
                     
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.payment.list') }}"
                         class="nav-link">Nhập hoá đơn</a></li>
                    </ul>
               
                </div>
            </li>

          
        @endif
        
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarBooking" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarBooking">
                    <i class="mdi mdi-calendar-clock"></i>
                    <span data-key="t-booking">Đặt Lịch xem phòng</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarBooking">
                    <ul class="nav nav-sm flex-column">
                        @if ($user->role === 'Landlord')
                            <li class="nav-item">
                                <a href="{{ route('landlord.bookings.index') }}" class="nav-link">Quản lý đặt lịch</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>
        @endif
            @if ($user->role === 'Staff')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarBookingStaff" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarBookingStaff">
                        <i class="mdi mdi-calendar-clock"></i>
                        <span data-key="t-booking">Đặt lịch & Hợp đồng</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarBookingStaff">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('booking.index') }}" class="nav-link">Quản lý đặt lịch</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('landlord.staff.chart.index') }}" class="nav-link">Biểu đồ đặt lịch</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('staff.contract.renewals.index') }}" class="nav-link">Tiếp nhận tái ký</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('landlord.staff.roomleave.index') }}" class="nav-link">
                                    Quản lý yêu cầu rời sửa phòng
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        <!-- Complaints (Landlord) -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarComplaints" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarComplaints">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span data-key="t-complaints">Khiếu nại</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarComplaints">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlord.complaints.index') }}" class="nav-link">Danh sách khiếu
                                nại</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif
    </ul>
</div>
