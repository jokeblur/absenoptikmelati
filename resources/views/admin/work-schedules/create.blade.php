@extends('adminlte::page')

@section('title', 'Tambah Jadwal Kerja')

@section('content_header')
    <h1>Tambah Jadwal Kerja Karyawan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Jadwal Kerja</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.work-schedules.store') }}" method="POST" id="scheduleForm">
                    @csrf
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="user_id">Pilih Karyawan <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            {{ (request('user_id') == $employee->id || old('user_id') == $employee->id) ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-calendar-week"></i> Jadwal Kerja Mingguan</h5>
                                <p class="text-muted">Atur jadwal kerja untuk setiap hari dari Senin sampai Sabtu. Jam istirahat bersifat opsional.</p>
                            </div>
                        </div>

                        @foreach($workDays as $dayKey => $dayName)
                            <div class="card card-outline card-primary mb-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-calendar-day"></i> {{ $dayName }}
                                    </h5>
                                    <div class="card-tools">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input day-toggle" 
                                                   id="toggle_{{ $dayKey }}" data-day="{{ $dayKey }}" checked>
                                            <label class="custom-control-label" for="toggle_{{ $dayKey }}">Aktif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body day-schedule" id="schedule_{{ $dayKey }}">
                                    <div class="row">
                                        <!-- Jam Masuk & Pulang -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="schedules_{{ $dayKey }}_clock_in">Jam Masuk <span class="text-danger">*</span></label>
                                                        <input type="time" 
                                                               name="schedules[{{ $loop->index }}][clock_in]" 
                                                               id="schedules_{{ $dayKey }}_clock_in"
                                                               class="form-control @error('schedules.{{ $loop->index }}.clock_in') is-invalid @enderror"
                                                               value="{{ old('schedules.'.$loop->index.'.clock_in', '08:00') }}">
                                                        @error('schedules.{{ $loop->index }}.clock_in')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="schedules_{{ $dayKey }}_clock_out">Jam Pulang <span class="text-danger">*</span></label>
                                                        <input type="time" 
                                                               name="schedules[{{ $loop->index }}][clock_out]" 
                                                               id="schedules_{{ $dayKey }}_clock_out"
                                                               class="form-control @error('schedules.{{ $loop->index }}.clock_out') is-invalid @enderror"
                                                               value="{{ old('schedules.'.$loop->index.'.clock_out', '17:00') }}">
                                                        @error('schedules.{{ $loop->index }}.clock_out')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Jam Istirahat -->
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="schedules_{{ $dayKey }}_break_start">Mulai Istirahat</label>
                                                        <input type="time" 
                                                               name="schedules[{{ $loop->index }}][break_start_time]" 
                                                               id="schedules_{{ $dayKey }}_break_start"
                                                               class="form-control @error('schedules.{{ $loop->index }}.break_start_time') is-invalid @enderror"
                                                               value="{{ old('schedules.'.$loop->index.'.break_start_time', '12:00') }}">
                                                        @error('schedules.{{ $loop->index }}.break_start_time')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="schedules_{{ $dayKey }}_break_end">Selesai Istirahat</label>
                                                        <input type="time" 
                                                               name="schedules[{{ $loop->index }}][break_end_time]" 
                                                               id="schedules_{{ $dayKey }}_break_end"
                                                               class="form-control @error('schedules.{{ $loop->index }}.break_end_time') is-invalid @enderror"
                                                               value="{{ old('schedules.'.$loop->index.'.break_end_time', '13:00') }}">
                                                        @error('schedules.{{ $loop->index }}.break_end_time')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden fields -->
                                    <input type="hidden" name="schedules[{{ $loop->index }}][day]" value="{{ $dayKey }}">
                                    <input type="hidden" name="schedules[{{ $loop->index }}][is_holiday]" value="0" class="holiday-input">
                                </div>
                                <div class="card-footer text-center holiday-notice" id="holiday_{{ $dayKey }}" style="display: none;">
                                    <span class="text-muted"><i class="fas fa-times"></i> Hari ini dinonaktifkan</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fas fa-info-circle"></i> Panduan Penggunaan</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="mb-0">
                                            <li>Toggle <strong>Aktif/Nonaktif</strong> untuk mengatur hari kerja atau libur</li>
                                            <li><strong>Jam Masuk & Pulang</strong> wajib diisi untuk hari kerja</li>
                                            <li><strong>Jam Istirahat</strong> bersifat opsional dan akan mempengaruhi perhitungan absensi</li>
                                            <li>Pastikan jam pulang lebih lambat dari jam masuk</li>
                                            <li>Pastikan jam istirahat berada di antara jam masuk dan pulang</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Jadwal Kerja
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .day-schedule.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .card-outline.card-primary {
            border-top: 3px solid #007bff;
        }
        .custom-control-label::before {
            background-color: #dc3545;
        }
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #28a745;
        }
        .holiday-notice {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Handle day toggle
            $('.day-toggle').change(function() {
                const day = $(this).data('day');
                const isChecked = $(this).is(':checked');
                const scheduleDiv = $('#schedule_' + day);
                const holidayDiv = $('#holiday_' + day);
                const holidayInput = scheduleDiv.find('.holiday-input');
                
                if (isChecked) {
                    scheduleDiv.show().removeClass('disabled');
                    holidayDiv.hide();
                    holidayInput.val('0');
                    // Enable required validation
                    scheduleDiv.find('input[type="time"]').each(function() {
                        if ($(this).attr('name').includes('clock_in') || $(this).attr('name').includes('clock_out')) {
                            $(this).attr('required', true);
                        }
                    });
                } else {
                    scheduleDiv.hide().addClass('disabled');
                    holidayDiv.show();
                    holidayInput.val('1');
                    // Disable required validation
                    scheduleDiv.find('input[type="time"]').removeAttr('required');
                }
            });

            // Form validation
            $('#scheduleForm').submit(function(e) {
                let hasActiveDay = false;
                $('.day-toggle').each(function() {
                    if ($(this).is(':checked')) {
                        hasActiveDay = true;
                        return false;
                    }
                });

                if (!hasActiveDay) {
                    e.preventDefault();
                    alert('Minimal satu hari kerja harus diaktifkan!');
                    return false;
                }

                // Validate time ranges for active days
                let isValid = true;
                $('.day-toggle:checked').each(function() {
                    const day = $(this).data('day');
                    const clockIn = $('#schedules_' + day + '_clock_in').val();
                    const clockOut = $('#schedules_' + day + '_clock_out').val();
                    const breakStart = $('#schedules_' + day + '_break_start').val();
                    const breakEnd = $('#schedules_' + day + '_break_end').val();

                    if (clockIn && clockOut && clockIn >= clockOut) {
                        alert('Jam pulang harus lebih lambat dari jam masuk pada hari ' + day);
                        isValid = false;
                        return false;
                    }

                    if (breakStart && breakEnd) {
                        if (breakStart >= breakEnd) {
                            alert('Jam selesai istirahat harus lebih lambat dari jam mulai istirahat pada hari ' + day);
                            isValid = false;
                            return false;
                        }
                        if (clockIn && breakStart <= clockIn) {
                            alert('Jam mulai istirahat harus setelah jam masuk pada hari ' + day);
                            isValid = false;
                            return false;
                        }
                        if (clockOut && breakEnd >= clockOut) {
                            alert('Jam selesai istirahat harus sebelum jam pulang pada hari ' + day);
                            isValid = false;
                            return false;
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop