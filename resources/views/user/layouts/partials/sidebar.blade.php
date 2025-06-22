 <div class="col-md-3 col-lg-2 px-0 sidebar" id="sidebar">
                <div class="p-3">
                    <h6 class="text-white fw-bold mb-4">
                        <i class="fas fa-user-circle me-2"></i>
                        Customer Panel
                    </h6>
                </div>
                
                <nav class="nav flex-column">
                    <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile*') ? 'active' : '' }}">
                        <i class="fas fa-user-edit"></i>
                        My Profile
                    </a>
                    
                    <a href="{{ route('user.addresses') }}" class="nav-link {{ request()->routeIs('user.addresses*') ? 'active' : '' }}">
                        <i class="fas fa-map-marker-alt"></i>
                        Addresses
                    </a>
                    
                    <a href="{{ route('user.security') }}" class="nav-link {{ request()->routeIs('user.security') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt"></i>
                        Security
                    </a>
                    
                    <a href="{{ route('user.notifications') }}" class="nav-link {{ request()->routeIs('user.notifications*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>
                        Notifications
                        @if(auth()->user()->customerNotifications()->whereNull('read_at')->count() > 0)
                            <span class="notification-badge">
                                {{ auth()->user()->customerNotifications()->whereNull('read_at')->count() }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ route('user.activity') }}" class="nav-link {{ request()->routeIs('user.activity') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        Activity Log
                    </a>
                    
                    <hr class="text-white-50">
                    
                    <a href="#" class="nav-link">
                        <i class="fas fa-shopping-bag"></i>
                        My Orders
                    </a>
                    
                    <a href="#" class="nav-link">
                        <i class="fas fa-heart"></i>
                        Wishlist
                    </a>
                    
                    <a href="#" class="nav-link">
                        <i class="fas fa-headset"></i>
                        Support
                    </a>
                    
                    <hr class="text-white-50">
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </nav>
            </div>