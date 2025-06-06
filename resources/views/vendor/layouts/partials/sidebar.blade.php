<div class="sidebar" id="sidebar">
    <div class="p-3">
      <h5 class="mb-3 mt-2">Navigation</h5>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('vendor.stores') }}" class="nav-link {{ request()->routeIs('vendor.stores*') ? 'active' : '' }}">
            <i class="bi bi-house"></i> My Stores
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('vendor.products') }}" class="nav-link {{ request()->routeIs('vendor.products*') ? 'active' : '' }}"> <i class="bi bi-bag"></i> Products </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-people"></i> Customers
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('vendor.advertisement') }}" class="nav-link {{ request()->routeIs('vendor.advertisement*') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> ADS Management
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-gift"></i> Coupons
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-graph-up"></i> Analytics
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-gear"></i> Settings
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-question-circle"></i> Support
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="bi bi-file-earmark-text"></i> Invoices
          </a>
        </li>
        <li class="nav-item  fw-bold m-hover">
          <a href="{{ route('vendor.logout') }}"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link ">
            <i class="bi bi-box-arrow-left"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
  <form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" class="d-none">
    @csrf
</form>
<style>
    .m-hover a:hover {
        background-color: #e0370d !important;
    }
    .m-hover a {
        color: #fff !important;
        background-color: #d9534f !important;
    }
</style>
