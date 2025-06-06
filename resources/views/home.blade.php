@extends('frontend.layout')

@section('title', 'Home')

@section('content')
<!-- Categories Section -->
    <section class="container my-5">
        <h2 class="text-center mb-4">Browse Popular Categories</h2>
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-4">
                <div class="category-pill">
                    <i class="fas fa-gem category-icon"></i> Jewelry
                </div>
                <div class="category-pill">
                    <i class="fas fa-tshirt category-icon"></i> Clothing
                </div>
                <div class="category-pill">
                    <i class="fas fa-home category-icon"></i> Home Decor
                </div>
                <div class="category-pill">
                    <i class="fas fa-utensils category-icon"></i> Kitchen
                </div>
                <div class="category-pill">
                    <i class="fas fa-spa category-icon"></i> Wellness
                </div>
                <div class="category-pill">
                    <i class="fas fa-gift category-icon"></i> Gifts
                </div>
                <div class="category-pill">
                    <i class="fas fa-palette category-icon"></i> Art
                </div>
            </div>
        </div>
    </section>

    <!-- Product Section -->
    <section class="container my-5">
        <h2 class="section-title">Introducing Our Products</h2>
        <div class="row g-4">
            <!-- Product 1 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">TrendyCrafts</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Handcrafted Angel Figurine</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$24.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">PetLovers</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Leather Pet Collar</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$19.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">JewelryBox</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Handmade Necklace Display</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$34.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">HomeEssentials</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Golden Wall Decor</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$42.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 5 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">ClothingCo</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Summer Dress Collection</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$55.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 6 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">KitchenWares</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Ceramic Mug Set</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$27.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 7 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">ArtGallery</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Abstract Wall Art</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$89.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 8 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <img src="/api/placeholder/300/300" class="card-img-top" alt="Product image" />
                        <span class="vendor-badge">WellnessStore</span>
                    </div>
                    <div class="card-body">
                        <h5 class="product-title">Essential Oil Diffuser</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">$33.99</span>
                            <button class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="#" class="btn btn-outline-primary rounded-pill px-4">View All Products</a>
        </div>
    </section>

    <!-- Deals Section -->
    <section class="container my-5">
        <div class="row g-4">
            <!-- Deal 1 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center p-4" style="background-color: #e9f9fd">
                        <h4>Summer Sale</h4>
                        <p>Up to 40% off</p>
                        <a href="#" class="btn btn-primary rounded-pill">Shop Now</a>
                    </div>
                </div>
            </div>

            <!-- Deal 2 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center p-4" style="background-color: #fff6e5">
                        <h4>New Arrivals</h4>
                        <p>Fresh products daily</p>
                        <a href="#" class="btn btn-primary rounded-pill">Discover</a>
                    </div>
                </div>
            </div>

            <!-- Deal 3 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center p-4" style="background-color: #f5edff">
                        <h4>Gift Cards</h4>
                        <p>Perfect for any occasion</p>
                        <a href="#" class="btn btn-primary rounded-pill">Buy Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="container my-5">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="stat-number">5,000+</div>
                <div class="stat-text">Products Available</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">500+</div>
                <div class="stat-text">Trusted Vendors</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">10k+</div>
                <div class="stat-text">Happy Customers</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">99%</div>
                <div class="stat-text">Satisfaction Rate</div>
            </div>
        </div>
    </section>

    <!-- Feature Banner -->
    <section class="container my-5">
        <div class="banner">
            <img src="/api/placeholder/1200/400" class="img-fluid w-100" alt="Banner" />
            <div class="banner-content">
                <h2>Shop Our Best Sellers</h2>
                <p>Discover what customers are loving right now!</p>
                <a href="#" class="btn btn-primary rounded-pill">Explore Now</a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div class="row g-4">
                <!-- Featured Product 1 -->
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top" alt="Featured product" />
                            <span class="vendor-badge">TopVendor</span>
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Handmade Ceramic Bowl</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$39.99</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Product 2 -->
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top" alt="Featured product" />
                            <span class="vendor-badge">ArtisanCrafts</span>
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Wooden Serving Tray</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$45.99</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Product 3 -->
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top" alt="Featured product" />
                            <span class="vendor-badge">EcoFriendly</span>
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Natural Wax Candles</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$19.99</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Product 4 -->
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top" alt="Featured product" />
                            <span class="vendor-badge">HandcraftedJoy</span>
                        </div>
                        <div class="card-body">
                            <h5 class="product-title">Macrame Wall Hanging</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$52.99</span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@section('styles')

@endsection
@section('scripts')

@endsection
