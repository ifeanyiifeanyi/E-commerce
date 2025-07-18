<div class="vertical-menu">

    <div data-simplebar class="h-100">


        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-profile-line"></i>
                        <span>Brands</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.brands') }}">View Brands</a></li>
                        <li><a href="{{ route('admin.brands.create') }}">Create Brand</a></li>
                    </ul>
                </li>
                 <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-profile-line"></i>
                        <span>ADS</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.advertisement.packages') }}">ADS Packages</a></li>
                        <li><a href="{{ route('admin.vendor.advertisements') }}">Advertisements</a></li>
                        <li><a href="{{ route('admin.vendor.advertisements.pending') }}">Pending Advertisements</a></li>
                        
                        <li><a href="{{ route('admin.vendor.advertisements.active') }}">Active Advertisements</a></li>
                        <li><a> -------------------- </a></li>
                       
                        <li><a href="{{ route('admin.vendor.advertisements.expired') }}">Expired Advertisements</a></li>
                        <li><a href="{{ route('admin.vendor.advertisements.suspended_details') }}">Suspended Advertisements</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-layout-3-line"></i>
                        <span>Categories</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="javascript: void(0);" class="has-arrow">Category</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('admin.categories') }}">View Categories</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">Sub Category</a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="{{ route('admin.subcategories') }}">View Sub Categories</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-file-list-3-line"></i>
                        <span>Measurement Units</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.measurement-units') }}">View Units</a></li>
                        <li><a href="{{ route('admin.measurement-units.create') }}">Create Unit</a></li>
                    </ul>

                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-file-list-3-line"></i>
                        <span>Inventory</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.inventory') }}">Inventory</a></li>
                        <li><a href="{{ route('admin.inventory.alerts') }}">Inventory Alerts</a></li>
                    </ul>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-account-circle-line"></i>
                        <span>Vendor Manage</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.vendors') }}">Vendors</a></li>
                        <li><a href="{{ route('admin.create.vendors') }}">Create Vendor</a></li>
                        <li><a href="{{ route('admin.vendor.stores') }}">Vendor Stores</a></li>

                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-profile-line"></i>
                        <span>Products</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.products') }}">All Products</a></li>
                        <li><a href="{{ route('admin.products.create') }}">Create Product</a></li>
                        <li><a href="pages-directory.html">Vendor Products</a></li>

                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-customer-service-2-line"></i>
                        <span>Customers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.customers') }}">Customers</a></li>
                        <li><a href="{{ route('admin.customers') }}">Customers Orders</a></li>

                    </ul>
                </li>



            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
