<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Google Fonts: Poppins -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="manifest" href="/absensioptik/public/manifest.json">
        <meta name="theme-color" content="#dc2626">
        <style>
            body {
                font-family: 'Poppins', 'Figtree', sans-serif;
                background: #f4f8fb !important;
                min-height: 100vh;
            }
            .login-wave {
                width: 100%;
                height: 48vh;
                min-height: 260px;
                max-height: 420px;
                background: none;
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1;
            }
            @media (max-width: 600px) {
                .login-wave {
                    height: 62vw;
                    min-height: 180px;
                    max-height: 320px;
                }
                .login-container {
                    padding-top: 22vw;
                }
            }
            .login-container {
                position: relative;
                z-index: 2;
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                min-height: 100vh;
                padding-top: 18vh;
            }
            .login-logo {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 1.2rem;
                margin-top: 0;
                z-index: 3;
                position: relative;
            }
            .login-logo img {
                width: 110px;
                height: 110px;
                object-fit: contain;
                background: #fff;
                border-radius: 50%;
                box-shadow: 0 2px 12px rgba(44,62,80,0.10);
                padding: 10px;
            }
            .login-title {
                font-weight: 700;
                color: #2c3e50;
                font-size: 1.3rem;
                margin-bottom: 0.2rem;
                text-align: center;
            }
            .login-subtitle {
                color: #7f8c8d;
                font-size: 1rem;
                text-align: center;
                margin-bottom: 1.2rem;
            }
            .login-form {
                width: 100%;
                margin-top: 0.5rem;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .form-group {
                position: relative;
                margin-bottom: 2.1rem;
                width: 100%;
                max-width: 320px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .form-label {
                color: #7f8c8d;
                font-weight: 400;
                font-size: 0.92rem;
                margin-bottom: 0.1rem;
                margin-left: 0.1rem;
                letter-spacing: 0.01em;
                text-align: center;
                width: 100%;
            }
            .form-control {
                border: none;
                border-bottom: 2.5px solid #e0e4ea;
                border-radius: 0;
                background: transparent;
                font-size: 1.13rem;
                padding: 0.85rem 2.2rem 0.6rem 2.2rem;
                color: #2c3e50;
                box-shadow: none;
                transition: border-color 0.2s;
                font-weight: 500;
                width: 100%;
                max-width: 320px;
                text-align: center;
            }
            .form-control:focus {
                border-bottom: 2.5px solid #3498db;
                outline: none;
                box-shadow: none;
                background: transparent;
            }
            .input-icon {
                position: absolute;
                left: 0.7rem;
                top: 50%;
                transform: translateY(-50%);
                color: #bdc3c7;
                font-size: 1.18rem;
                pointer-events: none;
            }
            .form-control::placeholder {
                color: #b0b8c1;
                opacity: 1;
                font-size: 1.08rem;
                font-weight: 400;
            }
            .form-control:focus + .input-icon {
                color: #3498db;
            }
            .btn-primary, .x-primary-button {
                background: #2c3e50;
                border: none;
                border-radius: 1.5rem;
                color: #fff;
                font-weight: 600;
                padding: 0.95rem 1.5rem;
                font-size: 1.13rem;
                transition: background 0.2s;
                width: 70%;
                margin: 1.2rem auto 0 auto;
                box-shadow: 0 2px 8px rgba(44,62,80,0.08);
                display: block;
                text-align: center;
                max-width: 320px;
            }
            .btn-primary:hover, .x-primary-button:hover {
                background: #3498db;
                color: #fff;
            }
            .login-footer {
                text-align: center;
                margin-top: 2.5rem;
                color: #7f8c8d;
                font-size: 0.95rem;
            }
            .block.mt-4.d-flex {
                margin-top: 0.5rem !important;
                margin-bottom: 0.5rem !important;
            }
            @media (max-width: 500px) {
                .btn-primary, .x-primary-button, .form-control, .form-group {
                    width: 100%;
                    max-width: 100%;
                }
            }
        </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="login-wave">
            <svg viewBox="0 0 500 400" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:100%;display:block;">
                <path d="M0 0H500V220C500 300 350 400 250 320C150 240 0 340 0 220V0Z" fill="#2c3e50"/>
            </svg>
        </div>
        <div class="login-container">
            <div class="login-logo">
                <img src="{{asset('image/optik-melati.png') }}" alt="Logo Optik Melati" loading="lazy">
            </div>
            <div class="login-title">OPTIK MELATI</div>
            <div class="login-subtitle">Sistem Absensi Karyawan</div>
            <div class="login-form">
                {{ $slot }}
            </div>
            <div class="login-footer">
                &copy; {{ date('Y') }} Optik Melati. All rights reserved.
            </div>
        </div>
        <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/absensioptik/public/service-worker.js')
          .then(function(reg) {
            // console.log('Service worker registered.', reg);
          });
      });
    }
    </script>
    </body>
</html>
