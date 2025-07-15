{{-- filepath: c:\laragon\www\room-management-web\resources\views\landlord\blocks\sidebar.blade.php --}}
<?php

use Illuminate\Support\Facades\Auth;

$user = Auth::user(); ?>
<div class="container-fluid">
    <div id="two-column-menu"></div>
    <ul class="navbar-nav" id="navbar-nav">
        <li class="nav-item">
            <a class="nav-link menu-link" href="{{ route('landlords.dashboard') }}">
                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboards">Dashboards</span>
            </a>
        </li>
        <!-- Room Management Menu -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarRoom" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarRoom">
                <i class="mdi mdi-view-grid-plus-outline"></i>
                <span data-key="t-apps">Room Management</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarRoom">
                <ul class="nav nav-sm flex-column">
                    @if ($user->role === 'Landlord')
                    <li class="nav-item">
                        <a href="{{ route('landlords.properties.list') }}" class="nav-link">
                            Properties Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('landlords.rooms.index') }}" class="nav-link">
                            Rooms Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('landlords.approvals.index') }}" class="nav-link">
                            Approval
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('landlords.approvals.users.index') }}" class="nav-link">
                            Approval User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('landlords.room_edit_requests.index') }}" class="nav-link">
                            Approve room change request
                        </a>
                    
                        <li class="nav-item">
                            <a href="{{ route('landlords.properties.list') }}" class="nav-link">
                                Properties Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.rooms.index') }}" class="nav-link">
                                Rooms Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.index') }}" class="nav-link">
                                Approval
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.approvals.users.index') }}" class="nav-link">
                                Approval User
                            </a>
                        </li>
                    @endif

                    @if ($user->role === 'Staff')
                        <li class="nav-item">
                            <a href="{{ route('landlords.staff.index') }}" class="nav-link">
                                Properties Management for Staff
                            </a>
                        </li>
                    @endif
                     @if ($user->role === 'Staff')
                    <li class="nav-item">
                        <a href="{{ route('landlords.staff.complaints.index') }}" class="nav-link">
                            Complaints Staff
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </li>
        @if ($user->role === 'Landlord')
            <li class="nav-item">
                <a class="nav-link menu-link" href="#sidebarBank" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarBank">
                    <i class="mdi mdi-bank"></i>
                    <span data-key="t-bank">Bank Accounts</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarBank">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('landlords.bank_accounts.index') }}" class="nav-link">
                                All Bank Accounts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('landlords.bank_accounts.assign') }}" class="nav-link">
                                Assign Bank Accounts to Properties
                            </a>
                        </li>  
                    </ul>
                <li class="nav-item">
                <a class="nav-link menu-link" href="{{ route('landlord.complaints.index') }}">
                 <i class="mdi mdi-alert-circle-outline"></i>
                      <span data-key="t-complaints">Complaints</span>
                    </a>
                  </li>
                </div>
            </li>
        @endif
         </li>
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


    </ul>
</div>
