<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="{{ route('admin.dashboard') }}" class="brand-link">
    <img src="{{ asset('image/optikmelati.jpg') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">OM Absensi</span>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{ asset('image/optikmelati.jpg') }}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="{{ route('admin.profile') }}" class="d-block">{{ Auth::user()->name ?? 'Admin User' }}</a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- DASHBOARD -->
        <li class="nav-header">DASHBOARD</li>
        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item"><hr class="dropdown-divider"></li>

        <!-- MANAJEMEN USER -->
        <li class="nav-header">MANAJEMEN USER</li>
        <li class="nav-item">
          <a href="{{ route('admin.admins.index') }}" class="nav-link {{ Request::routeIs('admin.admins.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Manajemen Admin</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.employees.index') }}" class="nav-link {{ Request::routeIs('admin.employees.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>Manajemen Karyawan</p>
          </a>
        </li>
        <li class="nav-item"><hr class="dropdown-divider"></li>

        <!-- MANAJEMEN ABSENSI -->
        <li class="nav-header">MANAJEMEN ABSENSI</li>
        <li class="nav-item has-treeview {{ Request::routeIs('admin.attendances.*') || Request::routeIs('admin.attendance.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ Request::routeIs('admin.attendances.*') || Request::routeIs('admin.attendance.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clock"></i>
            <p>
              Management Absensi
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.attendances.index') }}" class="nav-link {{ Request::routeIs('admin.attendances.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Daftar Absensi</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.attendance.report') }}" class="nav-link {{ Request::routeIs('admin.attendance.report') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Absensi</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.attendance.monthly_late_report') }}" class="nav-link {{ Request::routeIs('admin.attendance.monthly_late_report') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Keterlambatan Bulanan</p>
              </a>
            </li>
            <!-- <li class="nav-item">
              <a href="{{ route('admin.attendance.settings') }}" class="nav-link {{ Request::routeIs('admin.attendance.settings') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pengaturan Absensi</p>
              </a>
            </li> -->
            <li class="nav-item">
              <a href="{{ route('admin.attendance.export') }}" class="nav-link {{ Request::routeIs('admin.attendance.export') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Export Data</p>
              </a>
            </li>
          </ul>
        </li>
        <li class="nav-item"><hr class="dropdown-divider"></li>

        <!-- PERSETUJUAN -->
        <li class="nav-header">PERSETUJUAN</li>
        <li class="nav-item">
          <a href="{{ route('admin.leaves.index') }}" class="nav-link {{ Request::routeIs('admin.leaves.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>Persetujuan Cuti</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Persetujuan Izin</p>
          </a>
        </li>
        <li class="nav-item"><hr class="dropdown-divider"></li>

        <!-- PROFIL -->
        <li class="nav-header">PROFIL</li>
        <li class="nav-item">
          <a href="{{ route('admin.profile') }}" class="nav-link {{ Request::routeIs('admin.profile') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
            <p>Profil Admin</p>
          </a>
        </li>
        <!-- Tambahkan menu lain di sini -->
      </ul>
    </nav>
    </div>
  </aside>