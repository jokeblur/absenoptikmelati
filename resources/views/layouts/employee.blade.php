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
    
    <link rel="manifest" href="/absensioptik/public/manifest.json">
    <meta name="theme-color" content="#dc2626">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
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
            background-image: url('/absensioptik/public/image/optikmelati.jpg');
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0.08;
            z-index: -1;
        }
        
        /* Apply Poppins to all elements */
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Profile Section - Left Side */
        .profile-section {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 18px 25px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #dc2626;
            max-width: 380px;
            min-width: 320px;
        }
        
        .profile-section .profile-photo {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid #dc2626;
        }
        
        .profile-section .profile-photo img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }
        
        .profile-section .profile-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            flex: 1;
        }
        
        .profile-section .profile-info .name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #dc2626;
            white-space: nowrap;
            overflow: hidden;
            text-decoration: none;
            line-height: 1.2;
        }
        
        .profile-section .profile-info .branch {
            font-size: 0.85rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }
        
        .profile-section:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.2);
        }
        
        /* Logout Button - Right Side */
        .logout-section {
            position: fixed;
            top: 40px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        /* Profile Logo - Removed since moved to left */
        .profile-logo {
            display: none;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            color: white;
        }
        
        .logout-btn i {
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
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
            padding: 18px 0;
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
            padding: 10px 12px;
            border-radius: 12px;
            min-width: 70px;
        }
        
        .footer-nav .nav-item:hover,
        .footer-nav .nav-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .footer-nav .nav-item i {
            font-size: 1.6rem;
            margin-bottom: 6px;
            display: block;
        }
        
        .footer-nav .nav-item span {
            font-size: 0.8rem;
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
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
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 4px solid #dc2626;
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
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
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
            .profile-section {
                top: 15px;
                left: 15px;
                padding: 15px 20px;
                max-width: 280px;
                min-width: 260px;
                gap: 12px;
            }
            
            .profile-section .profile-photo {
                width: 60px;
                height: 60px;
            }
            
            .profile-section .profile-info .name {
                font-size: 1rem;
            }
            
            .profile-section .profile-info .branch {
                font-size: 0.8rem;
            }
            
            .logout-section {
                top: 40px;
                right: 15px;
                gap: 8px;
            }
            
            .logout-btn {
                padding: 10px 12px;
            }
            
            .main-content {
                padding: 50px;
                margin-bottom: 120px;
                margin-top: 80px;
            }
            
            .footer-nav {
                padding: 20px 0;
            }
            
            .footer-nav .nav-item {
                padding: 12px 8px;
                min-width: 75px;
            }
            
            .footer-nav .nav-item i {
                font-size: 1.8rem;
                margin-bottom: 8px;
            }
            
            .footer-nav .nav-item span {
                font-size: 0.85rem;
                font-weight: 600;
            }
            
            /* Jam yang lebih besar di mobile */
            .time-display, .clock-display {
                font-size: 2.5rem !important;
                font-weight: 700 !important;
            }
        }
        
        @media (min-width: 769px) {
            .main-content {
                margin-top: 90px;
            }
            
            /* Jam yang lebih besar di desktop */
            .time-display, .clock-display {
                font-size: 3rem !important;
                font-weight: 700 !important;
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

    <!-- Profile Section - Left Side -->
    <div class="profile-section">
        <a href="{{ route('employee.profile') }}" class="profile-photo">
            <img src="{{ asset('image/optik-melati.png') }}" alt="Optik Melati">
        </a>
        <div class="profile-info">
            <a href="{{ route('employee.profile') }}" class="name">{{ Auth::user()->name }}</a>
            <div class="branch">{{ Auth::user()->branch->name ?? 'Tidak ada cabang' }}</div>
        </div>
    </div>

    <!-- Floating Logout Section - Right Side -->
    <div class="logout-section">
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-warning mb-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; padding: 0; font-size: 1.5rem;">
                <i class="fas fa-arrow-left"></i>
            </a>
        @endif
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
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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
            $('.footer-nav .nav-item, .btn, .logout-btn, .profile-logo').on('touchstart', function() {
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
    <script>
// Push Notification Registration
const VAPID_PUBLIC_KEY = "BLlEaEhdu4qVHxcr0yZUZEwJ8GKnlKTgO3skM-QhIB_AGAttEJUmI4G0Zh5cZo17re23cLF9o0fuZrBlvh6tlI0";

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.ready.then(function(registration) {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                registration.pushManager.getSubscription().then(function(existingSub) {
                    if (!existingSub) {
                        registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                        }).then(function(subscription) {
                            // Kirim subscription ke server
                            fetch('/employee/save-subscription', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                body: JSON.stringify(subscription)
                            });
                        });
                    }
                });
            }
        });
    });
}
</script>
</body>
</html>