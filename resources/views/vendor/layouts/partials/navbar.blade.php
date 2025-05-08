<div class="topbar sticky-top d-flex justify-content-between align-items-center px-3">
    <!-- Added horizontal padding -->
    <div class="d-flex align-items-center flex-shrink-0">
        <!-- Added flex-shrink-0 -->
        <button class="btn btn-dark me-3 d-lg-none" id="sidebar-toggle">
            <i class="bi bi-list"></i>
        </button>
        <!-- Search Bar Container (Modified for responsiveness) -->
        <div class="search-bar-container flex-grow-1 me-3 d-none d-md-flex align-items-center"
            style="min-width: 150px; max-width: 400px">
            <!-- Use d-md-flex, allow grow, set min/max-width -->
            <div class="input-group input-group-sm">
                <!-- Smaller input group -->
                <input type="text" class="form-control" placeholder="Search..." />
                <!-- Shorter placeholder -->
                <button class="btn btn-success" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <!-- Icon only for smaller button -->
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center flex-shrink-0">
        <!-- Added flex-shrink-0 -->
        <a href="#" class="text-white me-3 d-none d-md-block">
            <!-- Adjusted margin -->
            <i class="bi bi-heart fs-5"></i>
            <!-- Slightly larger icon -->
        </a>
        <a href="#" class="text-white me-3">
            <!-- Adjusted margin -->
            <i class="bi bi-cart3 fs-5"></i>
            <!-- Slightly larger icon -->
        </a>
        <div class="dropdown me-4 mt-2">
            <a href="#" class="text-white position-relative" id="notificationsDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    9+
                    <span class="visually-hidden">unread notifications</span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                <li>
                    <h6 class="dropdown-header">Notifications</h6>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle p-1">
                                <i class="fas fa-shopping-cart fa-fw"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">New order received</p>
                            <small class="text-muted">5 min ago</small>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="me-3">
                            <div class="bg-success text-white rounded-circle p-1">
                                <i class="fas fa-user fa-fw"></i>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0">New customer registered</p>
                            <small class="text-muted">15 min ago</small>
                        </div>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
            </ul>
        </div>

        <!-- Profile Dropdown Start -->
        <div class="dropdown">
            <a href="#" class="text-decoration-none" id="profileDropdownToggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <div class="profile-circle" title="User Menu">
                    {{ Auth::user()->name[0] ?? 'U' }}

                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownToggle">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('vendor.profile') }}">
                        <i class="bi bi-person-circle me-2"></i> Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('vendor.documents') }}">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider" />
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('vendor.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- Profile Dropdown End -->
    </div>
</div>
<form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" class="d-none">
    @csrf
</form>
