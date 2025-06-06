 <!-- Announcement Bar -->
    <div class="announcement-bar text-center">
        <div class="container">
            <p class="mb-0">
                Free shipping on orders over $50! Limited time offer.
            </p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><span>Abode</span> Goods</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <form class="search-form mx-auto d-flex">
                    <input class="form-control me-2 rounded-pill" type="search" placeholder="Search products..." />
                    <button class="btn btn-outline-primary rounded-pill" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-user me-1"></i> Account
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-heart me-1"></i> Wishlist
                        </a>
                    </li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="#">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                            <span id="notification-badge" class="badge rounded-pill bg-danger">3</span>
                        </a>
                    </li>
                </ul>
                <a href="{{ route('vendor.register.step1') }}" class="btn btn-primary rounded-pill ms-3">
                    <i class="fas fa-store me-1"></i> Sell on Abode
                </a>
            </div>
        </div>
    </nav>
