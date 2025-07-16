@extends('layouts.employee')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Apply Poppins font to dashboard */
    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .card, .btn, .alert, h1, h2, h3, h4, h5, h6, p, span {
        font-family: 'Poppins', sans-serif;
    }
    
    .card-header h5 {
        font-weight: 600;
    }
    
    .btn {
        font-weight: 500;
    }
    
    .badge {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }
    
    /* Google Maps styling */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
    }
    
    .map-info {
        background: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 12px;
        max-width: 200px;
    }
    
    .map-info h6 {
        margin: 0 0 5px 0;
        font-weight: 600;
        color: #333;
    }
    
    .map-info p {
        margin: 0;
        color: #666;
        font-size: 11px;
    }
    
    .location-details {
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    
    .location-details h6 {
        margin-bottom: 10px;
        color: #007bff;
        font-weight: 600;
    }
    
    .location-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 5px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .location-item:last-child {
        border-bottom: none;
    }
    
    .location-label {
        font-weight: 500;
        color: #495057;
    }
    
    .location-value {
        font-weight: 600;
        color: #007bff;
    }

    /* Attendance status styling */
    .attendance-status-item {
        padding: 1rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .attendance-status-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .attendance-status-item h6 {
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    
    .attendance-status-item .badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .attendance-status-item p {
        margin-bottom: 0.5rem;
    }
    
    .attendance-status-item p:last-child {
        margin-bottom: 0;
    }
    
    .attendance-status-item .text-danger.small {
        font-size: 0.8rem;
    }
    
    .attendance-status-item .text-info.small {
        font-size: 0.8rem;
    }
    
    /* Summary section styling */
    .attendance-summary {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 1px solid #90caf9;
        border-radius: 0.5rem;
        padding: 1rem;
    }
    
    /* Empty state styling */
    .empty-attendance {
        text-align: center;
        padding: 2rem 1rem;
    }
    
    .empty-attendance i {
        color: #6c757d;
        margin-bottom: 1rem;
    }
    
    .empty-attendance p {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .empty-attendance .small {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
    <div class="text-center mb-4">
        {{-- Bagian untuk menampilkan pesan sukses atau error umum --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Bagian untuk menampilkan error validasi form --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- Foto Profil --}}
        <div class="mb-3">
            @if(Auth::user()->profile_photo)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" 
                     alt="Foto Profil" 
                     class="img-thumbnail rounded-circle" 
                     style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #3498db;">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=100&background=3498db&color=fff" 
                     alt="Foto Profil" 
                     class="img-thumbnail rounded-circle" 
                     style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #3498db;">
            @endif
        </div>
        
        <h2 class="mt-4">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-muted">Cabang: {{ Auth::user()->branch->name ?? 'Belum Ditentukan' }}</p>
        <p class="h5 mt-3">Jam Sekarang: <span id="current-time"></span></p>

        {{-- Tampilkan Jam Masuk Kustom --}}
        @if (Auth::user()->custom_clock_in_time)
            <p class="h5 mt-2">Jam Masuk Kustom Anda: <span class="badge bg-info">{{ \Carbon\Carbon::parse(Auth::user()->custom_clock_in_time)->format('H:i') }}</span></p>
        @else
            <p class="h5 mt-2 text-muted">Jam Masuk Kustom: Belum Diatur</p>
        @endif

        {{-- Tampilkan Jam Pulang Kustom --}}
        @if (Auth::user()->custom_clock_out_time)
            <p class="h5 mt-2">Jam Pulang Kustom Anda: <span class="badge bg-info">{{ \Carbon\Carbon::parse(Auth::user()->custom_clock_out_time)->format('H:i') }}</span></p>
        @else
            <p class="h5 mt-2 text-muted">Jam Pulang Kustom: Belum Diatur</p>
        @endif
    </div>

    <div class="row">
        <div class="col-6 d-grid gap-2">
            @if (!$hasClockedIn)
                <button id="clockInBtn" class="btn btn-success btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Absen Masuk
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-check me-2"></i>Sudah Absen Masuk
                </button>
            @endif
        </div>
        <div class="col-6 d-grid gap-2">
            @if ($hasClockedIn && !$hasClockedOut)
                <button id="clockOutBtn" class="btn btn-danger btn-lg">
                    <i class="fas fa-sign-out-alt me-2"></i>Absen Pulang
                </button>
            @elseif (!$hasClockedIn)
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-clock me-2"></i>Absen Pulang
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-check me-2"></i>Sudah Absen Pulang
                </button>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-clock me-2"></i>Status Absensi Hari Ini
            </h5>
        </div>
        <div class="card-body">
            @if ($attendanceToday)
                <div class="row">
                    <div class="col-md-6 text-center">
                        <div class="attendance-status-item">
                            <h6 class="text-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Absen Masuk
                            </h6>
                            @if ($attendanceToday->check_in)
                                <p class="mb-1">
                                    <strong>Jam:</strong> 
                                    <span class="badge bg-success">
                                        {{ \Carbon\Carbon::parse($attendanceToday->check_in)->format('H:i:s') }}
                                    </span>
                                </p>
                                <p class="mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge {{ $attendanceToday->status_in == 'terlambat' ? 'bg-danger' : 'bg-success' }}">
                    {{ ucfirst($attendanceToday->status_in) }}
                                    </span>
                                </p>
                                @if ($attendanceToday->status_in == 'terlambat')
                                    <p class="text-danger small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                    Terlambat {{ $attendanceToday->late_minutes ?? '0' }} menit
                </p>
                                @endif
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Belum absen masuk
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="attendance-status-item">
                            <h6 class="text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Absen Pulang
                            </h6>
                            @if ($attendanceToday->check_out)
                                <p class="mb-1">
                                    <strong>Jam:</strong> 
                                    <span class="badge bg-success">
                                        {{ \Carbon\Carbon::parse($attendanceToday->check_out)->format('H:i:s') }}
                                    </span>
                                </p>
                                <p class="mb-1">
                                    <strong>Status:</strong> 
                                    <span class="badge {{ $attendanceToday->status_out == 'lembur' ? 'bg-info' : 'bg-success' }}">
                                        {{ ucfirst($attendanceToday->status_out) ?? 'Normal' }}
                                    </span>
                                </p>
                                @if ($attendanceToday->status_out == 'lembur')
                                    <p class="text-info small">
                                        <i class="fas fa-moon me-1"></i>
                                        Lembur hari ini
                                    </p>
                                @endif
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Belum absen pulang
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if ($attendanceToday->check_in && $attendanceToday->check_out)
                    <hr class="my-3">
                    <div class="attendance-summary">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-info">
                                    <i class="fas fa-calendar-day me-2"></i>Ringkasan Hari Ini
                                </h6>
                                @php
                                    $checkIn = \Carbon\Carbon::parse($attendanceToday->check_in);
                                    $checkOut = \Carbon\Carbon::parse($attendanceToday->check_out);
                                    $workHours = $checkIn->diffInHours($checkOut);
                                    $workMinutes = $checkIn->diffInMinutes($checkOut) % 60;
                                @endphp
                                <p class="mb-1">
                                    <strong>Total Jam Kerja:</strong> 
                                    <span class="badge bg-info">
                                        {{ $workHours }} jam {{ $workMinutes }} menit
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-attendance">
                    <i class="fas fa-calendar-day fa-3x"></i>
                    <p>Belum ada data absensi hari ini</p>
                    <p class="small">Silakan lakukan absen masuk untuk memulai hari kerja Anda</p>
                </div>
            @endif
            
            <hr class="my-3">
            <p id="location-status" class="text-info mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>Mencari lokasi...
            </p>
        </div>
    </div>

    {{-- Peta Lokasi --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-map-marker-alt me-2"></i>
                Peta Lokasi
            </h5>
            <button id="refreshLocationBtn" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-sync-alt me-1"></i>
                Refresh Lokasi
            </button>
        </div>
        <div class="card-body">
            <div id="map"></div>
            
            {{-- Detail Lokasi --}}
            <div class="location-details" id="location-details" style="display: none;">
                <h6><i class="fas fa-info-circle me-2"></i>Detail Lokasi</h6>
                <div class="location-item">
                    <span class="location-label">Lokasi Anda:</span>
                    <span class="location-value" id="user-location">-</span>
                </div>
                <div class="location-item">
                    <span class="location-label">Kantor Cabang:</span>
                    <span class="location-value" id="office-location">-</span>
                </div>
                <div class="location-item">
                    <span class="location-label">Jarak ke Kantor:</span>
                    <span class="location-value" id="distance-to-office">-</span>
                </div>
                <div class="location-item">
                    <span class="location-label">Radius Absensi:</span>
                    <span class="location-value" id="attendance-radius">-</span>
                </div>
                <div class="location-item">
                    <span class="location-label">Status Lokasi:</span>
                    <span class="location-value" id="location-status-badge">-</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Data tersembunyi untuk JavaScript --}}
    <div style="display: none;">
        <span id="office-name">{{ Auth::user()->branch->name ?? 'Kantor Cabang' }}</span>
        <span id="office-address">{{ Auth::user()->branch->address ?? 'Alamat tidak tersedia' }}</span>
        <span id="office-lat">{{ Auth::user()->branch->latitude ?? 0 }}</span>
        <span id="office-lng">{{ Auth::user()->branch->longitude ?? 0 }}</span>
        <span id="office-radius">{{ Auth::user()->branch->attendance_radius ?? 100 }}</span>
    </div>

    {{-- Form tersembunyi tidak diperlukan lagi jika menggunakan AJAX untuk pengiriman data --}}
    {{-- Kecuali jika Anda ingin menggunakan form fallback tanpa JavaScript --}}
    {{-- Untuk contoh ini, saya berasumsi Anda hanya menggunakan AJAX --}}
    <!-- <pre>
    user_id: {{ Auth::user()->id }}
    hasClockedIn: {{ var_export($hasClockedIn, true) }}
    hasClockedOut: {{ var_export($hasClockedOut, true) }}
    attendanceToday: {{ var_export(isset($attendanceToday) ? $attendanceToday : null, true) }}
</pre> -->
@endsection

@push('scripts')
<!-- Google Maps API -->
<script>
    // Fallback jika Google Maps API gagal dimuat
    window.gm_authFailure = function() {
        console.log('Google Maps API authentication failed');
        $('#map').html(`
            <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="text-warning">Gagal memuat peta</h6>
                    <p class="text-muted small">Google Maps API tidak tersedia</p>
                </div>
            </div>
        `);
    };
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAtKnBpdNEsqcLOaluyGJJ7zUS4SekC_Dc" async defer></script>

<script>
    // Variabel global untuk peta dan marker
    let map;
    let userMarker;
    let officeMarker;
    let userLocation = null;
    let officeLocation = null;
    
    // Data kantor cabang dari server
    const officeData = {
        name: document.getElementById('office-name').textContent,
        address: document.getElementById('office-address').textContent,
        latitude: parseFloat(document.getElementById('office-lat').textContent),
        longitude: parseFloat(document.getElementById('office-lng').textContent),
        radius: parseInt(document.getElementById('office-radius').textContent)
    };
    
    console.log('Office data loaded:', officeData);

    // Fungsi untuk mendapatkan token CSRF dari meta tag
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    // Fungsi untuk menampilkan indikator loading (sesuaikan dengan implementasi Anda)
    function showLoading() {
        // Contoh sederhana: Menambahkan kelas 'disabled' dan teks loading
        $('#clockInBtn, #clockOutBtn').prop('disabled', true).text('Memproses...');
        $('#location-status').text('Mengirim data...');
        // Anda bisa menambahkan SweetAlert2 loading atau spinner lainnya di sini
        // Swal.fire({
        //     title: 'Mohon Tunggu!',
        //     text: 'Sedang memproses absensi...',
        //     allowOutsideClick: false,
        //     showConfirmButton: false,
        //     didOpen: () => {
        //         Swal.showLoading();
        //     }
        // });
    }

    // Fungsi untuk menyembunyikan indikator loading (sesuaikan dengan implementasi Anda)
    function hideLoading() {
        // Contoh sederhana: Mengembalikan teks dan status tombol
        $('#clockInBtn').text('Absen Masuk').prop('disabled', false);
        $('#clockOutBtn').text('Absen Pulang').prop('disabled', false);
        $('#location-status').text('Lokasi siap.'); // Atau status lokasi terakhir
        // Swal.close(); // Tutup SweetAlert2 loading jika digunakan
    }

    // Fungsi untuk mendapatkan lokasi pengguna
    function getLocation(callback) {
        $('#location-status').text('Mendapatkan lokasi...');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    $('#location-status').text('Lokasi didapatkan.');
                    const coords = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    
                    // Update peta dengan lokasi pengguna jika peta tersedia
                    if (typeof google !== 'undefined' && google.maps && map) {
                        updateMapWithUserLocation(coords);
                    } else {
                        // Fallback: tampilkan koordinat saja
                        showCoordinatesOnly(coords);
                    }
                    
                    callback(coords);
                },
                function(error) {
                    let errorMessage = "Terjadi kesalahan saat mendapatkan lokasi.";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = "Izin lokasi ditolak. Silakan izinkan akses lokasi untuk absensi.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = "Informasi lokasi tidak tersedia. Coba lagi.";
                            break;
                        case error.TIMEOUT:
                            errorMessage = "Waktu permintaan lokasi habis. Coba lagi.";
                            break;
                        case error.UNKNOWN_ERROR:
                            errorMessage = "Terjadi kesalahan yang tidak diketahui saat mendapatkan lokasi.";
                            break;
                    }
                    $('#location-status').text(errorMessage);
                    
                    // Tampilkan pesan error yang lebih user-friendly
                    if (error.code === error.PERMISSION_DENIED) {
                        Swal.fire({
                            title: 'Izin Lokasi Diperlukan',
                            text: 'Untuk dapat melakukan absensi, Anda perlu mengizinkan akses lokasi. Silakan refresh halaman dan izinkan akses lokasi.',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    } else {
                    Swal.fire('Gagal!', errorMessage, 'error');
                    }
                    
                    hideLoading(); // Pastikan loading disembunyikan jika ada error
                },
                {
                    enableHighAccuracy: true, // Mencoba mendapatkan lokasi seakurat mungkin
                    timeout: 10000,           // Batas waktu 10 detik
                    maximumAge: 0             // Tidak menggunakan cache lokasi lama
                }
            );
        } else {
            Swal.fire({
                title: 'Browser Tidak Didukung',
                text: 'Browser Anda tidak mendukung geolocation. Silakan gunakan browser yang lebih baru.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            $('#location-status').text('Geolocation tidak didukung.');
            hideLoading();
        }
    }

    // Fungsi untuk menghitung jarak antara dua titik
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c; // Jarak dalam meter
    }

    // Fungsi untuk inisialisasi peta
    function initMap() {
        try {
            console.log('Initializing map...');
            console.log('Office data:', officeData);
            
            // Default center (bisa diubah sesuai kebutuhan)
            const defaultCenter = { lat: officeData.latitude || -6.2088, lng: officeData.longitude || 106.8456 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            });

            console.log('Map initialized successfully');

            // Tambahkan marker kantor jika koordinat tersedia
            if (officeData.latitude && officeData.longitude && officeData.latitude !== 0 && officeData.longitude !== 0) {
                officeLocation = { lat: officeData.latitude, lng: officeData.longitude };
                
                officeMarker = new google.maps.Marker({
                    position: officeLocation,
                    map: map,
                    title: officeData.name,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                        scaledSize: new google.maps.Size(32, 32)
                    }
                });

                // Info window untuk kantor
                const officeInfoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="map-info">
                            <h6>🏢 ${officeData.name}</h6>
                            <p>📍 ${officeData.address}</p>
                            <p>📏 Radius: ${officeData.radius}m</p>
                        </div>
                    `
                });

                officeMarker.addListener('click', () => {
                    officeInfoWindow.open(map, officeMarker);
                });
                
                console.log('Office marker added');
            } else {
                // Jika koordinat kantor tidak tersedia, tampilkan pesan
                $('#map').html(`
                    <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                        <div class="text-center">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Koordinat kantor belum diatur</h6>
                            <p class="text-muted small">Admin perlu mengatur koordinat kantor cabang</p>
                        </div>
                    </div>
                `);
                console.log('Office coordinates not available');
            }
        } catch (error) {
            console.error('Error initializing map:', error);
            $('#map').html(`
                <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="text-warning">Gagal memuat peta</h6>
                        <p class="text-muted small">Google Maps API tidak tersedia</p>
                    </div>
                </div>
            `);
        }
    }

    // Fungsi untuk update peta dengan lokasi pengguna
    function updateMapWithUserLocation(coords) {
        userLocation = { lat: coords.latitude, lng: coords.longitude };
        
        // Cek apakah peta sudah diinisialisasi
        if (!map) {
            console.error('Map not initialized');
            return;
        }
        
        // Hapus marker pengguna yang lama jika ada
        if (userMarker) {
            userMarker.setMap(null);
        }

        // Tambahkan marker pengguna
        userMarker = new google.maps.Marker({
            position: userLocation,
            map: map,
            title: 'Lokasi Anda',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(32, 32)
            }
        });

        // Info window untuk pengguna
        const userInfoWindow = new google.maps.InfoWindow({
            content: `
                <div class="map-info">
                    <h6>📍 Lokasi Anda</h6>
                    <p>Lat: ${coords.latitude.toFixed(6)}</p>
                    <p>Lng: ${coords.longitude.toFixed(6)}</p>
                </div>
            `
        });

        userMarker.addListener('click', () => {
            userInfoWindow.open(map, userMarker);
        });

        // Hitung jarak ke kantor
        let distance = 0;
        let isWithinRadius = false;
        
        if (officeLocation) {
            distance = calculateDistance(
                coords.latitude, coords.longitude,
                officeLocation.lat, officeLocation.lng
            );
            isWithinRadius = distance <= officeData.radius;
        }

        // Update detail lokasi
        updateLocationDetails(coords, distance, isWithinRadius);

        // Fit bounds untuk menampilkan kedua marker
        if (officeLocation) {
            const bounds = new google.maps.LatLngBounds();
            bounds.extend(userLocation);
            bounds.extend(officeLocation);
            map.fitBounds(bounds);
            
            // Tambahkan padding
            map.setZoom(Math.min(map.getZoom(), 16));
        } else {
            map.setCenter(userLocation);
            map.setZoom(16);
        }
    }

    // Fungsi untuk update detail lokasi
    function updateLocationDetails(coords, distance, isWithinRadius) {
        $('#user-location').text(`${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`);
        
        if (officeData.latitude && officeData.longitude && officeData.latitude !== 0 && officeData.longitude !== 0) {
            $('#office-location').text(`${officeData.latitude.toFixed(6)}, ${officeData.longitude.toFixed(6)}`);
            $('#distance-to-office').text(`${Math.round(distance)}m`);
            $('#attendance-radius').text(`${officeData.radius}m`);
            
            const statusText = isWithinRadius ? 'Dalam Radius' : 'Di Luar Radius';
            const statusClass = isWithinRadius ? 'text-success' : 'text-danger';
            $('#location-status-badge').text(statusText).removeClass('text-success text-danger').addClass(statusClass);
        } else {
            $('#office-location').text('Belum diatur');
            $('#distance-to-office').text('-');
            $('#attendance-radius').text(`${officeData.radius}m`);
            $('#location-status-badge').text('Tidak dapat dihitung').removeClass('text-success text-danger').addClass('text-warning');
        }
        
        // Tampilkan detail lokasi
        $('#location-details').show();
    }

    // Fungsi untuk menampilkan koordinat tanpa peta (fallback)
    function showCoordinatesOnly(coords) {
        $('#map').html(`
            <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                <div class="text-center">
                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                    <h6 class="text-primary">Lokasi Anda</h6>
                    <p class="text-muted">Latitude: ${coords.latitude.toFixed(6)}</p>
                    <p class="text-muted">Longitude: ${coords.longitude.toFixed(6)}</p>
                    <small class="text-muted">Peta tidak tersedia, menampilkan koordinat saja</small>
                </div>
            </div>
        `);
    }

    // Fungsi untuk update jam saat ini
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', options);
    }

    // Jalankan saat DOM siap
    $(document).ready(function() {
        // Update jam setiap detik
        setInterval(updateClock, 1000);
        updateClock(); // Panggil pertama kali agar langsung tampil

        // Inisialisasi peta setelah Google Maps API dimuat
        setTimeout(() => {
            if (typeof google !== 'undefined' && google.maps) {
                try {
                    initMap();
                } catch (error) {
                    console.error('Error initializing map:', error);
                }
            }
        }, 2000);

        // Dapatkan lokasi pengguna secara otomatis
        setTimeout(() => {
            getLocation(function(coords) {
                console.log('Lokasi pengguna:', coords);
                // Update detail lokasi bahkan jika peta tidak tersedia
                if (officeData.latitude && officeData.longitude && officeData.latitude !== 0 && officeData.longitude !== 0) {
                    const distance = calculateDistance(
                        coords.latitude, coords.longitude,
                        officeData.latitude, officeData.longitude
                    );
                    const isWithinRadius = distance <= officeData.radius;
                    updateLocationDetails(coords, distance, isWithinRadius);
                } else {
                    updateLocationDetails(coords, 0, false);
                }
            });
        }, 3000); // Tunggu sedikit lebih lama agar peta sudah ter-load

        // Tambahkan event listener untuk tombol refresh lokasi dengan error handling
        $('#refreshLocationBtn').click(function() {
            if (!navigator.geolocation) {
                Swal.fire('Error!', 'Geolocation tidak didukung oleh browser ini.', 'error');
                return;
            }
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Mendapatkan Lokasi...');
            getLocation(function(coords) {
                $('#refreshLocationBtn').prop('disabled', false).html('<i class="fas fa-sync-alt me-1"></i>Refresh Lokasi');
                Swal.fire({
                    title: 'Lokasi Diperbarui!',
                    text: `Lokasi Anda: ${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        });

        // Mengatur token CSRF untuk semua permintaan AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        // Event listener untuk tombol Absen Masuk
        $('#clockInBtn').click(function() {
            showLoading(); // Tampilkan loading
            getLocation(function(coords) {
                $.ajax({
                    url: "{{ route('employee.absensi.clock_in') }}",
                    type: "POST",
                    data: {
                        latitude_in: coords.latitude,
                        longitude_in: coords.longitude
                    },
                    success: function(response) {
                        hideLoading();
                        if(response.status_in === 'terlambat') {
                            // Bulatkan menit ke atas (ceil) atau ke bawah (floor) sesuai kebutuhan
                            var menit = Math.round(response.menit_terlambat); // pembulatan ke terdekat
                            // var menit = Math.ceil(response.menit_terlambat); // pembulatan ke atas
                            // var menit = Math.floor(response.menit_terlambat); // pembulatan ke bawah

                            Swal.fire({
                                title: 'Terlambat!',
                                text: 'Anda terlambat ' + menit + ' Menit',
                                icon: 'warning',
                                timer: 8000,
                                timerProgressBar: true,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 5000, // 5 detik
                                timerProgressBar: true,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        hideLoading(); // Sembunyikan loading
                        let errorMessage = 'Terjadi kesalahan saat absen masuk.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON.errors) { // Error validasi Laravel (status 422)
                            errorMessage = "Periksa kembali input Anda:<br>";
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += "- " + value[0] + "<br>";
                            });
                        }
                        Swal.fire('Gagal!', errorMessage, 'error');
                    }
                });
            });
        });

        // Event listener untuk tombol Absen Pulang
        $('#clockOutBtn').click(function() {
            showLoading(); // Tampilkan loading
            getLocation(function(coords) {
                $.ajax({
                    url: "{{ route('employee.absensi.clock_out') }}",
                    type: "POST",
                    data: {
                        latitude_out: coords.latitude,
                        longitude_out: coords.longitude
                    },
                    success: function(response) {
                        hideLoading(); // Sembunyikan loading
                        Swal.fire('Berhasil!', response.message, 'success')
                            .then((result) => {
                                if (result.isConfirmed || result.isDismissed) {
                                    location.reload(); // Refresh halaman untuk update status tombol
                                }
                            });
                    },
                    error: function(xhr) {
                        hideLoading(); // Sembunyikan loading
                        let errorMessage = 'Terjadi kesalahan saat absen pulang.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON.errors) { // Error validasi Laravel (status 422)
                            errorMessage = "Periksa kembali input Anda:<br>";
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += "- " + value[0] + "<br>";
                            });
                        }
                        Swal.fire('Gagal!', errorMessage, 'error');
                    }
                });
            });
        });
    });
</script>
@endpush