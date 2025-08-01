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
                        <li class="nav-item"><a href="{{ route('landlords.properties.list') }}" class="nav-link">Quản Lý
                                Bất Động Sản</a></li>
                        <li class="nav-item"><a href="{{ route('landlords.rooms.index') }}" class="nav-link">Danh sách
                                Phòng Tổng Quan</a></li>
                        <li class="nav-item"><a href="{{ route('landlords.approvals.index') }}" class="nav-link">Quản Lý
                                Phê Duyệt Hợp Đồng</a></li>
                        <li class="nav-item"><a href="{{ route('landlords.approvals.users.index') }}" Fireplace:
                                class="nav-link">Quản Lý Phê Duyệt Người Dùng</a></li>
                        <li class="nav-item"><a href="{{ route('landlords.room_edit_requests.index') }}"
                                class="nav-link">Phê duyệt yêu cầu chuyển phòng</a></li>
                        <li class="nav-item"><a href="{{ route('landlord.posts.approval.index') }}"
                                class="nav-link">Duyệt bài đăng</a></li>
<<<<<<< HEAD

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
        
                        <li class="nav-item">
                            <a href="{{ route('landlords.room_edit_requests.index') }}" class="nav-link">
                                Phê duyệt yêu cầu chỉnh sửa  phòng

                            </a>
                        </li>
                         <li class="nav-item">
                      <a href="{{ route('landlord.roomleave.index') }}" class="nav-link">
                          Quản lý yêu cầu rời phòng
                      </a>
                  </li>
                      <li class="nav-item">
                      <a href="{{ route('landlord.roomleave.processed') }}" class="nav-link">
                          Lịch sử duyệt rời phòng
                      </a>
                  </li>

                        <li class="nav-item"><a href="{{ route('landlord.bookings.index') }}" class="nav-link">Quản
                                lí đặt
                                lịch</a></li>

=======
>>>>>>> 16ac02342d143393bb568ded9b2848c3826416c5
                    @endif

                    @if ($user->role === 'Staff')
                        <li class="nav-item"><a href="{{ route('landlords.staff.index') }}" class="nav-link">Quản lý BĐS
                                dành cho nhân viên</a></li>
                        <li class="nav-item"><a href="{{ route('landlord.staff.complaints.index') }}"
                                class="nav-link">Tiếp nhận khiếu nại</a></li>
                        <li class="nav-item"><a href="{{ route('staff.posts.index') }}" class="nav-link">Đăng bài</a>
                        </li>
<<<<<<< HEAD
                        <li class="nav-item">

                            <a href="{{ route('landlord.staff.complaints.index') }}" class="nav-link">
                                Nhân viên tiếp nhận khiếu nại

                            </a>
                        </li>
                         <li class="nav-item">
                   <a href="{{ route('landlord.staff.complaints.history') }}" class="nav-link">
                                Lịch sử khiếu nại
                      </a>
                      
                  </li>
                  <li class="nav-item">
                      <a href="{{ route('landlord.staff.roomleave.index') }}" class="nav-link">
                          Quản lý yêu cầu rời sửa phòng
                      </a>
                  </li>
                   <li class="nav-item">
                      <a href="{{ route('landlord.staff.roomleave.processed') }}" class="nav-link">
                          Lịch yêu cầu rời sửa phòng
                      </a>
                  </li>
                  
                   
                   
                    @endif

                        <li class="nav-item"><a href="{{ route('staff.contract.renewals.index') }}"
                                class="nav-link">Tiếp Nhận Tái Ký</a></li>

                        <li class="nav-item"><a href="{{ route('booking.index') }}" class="nav-link">Quản
                                lý đặt
                                lịch</a></li>

               
=======
                        <li class="nav-item"><a href="{{ route('landlord.staff.complaints.history') }}"
                                class="nav-link">Lịch sử xử lý khiếu nại</a></li>
                    @endif
>>>>>>> 16ac02342d143393bb568ded9b2848c3826416c5
                </ul>
            </div>
        </li>

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
                    @endif

                    @if ($user->role === 'Landlord')
                        <li class="nav-item">
                            <a href="{{ route('landlord.posts.approval.index') }}" class="nav-link">Duyệt bài đăng</a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('staff.categories.index') }}" class="nav-link">Danh mục bài đăng</a>
                    </li>
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
                        <li class="nav-item"><a href="{{ route('landlords.properties.list') }}"
                                class="nav-link">Danh sách Bất Động
                                Sản</a></li>
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

        <!-- Bills (Staff) -->
        @if ($user->role === 'Staff')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#bills" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="bills">
                    <i class="mdi mdi-receipt"></i>
                    <span data-key="t-bank">Hoá đơn</span>
                </a>
                <div class="collapse menu-dropdown" id="bills">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.staff.payment.list') }}"
                                class="nav-link">Danh
                                sách hoá đơn</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item"><a href="{{ route('staff.contract.renewals.index') }}" class="nav-link">Tiếp
                    Nhận Tái Ký</a></li>

            <li class="nav-item"><a href="{{ route('booking.index') }}" class="nav-link">Quản
                    lý đặt lịch</a></li>
            <li class="nav-item"><a href="{{ route('landlord.staff.chart.index') }}" class="nav-link">Biều
                    Đồ Đặt Lịch</a></li>
        @endif

        <!-- Bills (Landlord) -->
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#billsC" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="billsC">
                    <i class="mdi mdi-file-document-outline"></i>
                    <span data-key="t-bank">Hoá đơn (chủ)</span>
                </a>
                <div class="collapse menu-dropdown" id="billsC">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('landlords.bills.index') }}" class="nav-link">Danh
                                sách hoá đơn</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item"><a href="{{ route('landlord.bookings.index') }}" class="nav-link">Quản
                    lý đặt lịch</a></li>
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
