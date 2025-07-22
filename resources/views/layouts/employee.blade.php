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
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        /* Background with Optik Melati image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('image/optikmelati.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.05;
            z-index: -1;
        }
        
        /* Apply Poppins to all elements */
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Top Header with Logo and Logout */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 3px solid #dc2626;
        }
        
        .top-header .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .top-header .logo-section img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .top-header .logo-section .brand-text {
            font-weight: 600;
            font-size: 1.1rem;
            color: #dc2626;
        }
        
        .top-header .logout-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }
        
        .top-header .logout-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }
        
        .top-header .logout-btn i {
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-top: 80px;
            margin-bottom: 100px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        /* Card styling */
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-bottom: none;
            padding: 20px;
            color: white;
            font-weight: 600;
        }
        
        .card-body {
            padding: 25px;
            background: rgba(255, 255, 255, 0.98);
        }
        
        .card-title {
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        /* Bottom Footer Menu */
        .footer-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 15px 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border-top: 3px solid #ef4444;
        }
        
        .footer-nav .nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 10px;
        }
        
        .footer-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 12px;
            min-width: 60px;
        }
        
        .footer-nav .nav-item:hover,
        .footer-nav .nav-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .footer-nav .nav-item i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            display: block;
        }
        
        .footer-nav .nav-item span {
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
        }
        
        /* Button styling */
        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
        }
        
        .btn-info {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(8, 145, 178, 0.3);
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #0e7490 0%, #155e75 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(8, 145, 178, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.4);
        }
        
        .btn-lg {
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 15px;
        }
        
        /* Form styling */
        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
            background: white;
        }
        
        .form-label {
            color: #374151;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        /* Alert styling */
        .alert {
            border: none;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border-left: 4px solid #059669;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border-left: 4px solid #d97706;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #dc2626;
        }
        
        /* Badge styling */
        .badge {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge.bg-primary {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        }
        
        .badge.bg-info {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%) !important;
        }
        
        .badge.bg-warning {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            color: white !important;
        }
        
        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #dc2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Table styling */
        .table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table thead {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table tbody td {
            padding: 15px;
            border-color: #f3f4f6;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .top-header {
                padding: 12px 15px;
            }
            
            .top-header .logo-section .brand-text {
                font-size: 1rem;
            }
            
            .top-header .logout-btn {
                padding: 8px 12px;
            }
            
            .main-content {
                padding: 15px;
                margin-top: 70px;
            }
            
            .footer-nav .nav-item i {
                font-size: 1.3rem;
            }
            
            .footer-nav .nav-item span {
                font-size: 0.7rem;
            }
        }
        
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Hide scrollbar but keep functionality */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
    </style>
    @stack('styles')
</head>
<body>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <img src="{{ asset('image/optik-melati.png') }}" alt="Logo Optik Melati">
            <div class="brand-text">Absensi Optik Melati</div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="d-inline" id="logoutForm">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bottom Footer Menu -->
    <nav class="footer-nav">
        <div class="nav-container">
            <a class="nav-item {{ Request::routeIs('employee.dashboard') ? 'active' : '' }}" href="{{ route('employee.dashboard') }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a class="nav-item {{ Request::routeIs('employee.pengajuan.index') ? 'active' : '' }}" href="{{ route('employee.pengajuan.index') }}">
                <i class="fas fa-file-alt"></i>
                <span>Pengajuan</span>
            </a>
            <a class="nav-item {{ Request::routeIs('employee.history') ? 'active' : '' }}" href="{{ route('employee.history') }}">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </a>
            <a class="nav-item {{ Request::routeIs('employee.profile') ? 'active' : '' }}" href="{{ route('employee.profile') }}">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
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

        // Handle logout confirmation
        $(document).ready(function() {
            $('#logoutForm').on('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    backdrop: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading();
                        this.submit();
                    }
                });
            });
            
            // Smooth scrolling and animations
            $('body').addClass('loaded');
            
            // Add touch feedback for mobile
            $('.footer-nav .nav-item, .btn, .top-header .logout-btn').on('touchstart', function() {
                $(this).addClass('touching');
            }).on('touchend touchcancel', function() {
                $(this).removeClass('touching');
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
                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                        console.error("Error getting location:", error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000, // 10 detik
                        maximumAge: 0 // Tidak menggunakan cache lokasi
                    }
                );
            } else {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Geolocation tidak didukung oleh browser Anda.',
                    icon: 'warning',
                    confirmButtonColor: '#dc2626'
                });
            }
        }

        // Add CSS for touch feedback
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .touching {
                    transform: scale(0.95) !important;
                    transition: transform 0.1s ease !important;
                }
                
                .loaded .card {
                    animation: fadeInUp 0.6s ease-out;
                }
                
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `)
            .appendTo('head');
    </script>
    @stack('scripts')
</body>
</html>