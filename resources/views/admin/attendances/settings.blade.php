@extends('layouts.admin')

@section('page_title', 'Pengaturan Absensi')
@section('breadcrumb_item', 'Pengaturan')

@push('styles')
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-1"></i>
                    Pengaturan Jam Kerja
                </h3>
            </div>
            <div class="card-body">
                <form id="workTimeForm">
                    <div class="form-group">
                        <label for="work_start_time">Jam Mulai Kerja:</label>
                        <input type="time" class="form-control" id="work_start_time" name="work_start_time" 
                               value="{{ $settings['work_start_time'] }}" required>
                        <small class="form-text text-muted">Jam mulai kerja karyawan</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="work_end_time">Jam Selesai Kerja:</label>
                        <input type="time" class="form-control" id="work_end_time" name="work_end_time" 
                               value="{{ $settings['work_end_time'] }}" required>
                        <small class="form-text text-muted">Jam selesai kerja karyawan</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Jam Kerja</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Pengaturan Toleransi
                </h3>
            </div>
            <div class="card-body">
                <form id="toleranceForm">
                    <div class="form-group">
                        <label for="late_threshold">Toleransi Keterlambatan (menit):</label>
                        <input type="number" class="form-control" id="late_threshold" name="late_threshold" 
                               value="{{ $settings['late_threshold'] }}" min="0" max="60" required>
                        <small class="form-text text-muted">Waktu toleransi sebelum dianggap terlambat</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="early_leave_threshold">Toleransi Pulang Cepat (menit):</label>
                        <input type="number" class="form-control" id="early_leave_threshold" name="early_leave_threshold" 
                               value="{{ $settings['early_leave_threshold'] }}" min="0" max="120" required>
                        <small class="form-text text-muted">Waktu toleransi sebelum dianggap pulang cepat</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Toleransi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    Pengaturan Lokasi
                </h3>
            </div>
            <div class="card-body">
                <form id="locationForm">
                    <div class="form-group">
                        <label for="attendance_radius">Radius Absensi (meter):</label>
                        <input type="number" class="form-control" id="attendance_radius" name="attendance_radius" 
                               value="{{ $settings['attendance_radius'] }}" min="10" max="1000" required>
                        <small class="form-text text-muted">Jarak maksimal untuk absensi dari lokasi kantor</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="office_latitude">Latitude Kantor:</label>
                        <input type="text" class="form-control" id="office_latitude" name="office_latitude" 
                               placeholder="-6.1234567" pattern="-?\d+\.\d+">
                        <small class="form-text text-muted">Koordinat latitude kantor (opsional)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="office_longitude">Longitude Kantor:</label>
                        <input type="text" class="form-control" id="office_longitude" name="office_longitude" 
                               placeholder="106.1234567" pattern="-?\d+\.\d+">
                        <small class="form-text text-muted">Koordinat longitude kantor (opsional)</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Lokasi</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-1"></i>
                    Pengaturan Umum
                </h3>
            </div>
            <div class="card-body">
                <form id="generalForm">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="enable_location" name="enable_location" checked>
                            <label class="custom-control-label" for="enable_location">Aktifkan Verifikasi Lokasi</label>
                        </div>
                        <small class="form-text text-muted">Mengaktifkan verifikasi lokasi saat absensi</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="enable_photo" name="enable_photo">
                            <label class="custom-control-label" for="enable_photo">Aktifkan Foto Absensi</label>
                        </div>
                        <small class="form-text text-muted">Mengaktifkan pengambilan foto saat absensi</small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="enable_notification" name="enable_notification" checked>
                            <label class="custom-control-label" for="enable_notification">Aktifkan Notifikasi</label>
                        </div>
                        <small class="form-text text-muted">Mengaktifkan notifikasi keterlambatan</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Umum</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Pengaturan Hari Kerja
                </h3>
            </div>
            <div class="card-body">
                <form id="workDaysForm">
                    <div class="form-group">
                        <label>Hari Kerja:</label>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="monday" name="work_days[]" value="monday" checked>
                                    <label class="custom-control-label" for="monday">Senin</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="tuesday" name="work_days[]" value="tuesday" checked>
                                    <label class="custom-control-label" for="tuesday">Selasa</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="wednesday" name="work_days[]" value="wednesday" checked>
                                    <label class="custom-control-label" for="wednesday">Rabu</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="thursday" name="work_days[]" value="thursday" checked>
                                    <label class="custom-control-label" for="thursday">Kamis</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="friday" name="work_days[]" value="friday" checked>
                                    <label class="custom-control-label" for="friday">Jumat</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="saturday" name="work_days[]" value="saturday">
                                    <label class="custom-control-label" for="saturday">Sabtu</label>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Pilih hari-hari kerja</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan Hari Kerja</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

<script>
$(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Form handlers
    $('#workTimeForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('work_time', $(this).serialize());
    });

    $('#toleranceForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('tolerance', $(this).serialize());
    });

    $('#locationForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('location', $(this).serialize());
    });

    $('#generalForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('general', $(this).serialize());
    });

    $('#workDaysForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings('work_days', $(this).serialize());
    });

    function saveSettings(type, data) {
        $.ajax({
            url: '{{ route("admin.attendance.settings.save") }}',
            type: 'POST',
            data: data + '&type=' + type,
            success: function(response) {
                Swal.fire('Berhasil!', 'Pengaturan berhasil disimpan.', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan pengaturan.', 'error');
            }
        });
    }
});
</script>
@endpush 