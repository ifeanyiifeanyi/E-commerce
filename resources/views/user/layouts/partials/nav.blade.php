<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <button class="btn btn-outline-primary d-md-none" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2 class="mb-0 ms-3 ms-md-0">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="d-flex align-items-center">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile" class="rounded-circle me-2"
                    width="30" height="30">
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                        <i class="fas fa-user me-2"></i>Profile
                    </a></li>
                <li><a class="dropdown-item" href="{{ route('user.security') }}">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
