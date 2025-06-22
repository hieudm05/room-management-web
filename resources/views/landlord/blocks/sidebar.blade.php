<?php

use Illuminate\Support\Facades\Auth;

 $user = Auth::user(); ?>
<div class="container-fluid">
    <div id="two-column-menu">
    </div>
    <ul class="navbar-nav" id="navbar-nav">
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="" role="button" aria-expanded="false"
                aria-controls="sidebarDashboards">
                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboards">Dashboards</span>
            </a>
        </li>
        <!-- end Dashboard Menu -->
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarApps">
                <i class="mdi mdi-view-grid-plus-outline"></i>
                <span data-key="t-apps">Room Management</span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarApps">
                <ul class="nav nav-sm flex-column">
                    {{-- Chủ trọ --}}
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
                    @endif
                    {{-- Quản lí --}}
                    <li class="nav-item">
                        <a href="{{ route('landlords.staff.index') }}" class="nav-link">
                            Rooms Management for Staff
                        </a>
                    </li>
                    {{-- end Quản lí --}}
                </ul>
            </div>
        </li>

    </ul>
</div>
