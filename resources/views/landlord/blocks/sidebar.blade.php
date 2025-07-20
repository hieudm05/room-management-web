{{-- filepath: c:\laragon\www\room-management-web\resources\views\landlord\blocks\sidebar.blade.php --}}
<?php

use Illuminate\Support\Facades\Auth;

$user = Auth::user();
?>

<div class="container-fluid">
    <div id="two-column-menu"></div>
    <ul class="navbar-nav" id="navbar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="{{ route('landlords.dashboard') }}">

                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboards">Tổng Quan</span>

                <i class="mdi mdi-speedometer"></i>
                <span data-key="t-dashboards">Dashboards</span>

            </a>
        </li>

        <!-- Room Management Menu -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarRoom" data-bs-toggle="collapse" role="button"
               aria-expanded="false" aria-controls="sidebarRoom">
                <i class="mdi mdi-view-grid-plus-outline"></i>
                <span data-key="t-apps">Quản Lý Phòng</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarRoom">
                <ul class="nav nav-sm flex-column">

                    @if ($user->role === 'Landlord')
                        <li class="nav-item">

                            <a href="{{ route('landlords.properties.list') }}" class="nav-link">
                                Quản Lý Bất Động Sản
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.rooms.index') }}" class="nav-link">
                                Danh sách Phòng Tổng Quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.index') }}" class="nav-link">
                                Quản Lý Phê Duyệt Hợp Đồng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.users.index') }}" class="nav-link">
                                Quản Lý Phê Duyệt Người Dùng

                            <a href="{{ route('landlords.properties.list') }}" class="nav-link">Properties Management</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.rooms.index') }}" class="nav-link">Rooms Management</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.index') }}" class="nav-link">Approval</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.users.index') }}" class="nav-link">Approval User</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.room_edit_requests.index') }}" class="nav-link">
                                Phê duyệt yêu cầu chuyển phòng

                            </a>
                        </li>
                    @endif

                    @if ($user->role === 'Staff')
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff.index') }}" class="nav-link">
                                Quản lý bất động sản dành cho nhân viên
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff.complaints.index') }}" class="nav-link">
                                Nhân viên tiếp nhận khiếu nại
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
        </li>

        <!-- Bank Accounts (Landlord only) -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarBank" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarBank">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Tài khoản ngân hàng</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarBank">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.bank_accounts.index') }}" class="nav-link">
                                Tất Cả Tài Khoản Ngân Hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.bank_accounts.assign') }}" class="nav-link">
                                Phân công tài khoản ngân hàng cho tài sản
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Complaints -->
            <li class="nav-item">
                <a class="nav-link menu-link" href="{{ route('landlord.complaints.index') }}">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <span data-key="t-complaints">Khiếu nại</span>
                </a>
            </li>
        @endif

        <!-- Staff Accounts (Landlord only) -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarStaff" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarStaff">
                    <i class="mdi mdi-account-group"></i>
                    <span data-key="t-staff">Tài Khoản Nhân Viên</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarStaff">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff_accounts.index') }}" class="nav-link">
                                Tất cả tài khoản nhân viên
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        <!-- Bills (Staff only) -->
        @if ($user->role === 'Staff')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#bills" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="bills">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Hoá đơn</span>
                </a>
                <div class="collapse menu-dropdown" id="bills">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff.payment.index') }}" class="nav-link">
                                Danh sách hoá đơn
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

         @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#billsC" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="bills">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Hoá đơn (chủ)</span>
                </a>
                <div class="collapse menu-dropdown" id="billsC">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.bills.index') }}" class="nav-link">
                                Danh sách hoá đơn
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

    </ul>
</div>
