<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Untuk AJAX --}}
    <title>Absensi Optik Melati | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Fallback for Bootstrap Icons -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Apply Poppins to all elements */
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background-color: #2c3e50;
        }
        .navbar-brand, .nav-link {
            color: #ecf0f1 !important;
        }
        /* Navbar toggler styles moved to animated section below */
        /* Card styling consistent with dark theme */
        .card {
            margin-bottom: 15px;
            border: 1px solid #bdc3c7;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        
        .card-header {
            background-color: #ecf0f1;
            border-bottom: 1px solid #bdc3c7;
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card-body {
            padding: 1rem;
            background-color: #fff;
        }
        
        .card-title {
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Form styling */
        .form-control, .form-select {
            border: 1px solid #bdc3c7;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
        
        .form-label {
            color: #2c3e50;
            font-weight: 500;
        }
        .footer-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #2c3e50;
            border-top: 1px solid #34495e;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0,0,0,.1);
            z-index: 1000;
        }
        .footer-nav .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.8em;
            color: #bdc3c7;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }
        .footer-nav .nav-link:hover {
            color: #3498db;
            background-color: #34495e;
        }
        .footer-nav .nav-link.active {
            color: #3498db;
            background-color: #34495e;
        }
        .footer-nav .nav-link i {
            font-size: 1.5em;
            margin-bottom: 5px;
            display: block;
        }
        .footer-nav .nav-link span {
            font-size: 0.75em;
            font-weight: 500;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none; /* Hidden by default */
        }
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Smooth animation for navbar collapse */
        .navbar-collapse {
            transition: all 0.3s ease-in-out;
        }
        
        .navbar-collapse.collapsing {
            transition: height 0.35s ease;
        }
        
        .navbar-collapse.show {
            animation: slideDown 0.3s ease-in-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Enhanced mobile navbar styling */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #2c3e50;
                padding: 1rem;
                border-radius: 0.375rem;
                margin-top: 0.5rem;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                border: 1px solid #34495e;
            }
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
                border-bottom: 1px solid rgba(236,240,241,0.1);
                padding-bottom: 0.5rem;
            }
            .navbar-nav .nav-item:last-child {
                margin-bottom: 0;
                border-bottom: none;
                padding-bottom: 0;
            }
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                border-radius: 0.25rem;
                transition: all 0.2s ease;
                color: #ecf0f1 !important;
            }
            .navbar-nav .nav-link:hover {
                background-color: rgba(236,240,241,0.1);
                transform: translateX(5px);
            }
            .navbar-nav .nav-link.active {
                background-color: rgba(52,152,219,0.2);
                color: #3498db !important;
            }
        }
        
        /* Ensure hamburger menu is visible */
        .navbar-toggler {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Font Awesome styling */
        .fas {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }
        
        /* Icon sizes for better visibility */
        .nav-link .fas {
            font-size: 1.1em;
        }
        
        .footer-nav .nav-link .fas {
            font-size: 1.5em;
            margin-bottom: 5px;
            display: block;
        }

        /* Animated hamburger icon */
        .navbar-toggler {
            border: 1px solid rgba(236,240,241,0.5);
            padding: 0.25rem 0.5rem;
            background-color: transparent;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-toggler:hover {
            background-color: rgba(236,240,241,0.1);
            transform: scale(1.05);
        }
        
        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(236,240,241,0.25);
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28236, 240, 241, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            transition: all 0.3s ease;
        }
        
        /* Animated hamburger when active */
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            transform: rotate(90deg);
        }
        
        /* Custom hamburger animation */
        .navbar-toggler[aria-expanded="true"] {
            background-color: rgba(52,152,219,0.2);
            border-color: #3498db;
        }

        /* Ensure navbar collapse works properly */
        .navbar-collapse {
            flex-basis: 100%;
            flex-grow: 1;
            align-items: center;
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 1000;
                background-color: #2c3e50;
                border-top: 1px solid #34495e;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            
            .navbar-nav {
                padding: 1rem;
            }
        }

        /* Button styling consistent with dark navbar theme */
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            color: #fff;
        }
        
        .btn-success {
            background-color: #27ae60;
            border-color: #27ae60;
            color: #fff;
        }
        
        .btn-success:hover {
            background-color: #229954;
            border-color: #229954;
            color: #fff;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: #fff;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            color: #fff;
        }
        
        .btn-warning {
            background-color: #f39c12;
            border-color: #f39c12;
            color: #fff;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
            color: #fff;
        }
        
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }
        
        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
            color: #fff;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            color: #fff;
        }
        
        .btn-outline-primary {
            color: #3498db;
            border-color: #3498db;
        }
        
        .btn-outline-primary:hover {
            background-color: #3498db;
            border-color: #3498db;
            color: #fff;
        }
        
        .btn-outline-success {
            color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-outline-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
            color: #fff;
        }
        
        .btn-outline-danger {
            color: #e74c3c;
            border-color: #e74c3c;
        }
        
        .btn-outline-danger:hover {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: #fff;
        }
        
        .btn-outline-warning {
            color: #f39c12;
            border-color: #f39c12;
        }
        
        .btn-outline-warning:hover {
            background-color: #f39c12;
            border-color: #f39c12;
            color: #fff;
        }
        
        .btn-outline-info {
            color: #17a2b8;
            border-color: #17a2b8;
        }
        
        .btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }
        
        /* Logout button styling */
        .btn-link.nav-link {
            color: #bdc3c7 !important;
            text-decoration: none;
            border: none;
            background: none;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .btn-link.nav-link:hover {
            color: #3498db !important;
            background-color: #34495e;
        }
        
        .dropdown-item {
            color: #2c3e50;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #2c3e50;
        }
        
        /* Button focus states */
        .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
        
        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(52,152,219,0.25);
        }
        
        .btn-success:focus {
            box-shadow: 0 0 0 0.2rem rgba(39,174,96,0.25);
        }
        
        .btn-danger:focus {
            box-shadow: 0 0 0 0.2rem rgba(231,76,60,0.25);
        }
        
        .btn-warning:focus {
            box-shadow: 0 0 0 0.2rem rgba(243,156,18,0.25);
        }
        
        .btn-info:focus {
            box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25);
        }
        
        .btn-secondary:focus {
            box-shadow: 0 0 0 0.2rem rgba(108,117,125,0.25);
        }

        /* Alert styling consistent with dark theme */
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        .alert-primary {
            background-color: #cce7ff;
            border-color: #b3d9ff;
            color: #004085;
        }
        
        /* Badge styling */
        .badge.bg-success {
            background-color: #27ae60 !important;
        }
        
        .badge.bg-danger {
            background-color: #e74c3c !important;
        }
        
        .badge.bg-warning {
            background-color: #f39c12 !important;
        }
        
        .badge.bg-info {
            background-color: #17a2b8 !important;
        }
        
        .badge.bg-primary {
            background-color: #3498db !important;
        }
        
        .badge.bg-secondary {
            background-color: #6c757d !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('employee.dashboard') }}">
                <img src="{{asset('image/optik-melati.png')}}" alt="Logo Optik Melati" style="width:36px; height:36px; object-fit:contain; margin-right:8px;">
                <span>Absensi Optik Melati</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('employee.history') ? 'active' : '' }}" href="{{ route('employee.history') }}">
                            <i class="fas fa-history me-1"></i>Riwayat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('employee.pengajuan.index') ? 'active' : '' }}" href="{{ route('employee.pengajuan.index') }}">
                            <i class="fas fa-file-alt me-1"></i>Pengajuan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::routeIs('employee.profile') ? 'active' : '' }}" href="{{ route('employee.profile') }}">
                            <i class="fas fa-user me-1"></i>Profil
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" 
                                     alt="Foto Profil" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=32&background=3498db&color=fff" 
                                     alt="Foto Profil" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('employee.profile') }}">
                                <i class="fas fa-user me-2"></i>Edit Profil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline" id="logoutForm">
                                    @csrf
                                    <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; padding: 0.5rem 1rem;">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3 mb-5 pb-5">
        @yield('content')
    </div>

    
    <nav class="footer-nav">
        <div class="container">
            <div class="row text-center">
                <div class="col">
                    <a class="nav-link {{ Request::routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a class="nav-link {{ Request::routeIs('employee.pengajuan.index') ? 'active' : '' }}" href="{{ route('employee.pengajuan.index') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Pengajuan</span>
                    </a>
                </div>
                <div class="col">
                    <a class="nav-link {{ Request::routeIs('employee.history') ? 'active' : '' }}" href="{{ route('employee.history') }}">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                    </a>
                </div>
                <div class="col">
                    <a class="nav-link {{ Request::routeIs('employee.profile') ? 'active' : '' }}" href="{{ route('employee.profile') }}">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </div>
                <div class="col">
                    <form action="{{ route('logout') }}" method="POST" class="d-grid" id="footerLogoutForm">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link text-decoration-none">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> {{-- jQuery untuk AJAX --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eOzrjh+O7O/1l8" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- SweetAlert2 untuk notifikasi --}}

    <script>
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        // Enhanced navbar functionality with smooth animations
        $(document).ready(function() {
            console.log('Employee layout loaded');
            
            // Initialize Bootstrap collapse
            var navbarCollapse = document.getElementById('navbarNav');
            var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                toggle: false
            });
            
            // Enhanced hamburger menu functionality
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                console.log('Hamburger menu clicked');
                
                var target = $(this).data('bs-target');
                var $navbarCollapse = $(target);
                
                // Toggle with smooth animation
                if ($navbarCollapse.hasClass('show')) {
                    console.log('Closing navbar');
                    $navbarCollapse.removeClass('show').addClass('collapsing');
                    setTimeout(function() {
                        $navbarCollapse.removeClass('collapsing');
                    }, 350);
                } else {
                    console.log('Opening navbar');
                    $navbarCollapse.addClass('collapsing');
                    setTimeout(function() {
                        $navbarCollapse.removeClass('collapsing').addClass('show');
                    }, 10);
                }
                
                // Update aria-expanded
                $(this).attr('aria-expanded', !$navbarCollapse.hasClass('show'));
            });
            
            // Close navbar when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar').length) {
                    $('#navbarNav').removeClass('show');
                    $('.navbar-toggler').attr('aria-expanded', 'false');
                }
            });
            
            // Close navbar when clicking on a link (mobile)
            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    setTimeout(function() {
                        $('#navbarNav').removeClass('show');
                        $('.navbar-toggler').attr('aria-expanded', 'false');
                    }, 300);
                }
            });
            
            // Debug: Check if Bootstrap is loaded
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap loaded successfully');
            } else {
                console.log('Bootstrap not loaded, using fallback');
            }
            
            // Debug: Check navbar elements
            console.log('Navbar elements:', {
                toggler: $('.navbar-toggler').length,
                collapse: $('#navbarNav').length,
                navItems: $('.navbar-nav .nav-item').length
            });
        });

        // Fallback for Bootstrap collapse if not loaded
        if (typeof bootstrap === 'undefined') {
            console.log('Using fallback navbar functionality');
            
            $('.navbar-toggler').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var target = $(this).data('bs-target');
                var $navbarCollapse = $(target);
                var isExpanded = $(this).attr('aria-expanded') === 'true';
                
                console.log('Fallback hamburger clicked, expanded:', isExpanded);
                
                if (isExpanded) {
                    // Close menu
                    $navbarCollapse.removeClass('show').addClass('collapsing');
                    $(this).attr('aria-expanded', 'false');
                    
                    setTimeout(function() {
                        $navbarCollapse.removeClass('collapsing');
                    }, 350);
                } else {
                    // Open menu
                    $navbarCollapse.addClass('collapsing');
                    $(this).attr('aria-expanded', 'true');
                    
                    setTimeout(function() {
                        $navbarCollapse.removeClass('collapsing').addClass('show');
                    }, 10);
                }
            });
        }

        // Handle logout forms
        $(document).ready(function() {
            console.log('Setting up logout forms...');
            
            // Handle navbar logout form
            $('#logoutForm').on('submit', function(e) {
                console.log('Navbar logout form submitted');
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    console.log('Proceeding with logout...');
                    return true; // Allow form to submit
                } else {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Handle footer logout form
            $('#footerLogoutForm').on('submit', function(e) {
                console.log('Footer logout form submitted');
                if (confirm('Apakah Anda yakin ingin logout?')) {
                    console.log('Proceeding with logout...');
                    return true; // Allow form to submit
                } else {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Debug: Check if forms exist
            console.log('Logout forms found:', {
                navbarForm: $('#logoutForm').length,
                footerForm: $('#footerLogoutForm').length
            });
            
            // Test logout functionality
            $('.dropdown-item[type="submit"]').on('click', function() {
                console.log('Logout button clicked');
            });
        });

        // Fungsi untuk mendapatkan lokasi GPS
        function getLocation(callback) {
            if (navigator.geolocation) {
                showLoading();
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        hideLoading();
                        callback({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        });
                    },
                    (error) => {
                        hideLoading();
                        let errorMessage;
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = "Izinkan akses lokasi untuk absensi.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = "Informasi lokasi tidak tersedia.";
                                break;
                            case error.TIMEOUT:
                                errorMessage = "Permintaan lokasi habis waktu.";
                                break;
                            case error.UNKNOWN_ERROR:
                                errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                                break;
                        }
                        Swal.fire('Gagal!', errorMessage, 'error');
                        console.error("Error getting location:", error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000, // 10 detik
                        maximumAge: 0 // Tidak menggunakan cache lokasi
                    }
                );
            } else {
                Swal.fire('Perhatian!', "Geolocation tidak didukung oleh browser Anda.", 'warning');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>