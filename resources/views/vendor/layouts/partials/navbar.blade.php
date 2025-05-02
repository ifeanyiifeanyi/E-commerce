<div
class="topbar sticky-top d-flex justify-content-between align-items-center px-3"
>
<!-- Added horizontal padding -->
<div class="d-flex align-items-center flex-shrink-0">
  <!-- Added flex-shrink-0 -->
  <button class="btn btn-dark me-3 d-lg-none" id="sidebar-toggle">
    <i class="bi bi-list"></i>
  </button>
  <!-- Search Bar Container (Modified for responsiveness) -->
  <div
    class="search-bar-container flex-grow-1 me-3 d-none d-md-flex align-items-center"
    style="min-width: 150px; max-width: 400px"
  >
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
  <!-- Profile Dropdown Start -->
  <div class="dropdown">
    <a
      href="#"
      class="text-decoration-none"
      id="profileDropdownToggle"
      role="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      <div class="profile-circle" title="User Menu">
        {{ Auth::user()->name[0] ?? 'U' }}

      </div>
    </a>
    <ul
      class="dropdown-menu dropdown-menu-end"
      aria-labelledby="profileDropdownToggle"
    >
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
      <li><hr class="dropdown-divider" /></li>
      <li>
        <a
          class="dropdown-item d-flex align-items-center text-danger"
          href="#"
        >
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </div>
  <!-- Profile Dropdown End -->
</div>
</div>
