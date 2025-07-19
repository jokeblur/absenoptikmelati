@extends('adminlte::page')

@section('title', 'Detail Jadwal Kerja')

@section('content_header')
    <h1>Detail Jadwal Kerja - {{ $employee->name }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Jadwal Kerja</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.work-schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if($employee->workSchedules->count() > 0)
                            <a href="{{ route('admin.work-schedules.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit Jadwal
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Informasi Karyawan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nama Karyawan</span>
                                    <span class="info-box-number">{{ $employee->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-envelope"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Email</span>
                                    <span class="info-box-number" style="font-size: 16px;">{{ $employee->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($employee->workSchedules->count() > 0)
                        <!-- Jadwal Kerja -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-calendar-week"></i> Jadwal Kerja Mingguan</h5>
                            </div>
                        </div>

                        <div class="row">
                            @foreach($workDays as $dayKey => $dayName)
                                @php
                                    $schedule = $schedules[$dayKey] ?? null;
                                @endphp
                                
                                <div class="col-lg-6 mb-3">
                                    <div class="card h-100 {{ $schedule && !$schedule->is_holiday ? 'card-outline card-success' : 'card-outline card-secondary' }}">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-calendar-day"></i> {{ $dayName }}
                                                @if($schedule && !$schedule->is_holiday)
                                                    <span class="badge badge-success float-right">
                                                        <i class="fas fa-check"></i> Hari Kerja
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary float-right">
                                                        <i class="fas fa-times"></i> Libur
                                                    </span>
                                                @endif
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($schedule && !$schedule->is_holiday)
                                                <div class="row">
                                                    <div class="col-12">
                                                        <table class="table table-sm table-borderless">
                                                            <tr>
                                                                <td width="40%"><strong><i class="fas fa-sign-in-alt text-success"></i> Jam Masuk:</strong></td>
                                                                <td>{{ date('H:i', strtotime($schedule->clock_in)) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong><i class="fas fa-sign-out-alt text-danger"></i> Jam Pulang:</strong></td>
                                                                <td>{{ date('H:i', strtotime($schedule->clock_out)) }}</td>
                                                            </tr>
                                                            @if($schedule->break_start_time && $schedule->break_end_time)
                                                                <tr>
                                                                    <td><strong><i class="fas fa-coffee text-warning"></i> Istirahat:</strong></td>
                                                                    <td>{{ date('H:i', strtotime($schedule->break_start_time)) }} - {{ date('H:i', strtotime($schedule->break_end_time)) }}</td>
                                                                </tr>
                                                            @else
                                                                <tr>
                                                                    <td><strong><i class="fas fa-coffee text-muted"></i> Istirahat:</strong></td>
                                                                    <td><span class="text-muted">Tidak diatur</span></td>
                                                                </tr>
                                                            @endif
                                                            <tr>
                                                                <td><strong><i class="fas fa-clock text-info"></i> Total Jam:</strong></td>
                                                                <td>
                                                                    @php
                                                                        $startTime = strtotime($schedule->clock_in);
                                                                        $endTime = strtotime($schedule->clock_out);
                                                                        $totalMinutes = ($endTime - $startTime) / 60;
                                                                        
                                                                        // Kurangi waktu istirahat jika ada
                                                                        if($schedule->break_start_time && $schedule->break_end_time) {
                                                                            $breakStart = strtotime($schedule->break_start_time);
                                                                            $breakEnd = strtotime($schedule->break_end_time);
                                                                            $breakMinutes = ($breakEnd - $breakStart) / 60;
                                                                            $totalMinutes -= $breakMinutes;
                                                                        }
                                                                        
                                                                        $hours = floor($totalMinutes / 60);
                                                                        $minutes = $totalMinutes % 60;
                                                                    @endphp
                                                                    {{ $hours }} jam {{ $minutes }} menit
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-3">
                                                    <i class="fas fa-bed fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">Hari libur / tidak ada jadwal kerja</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Summary -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="fas fa-chart-bar"></i> Ringkasan Jadwal Kerja</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Hari Kerja</span>
                                                        <span class="info-box-number">
                                                            {{ $employee->workSchedules->where('is_holiday', false)->count() }} hari
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box bg-secondary">
                                                    <span class="info-box-icon"><i class="fas fa-calendar-times"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Hari Libur</span>
                                                        <span class="info-box-number">
                                                            {{ 6 - $employee->workSchedules->where('is_holiday', false)->count() }} hari
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-coffee"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Dengan Istirahat</span>
                                                        <span class="info-box-number">
                                                            {{ $employee->workSchedules->whereNotNull('break_start_time')->count() }} hari
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box bg-info">
                                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Jam/Minggu</span>
                                                        <span class="info-box-number">
                                                            @php
                                                                $totalWeeklyHours = 0;
                                                                foreach($employee->workSchedules->where('is_holiday', false) as $schedule) {
                                                                    $startTime = strtotime($schedule->clock_in);
                                                                    $endTime = strtotime($schedule->clock_out);
                                                                    $dayHours = ($endTime - $startTime) / 3600;
                                                                    
                                                                    // Kurangi waktu istirahat jika ada
                                                                    if($schedule->break_start_time && $schedule->break_end_time) {
                                                                        $breakStart = strtotime($schedule->break_start_time);
                                                                        $breakEnd = strtotime($schedule->break_end_time);
                                                                        $breakHours = ($breakEnd - $breakStart) / 3600;
                                                                        $dayHours -= $breakHours;
                                                                    }
                                                                    
                                                                    $totalWeeklyHours += $dayHours;
                                                                }
                                                            @endphp
                                                            {{ number_format($totalWeeklyHours, 1) }} jam
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tidak ada jadwal -->
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">Belum Ada Jadwal Kerja</h4>
                            <p class="text-muted mb-4">Karyawan ini belum memiliki jadwal kerja yang ditetapkan.</p>
                            <a href="{{ route('admin.work-schedules.create') }}?user_id={{ $employee->id }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Jadwal Kerja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-outline.card-success {
            border-top: 3px solid #28a745;
        }
        .card-outline.card-secondary {
            border-top: 3px solid #6c757d;
        }
        .info-box-number {
            font-size: 18px !important;
        }
        .table-borderless td {
            border: none !important;
            padding: 0.25rem 0.5rem;
        }
        .card.h-100 {
            height: 100% !important;
        }
    </style>
@stop