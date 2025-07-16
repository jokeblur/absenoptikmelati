@extends('layouts.admin')

@section('page_title', 'Dashboard Admin')
@section('breadcrumb_item', 'Dashboard')

@push('styles')
    {{-- Chart.js --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/chart.js/Chart.min.js') }}"></script>
@endpush

@section('content')
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $totalEmployees }}</h3>
          <p>Karyawan Terdaftar</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="{{ route('admin.employees.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $totalAdmins }}</h3>
          <p>Admin Terdaftar</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-shield"></i>
        </div>
        <a href="{{ route('admin.admins.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $totalAttendance }}</h3>
          <p>Absensi Hari Ini</p>
        </div>
        <div class="icon">
          <i class="fas fa-clock"></i>
        </div>
        <a href="{{ route('admin.attendances.index') }}?start_date={{ now()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $pendingLeaves }}</h3>
          <p>Pengajuan Cuti Pending</p>
        </div>
        <div class="icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <a href="{{ route('admin.leaves.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
  </div>
  <div class="row">
    <section class="col-lg-12 connectedSortable">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-chart-pie mr-1"></i>
            Grafik Absensi Bulanan
          </h3>
        </div><div class="card-body">
          <div class="tab-content p-0">
            <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
              <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
            </div>
          </div>
        </div></div>
      </section>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-clock mr-1"></i>
            Ringkasan Absensi Hari Ini
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Tepat Waktu</span>
                  <span class="info-box-number">{{ $totalAttendance - $todayLateCount }}</span>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Terlambat</span>
                  <span class="info-box-number">{{ $todayLateCount }}</span>
                  @if($todayLateCount > 0)
                    <small>Total: {{ $totalLateMinutes }} menit</small>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-tasks mr-1"></i>
            Menu Cepat
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <a href="{{ route('admin.admins.index') }}" class="btn btn-primary btn-block mb-2">
                <i class="fas fa-user-shield mr-1"></i> Manajemen Admin
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.employees.index') }}" class="btn btn-info btn-block mb-2">
                <i class="fas fa-users mr-1"></i> Manajemen Karyawan
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.attendance.report') }}" class="btn btn-success btn-block mb-2">
                <i class="fas fa-chart-bar mr-1"></i> Laporan Absensi
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.profile') }}" class="btn btn-warning btn-block mb-2">
                <i class="fas fa-user mr-1"></i> Profil Admin
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endsection

@push('scripts')
<script>
$(function () {
    // Grafik Absensi Bulanan
    var ctx = document.getElementById('revenue-chart-canvas').getContext('2d');
    /* eslint-disable */
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Absensi',
                data: @json($chartData),
                borderColor: 'rgb(158, 182, 182)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    /* eslint-enable */
});
</script>
@endpush