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
    
    /* Leaflet Map styling */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
        z-index: 1;
    }
    
    /* Custom marker styling */
    .custom-office-marker,
    .custom-user-marker {
        background: transparent !important;
        border: none !important;
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

    /* New styles for day and time display */
    .day-display {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .time-display-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 120px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        border: 2px solid rgba(220, 38, 38, 0.3);
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        margin: 0 20px;
    }

    .time-display {
        font-size: 4rem;
        font-weight: 800;
        color: #dc2626;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        letter-spacing: 2px;
        background: rgba(255, 255, 255, 0.9);
        -webkit-background-clip: text;
        background-clip: text;
        filter: drop-shadow(0 0 10px rgba(220, 38, 38, 0.3));
    }

    /* Schedule info styling */
    .schedule-info {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .schedule-info .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    /* Compact alert styling */
    .alert.py-2 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    
    .alert.py-2 .badge {
        font-size: 0.75rem;
        padding: 0.3rem 0.5rem;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .day-display {
            font-size: 1.8rem;
        }
        
        .time-display-container {
            min-height: 100px;
            margin: 0 10px;
            padding: 15px;
        }
        
        .time-display {
            font-size: 3rem;
        }
        
        .schedule-info .badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .day-display {
            font-size: 1.6rem;
        }
        
        .time-display-container {
            min-height: 90px;
            margin: 0 5px;
            padding: 12px;
        }
        
        .time-display {
            font-size: 2.5rem;
            letter-spacing: 1px;
        }
        
        .schedule-info .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }
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
        <!-- <div class="mb-3">
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
        </div> -->
        
        {{-- Tampilan Hari dan Jam yang Diperbesar --}}
        <div class="text-center mb-4">
            <h1 class="day-display mb-2">
                <i class="fas fa-calendar-day me-2 "></i>
                <strong>{{ $dayName }}</strong>
            </h1>
            <div class="time-display-container">
                <div class="time-display" id="current-time"></div>
            </div>
        </div>

        @if ($todaySchedule && !$todaySchedule->is_holiday)
            <div class="schedule-info mb-3">
                <span class="text-muted small">
                    <i class="fas fa-clock me-1"></i>Jadwal: 
                    <span class="badge bg-primary mx-1">{{ \Carbon\Carbon::parse($todaySchedule->clock_in)->format('H:i') }}</span>
                    -
                    <span class="badge bg-danger mx-1">{{ \Carbon\Carbon::parse($todaySchedule->clock_out)->format('H:i') }}</span>
                </span>
            </div>
        @endif
    </div>

    @if ($todaySchedule && $todaySchedule->is_holiday || $dayName === 'Minggu')
    <div class="alert alert-info text-center">
        <h4 class="alert-heading"><i class="fas fa-info-circle"></i> Hari Libur</h4>
        <p>Hari ini adalah hari libur sesuai dengan jadwal kerja Anda. Tidak perlu melakukan absensi.</p>
    </div>
    @else
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
    <div class="row mt-3">
        <div class="col-6 d-grid gap-2">
            @if ($hasClockedIn && $attendanceToday && !$attendanceToday->break_start)
                <button id="breakStartBtn" class="btn btn-warning btn-lg">
                    <i class="fas fa-coffee me-2"></i>Mulai Istirahat
                </button>
            @elseif ($hasClockedIn && $attendanceToday && $attendanceToday->break_start)
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-check me-2"></i>Sudah Mulai Istirahat
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-coffee me-2"></i>Mulai Istirahat
                </button>
            @endif
        </div>
        <div class="col-6 d-grid gap-2">
            @if ($hasClockedIn && $attendanceToday && $attendanceToday->break_start && !$attendanceToday->break_end)
                <button id="breakEndBtn" class="btn btn-warning btn-lg">
                    <i class="fas fa-play me-2"></i>Selesai Istirahat
                </button>
            @elseif ($hasClockedIn && $attendanceToday && $attendanceToday->break_start && $attendanceToday->break_end)
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-check me-2"></i>Sudah Selesai Istirahat
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-play me-2"></i>Selesai Istirahat
                </button>
            @endif
        </div>
    </div>
    
    {{-- Informasi Jadwal Istirahat Hari Ini --}}
    @if ($todaySchedule && !$todaySchedule->is_holiday && $todaySchedule->break_start_time && $todaySchedule->break_end_time)
        <div class="alert alert-info mt-3 mb-0 py-2">
            <div class="text-center">
                <small class="text-muted">
                    <i class="fas fa-coffee me-1"></i>Istirahat: 
                    <span class="badge bg-primary mx-1">{{ \Carbon\Carbon::parse($todaySchedule->break_start_time)->format('H:i') }}</span>
                    -
                    <span class="badge bg-warning text-dark mx-1">{{ \Carbon\Carbon::parse($todaySchedule->break_end_time)->format('H:i') }}</span>
                    <span class="d-block d-sm-inline mt-1 mt-sm-0">
                        <i class="fas fa-info-circle me-1"></i>Tanpa toleransi waktu
                    </span>
                </small>
            </div>
        </div>
    @endif
    @endif

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
                
                {{-- Break Times --}}
                <div class="row mt-2">
                    <div class="col-md-6 text-center">
                        <div class="attendance-status-item">
                            <h6 class="text-info">
                                <i class="fas fa-coffee me-2"></i>Mulai Istirahat
                            </h6>
                            @if ($attendanceToday->break_start)
                                <p class="mb-1">
                                    <strong>Jam:</strong> 
                                    <span class="badge bg-info">
                                        {{ \Carbon\Carbon::parse($attendanceToday->break_start)->format('H:i:s') }}
                                    </span>
                                </p>
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Belum mulai istirahat
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="attendance-status-item">
                            <h6 class="text-warning text-dark">
                                <i class="fas fa-play me-2"></i>Selesai Istirahat
                            </h6>
                            @if ($attendanceToday->break_end)
                                <p class="mb-1">
                                    <strong>Jam:</strong> 
                                    <span class="badge bg-warning text-dark">
                                        {{ \Carbon\Carbon::parse($attendanceToday->break_end)->format('H:i:s') }}
                                    </span>
                                </p>
                                @if ($attendanceToday->break_late_minutes > 0)
                                    <p class="text-danger small">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Terlambat {{ $attendanceToday->break_late_minutes }} menit
                                    </p>
                                @endif
                            @else
                                <p class="text-muted">
                                    <i class="fas fa-clock me-1"></i>Belum selesai istirahat
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
                                    $totalDurationMinutes = $checkIn->diffInMinutes($checkOut);
                                    
                                    $breakDurationMinutes = 0;
                                    if ($attendanceToday->break_start && $attendanceToday->break_end) {
                                        $breakStart = \Carbon\Carbon::parse($attendanceToday->break_start);
                                        $breakEnd = \Carbon\Carbon::parse($attendanceToday->break_end);
                                        $breakDurationMinutes = $breakStart->diffInMinutes($breakEnd);
                                    }
                                    
                                    $workDurationMinutes = $totalDurationMinutes - $breakDurationMinutes;
                                    if ($workDurationMinutes < 0) $workDurationMinutes = 0;
                                    
                                    $workHours = floor($workDurationMinutes / 60);
                                    $workMinutes = $workDurationMinutes % 60;
                                @endphp
                                @if ($breakDurationMinutes > 0)
                                <p class="mb-1">
                                    <strong>Total Jam Istirahat:</strong>
                                    <span class="badge bg-secondary">
                                        {{ floor($breakDurationMinutes / 60) }} jam {{ $breakDurationMinutes % 60 }} menit
                                    </span>
                                </p>
                                @endif
                                <p class="mb-1">
                                    <strong>Total Jam Kerja Efektif:</strong> 
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

    {{-- Work Schedule --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Jadwal Kerja Anda
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Hari</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Istirahat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        @endphp
                        @foreach ($days as $index => $day)
                            @php
                                $schedule = $workSchedules->get($day);
                            @endphp
                            <tr>
                                <td>{{ $dayNames[$index] }}</td>
                                @if ($schedule && !$schedule->is_holiday)
                                    <td><span class="badge bg-success">{{ $schedule->clock_in ? \Carbon\Carbon::parse($schedule->clock_in)->format('H:i') : '-' }}</span></td>
                                    <td><span class="badge bg-danger">{{ $schedule->clock_out ? \Carbon\Carbon::parse($schedule->clock_out)->format('H:i') : '-' }}</span></td>
                                    <td>
                                        @if ($schedule->break_start_time && $schedule->break_end_time)
                                            <span class="badge bg-info">{{ \Carbon\Carbon::parse($schedule->break_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end_time)->format('H:i') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @else
                                    <td colspan="3"><span class="badge bg-secondary">Hari Libur</span></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
<!-- Leaflet Map -->
<script>
    // Variabel global untuk peta dan marker
    let map;
    let userMarker;
    let officeMarker;
    let radiusCircle;
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
                    if (typeof L !== 'undefined' && map) {
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

    // Fungsi untuk inisialisasi peta dengan Leaflet
    function initMap() {
        try {
            console.log('Initializing Leaflet map...');
            console.log('Office data:', officeData);
            
            // Default center (bisa diubah sesuai kebutuhan)
            const defaultCenter = [officeData.latitude || -6.2088, officeData.longitude || 106.8456];
            
            // Inisialisasi peta Leaflet
            map = L.map('map').setView(defaultCenter, 15);
            
            // Tambahkan tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            console.log('Leaflet map initialized successfully');

            // Tambahkan marker kantor jika koordinat tersedia
            if (officeData.latitude && officeData.longitude && officeData.latitude !== 0 && officeData.longitude !== 0) {
                officeLocation = [officeData.latitude, officeData.longitude];
                
                // Buat custom icon untuk kantor
                const officeIcon = L.divIcon({
                    className: 'custom-office-marker',
                    html: '<div style="background-color: #3b82f6; width: 32px; height: 32px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">🏢</div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });
                
                officeMarker = L.marker(officeLocation, { icon: officeIcon }).addTo(map);
                
                // Tambahkan lingkaran radius
                radiusCircle = L.circle(officeLocation, {
                    color: '#dc2626',
                    fillColor: '#dc2626',
                    fillOpacity: 0.1,
                    weight: 2,
                    radius: officeData.radius
                }).addTo(map);
                
                // Popup untuk kantor
                const officePopup = `
                    <div class="map-info">
                        <h6>🏢 ${officeData.name}</h6>
                        <p>📍 ${officeData.address}</p>
                        <p>📏 Radius: ${officeData.radius}m</p>
                        <p style="color: #dc2626; font-weight: 600;">🔴 Area absensi yang diizinkan</p>
                    </div>
                `;
                
                officeMarker.bindPopup(officePopup);
                console.log('Office marker and radius circle added');
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
            console.error('Error initializing Leaflet map:', error);
            $('#map').html(`
                <div style="height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px;">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="text-warning">Gagal memuat peta</h6>
                        <p class="text-muted small">Leaflet tidak tersedia</p>
                    </div>
                </div>
            `);
        }
    }

    // Fungsi untuk update peta dengan lokasi pengguna menggunakan Leaflet
    function updateMapWithUserLocation(coords) {
        userLocation = [coords.latitude, coords.longitude];
        
        // Cek apakah peta sudah diinisialisasi
        if (!map) {
            console.error('Map not initialized');
            return;
        }
        
        // Hapus marker pengguna yang lama jika ada
        if (userMarker) {
            map.removeLayer(userMarker);
        }
        
        // Hapus lingkaran radius yang lama jika ada (untuk refresh)
        if (radiusCircle) {
            map.removeLayer(radiusCircle);
        }

        // Buat custom icon untuk pengguna
        const userIcon = L.divIcon({
            className: 'custom-user-marker',
            html: '<div style="background-color: #dc2626; width: 32px; height: 32px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">📍</div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        // Tambahkan marker pengguna
        userMarker = L.marker(userLocation, { icon: userIcon }).addTo(map);
        
        // Popup untuk pengguna
        const userPopup = `
            <div class="map-info">
                <h6>📍 Lokasi Anda</h6>
                <p>Lat: ${coords.latitude.toFixed(6)}</p>
                <p>Lng: ${coords.longitude.toFixed(6)}</p>
            </div>
        `;
        
        userMarker.bindPopup(userPopup);

        // Tambahkan kembali lingkaran radius jika ada koordinat kantor
        if (officeLocation) {
            radiusCircle = L.circle(officeLocation, {
                color: '#dc2626',
                fillColor: '#dc2626',
                fillOpacity: 0.1,
                weight: 2,
                radius: officeData.radius
            }).addTo(map);
        }

        // Hitung jarak ke kantor
        let distance = 0;
        let isWithinRadius = false;
        
        if (officeLocation) {
            distance = calculateDistance(
                coords.latitude, coords.longitude,
                officeLocation[0], officeLocation[1]
            );
            isWithinRadius = distance <= officeData.radius;
            
            // Update warna lingkaran berdasarkan posisi pengguna
            if (radiusCircle) {
                if (isWithinRadius) {
                    radiusCircle.setStyle({
                        color: '#10b981',
                        fillColor: '#10b981',
                        fillOpacity: 0.2
                    });
                } else {
                    radiusCircle.setStyle({
                        color: '#dc2626',
                        fillColor: '#dc2626',
                        fillOpacity: 0.1
                    });
                }
            }
        }

        // Update detail lokasi
        updateLocationDetails(coords, distance, isWithinRadius);

        // Fit bounds untuk menampilkan kedua marker dan lingkaran
        if (officeLocation) {
            const bounds = L.latLngBounds([userLocation, officeLocation]);
            map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            map.setView(userLocation, 16);
        }
    }

    // Fungsi untuk update detail lokasi
    function updateLocationDetails(coords, distance, isWithinRadius) {
        $('#user-location').text(`${coords.latitude.toFixed(6)}, ${coords.longitude.toFixed(6)}`);
        
        if (officeData.latitude && officeData.longitude && officeData.latitude !== 0 && officeData.longitude !== 0) {
            $('#office-location').text(`${officeData.latitude.toFixed(6)}, ${officeData.longitude.toFixed(6)}`);
            $('#distance-to-office').text(`${Math.round(distance)}m`);
            $('#attendance-radius').text(`${officeData.radius}m`);
            
            const statusText = isWithinRadius ? '✅ Dalam Radius' : '❌ Di Luar Radius';
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

        // Inisialisasi peta Leaflet
        setTimeout(() => {
            if (typeof L !== 'undefined') {
                try {
                    initMap();
                } catch (error) {
                    console.error('Error initializing Leaflet map:', error);
                }
            }
        }, 1000);

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

        // Event listener untuk tombol Mulai Istirahat
        $('#breakStartBtn').click(function() {
            $.ajax({
                url: "{{ route('employee.absensi.break_start') }}",
                type: "POST",
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON.message || 'Terjadi kesalahan.';
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            });
        });

        // Event listener untuk tombol Selesai Istirahat
        $('#breakEndBtn').click(function() {
            $.ajax({
                url: "{{ route('employee.absensi.break_end') }}",
                type: "POST",
                success: function(response) {
                    let message = response.message;
                    let icon = 'success';
                    if (response.late_minutes > 0) {
                        icon = 'warning';
                    }
                    Swal.fire('Berhasil!', message, icon)
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON.message || 'Terjadi kesalahan.';
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            });
        });
    });
</script>
@endpush