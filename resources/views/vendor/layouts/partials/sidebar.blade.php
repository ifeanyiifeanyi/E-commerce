<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box">
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-light.png" alt="" height="24">
                    </span>
                </a>
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/logo-sm.png" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/logo-dark.png" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                <li>
                    <a href="{{ route('vendor.dashboard') }}">
                        <i data-feather="home"></i>

                        <span> Dashboard </span>
                    </a>
                </li>

               


                <li>
                    <a href="#store" data-bs-toggle="collapse">
                        <i data-feather="shopping-bag"></i>
                        <span> My Store </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="store">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('vendor.stores.show') }}">Store Details</a>
                            </li>
                            <li>
                                <a href="{{ route('vendor.stores') }}">Update Details</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#product" data-bs-toggle="collapse">
                        <i data-feather="database"></i>
                        <span> Products </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="product">
                        <ul class="nav-second-level">
                            <li>
                                <a href="">View Products</a>
                            </li>
                            <li>
                                <a href="">Create Product</a>
                            </li>
                        </ul>
                    </div>
                </li>
               
                <li>
                    <a href="#sidebarAuth" data-bs-toggle="collapse">
                        <i data-feather="folder-plus"></i>
                        <span> Verification Documents </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('vendor.documents.create') }}">Submit Document</a>
                            </li>
                            <li>
                                <a href="{{ route('vendor.documents') }}">View Documents</a>
                            </li>
                        </ul>
                    </div>
                </li>


            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
