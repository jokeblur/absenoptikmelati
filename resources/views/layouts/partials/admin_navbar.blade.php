<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#" id="notificationDropdown">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="notificationBadge" style="display: none;">0</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notificationDropdownMenu">
        <span class="dropdown-item dropdown-header" id="notificationHeader">Notifikasi Terbaru</span>
        <div class="dropdown-divider"></div>
        <div id="notificationList">
          <!-- Notifications will be loaded here via AJAX -->
        </div>
        <div class="dropdown-divider"></div>
        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">Lihat Semua Notifikasi</a>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('employee.dashboard') }}" title="Absen (Halaman Karyawan)">
        <i class="fas fa-clock"></i> <span class="d-none d-md-inline">Absen</span>
      </a>
    </li>

    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link nav-link">Logout</button>
        </form>
    </li>
  </ul>
</nav>