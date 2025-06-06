<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">
                    Product Quick View
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="/api/placeholder/500/500" class="img-fluid rounded" alt="Product Image" />
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-3">Product Name</h4>
                        <div class="mb-3">
                            <span class="badge bg-primary">TrendyCrafts</span>
                            <div class="mt-2">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                <span class="ms-2">(4.5)</span>
                            </div>
                        </div>
                        <h5 class="text-primary mb-3">$29.99</h5>
                        <p>
                            Product description goes here. This is a brief overview of the
                            product highlighting its key features and benefits.
                        </p>
                        <div class="mb-3">
                            <h6>Options:</h6>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="btnradio" id="option1" checked />
                                <label class="btn btn-outline-primary" for="option1">Small</label>
                                <input type="radio" class="btn-check" name="btnradio" id="option2" />
                                <label class="btn btn-outline-primary" for="option2">Medium</label>
                                <input type="radio" class="btn-check" name="btnradio" id="option3" />
                                <label class="btn btn-outline-primary" for="option3">Large</label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="input-group me-3" style="width: 130px">
                                <button class="btn btn-outline-secondary" type="button" id="decrementBtn">
                                    -
                                </button>
                                <input type="text" class="form-control text-center" value="1"
                                    id="quantityInput" />
                                <button class="btn btn-outline-secondary" type="button" id="incrementBtn">
                                    +
                                </button>
                            </div>
                            <button class="btn btn-primary" id="addToCartBtn">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-outline-secondary me-2">
                                <i class="fas fa-heart me-1"></i>Wishlist
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fas fa-share-alt me-1"></i>Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">
                    Login to Your Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center mb-4">
                    Welcome back! Please enter your credentials to access your account.
                </p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required />
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" />
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                    </p>
                </form>
                <div class="text-center mt-3">
                    <p>
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-decoration-none">Sign up</a>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100 text-center">
                    <p class="mb-0">Or login with:</p>
                    <div class="d-flex justify-content-center mt-2">
                        <button class="btn btn-outline-dark mx-1">
                            <i class="fab fa-google"></i>
                        </button>
                        <button class="btn btn-outline-dark mx-1">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                        <button class="btn btn-outline-dark mx-1">
                            <i class="fab fa-apple"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
