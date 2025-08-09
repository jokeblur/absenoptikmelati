@extends('layouts.admin')

@section('page_title', 'Laporan Absensi')
@section('breadcrumb_item', 'Laporan Absensi')

@push('styles')
    {{-- Chart.js --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/chart.js/Chart.min.js') }}"></script>
    {{-- Date Range Picker --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.css') }}">
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Laporan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportModal">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.report') }}" class="row">
                    <div class="col-md-3">
                        <label for="start_date">Dari Tanggal:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date', $startDate->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">Sampai Tanggal:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date', $endDate->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="user_id">Karyawan:</label>
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id">Cabang:</label>
                        <select class="form-control" id="branch_id" name="branch_id">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>Filter Laporan
                        </button>
                        <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">
                            <i class="fas fa-refresh mr-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalEmployees }}</h3>
                <p>Total Karyawan</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalAttendance }}</h3>
                <p>Total Absensi</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $lateCount }}</h3>
                <p>Keterlambatan</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalAbsentCount }}</h3>
                <p>Tidak Hadir (Hari Kerja)</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Grafik Absensi Harian
                </h3>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Status Absensi
                </h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Ringkasan Harian
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Total Absensi</th>
                                <th>Terlambat</th>
                                <th>Tidak Hadir</th>
                                <th>Persentase Kehadiran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyStats as $stat)
                            <tr class="{{ !$stat->is_working_day ? 'table-secondary' : '' }}">
                                <td>
                                    {{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}
                                    @if(!$stat->is_working_day)
                                        <span class="badge badge-info ml-1">Libur</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $dayNames = [
                                            'Monday' => 'Senin',
                                            'Tuesday' => 'Selasa', 
                                            'Wednesday' => 'Rabu',
                                            'Thursday' => 'Kamis',
                                            'Friday' => 'Jumat',
                                            'Saturday' => 'Sabtu',
                                            'Sunday' => 'Minggu'
                                        ];
                                        $dayName = $dayNames[$stat->day_name] ?? $stat->day_name;
                                    @endphp
                                    {{ $dayName }}
                                </td>
                                <td>{{ $stat->total }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ $stat->late_count }}</span>
                                </td>
                                <td>
                                    @if($stat->is_working_day)
                                        <span class="badge badge-danger">{{ $stat->absent_count }}</span>
                                    @else
                                        <span class="badge badge-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat->is_working_day)
                                        @php
                                            $percentage = $totalEmployees > 0 ? (($stat->total) / $totalEmployees) * 100 : 0;
                                        @endphp
                                        <span class="badge badge-{{ $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($percentage, 1) }}%
                                        </span>
                                    @else
                                        <span class="badge badge-info">Hari Libur</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($stat->is_working_day && $stat->late_count > 0)
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="showLateUsers('{{ $stat->date }}', {{ $stat->late_count }})">
                                                <i class="fas fa-clock"></i> Lihat Terlambat
                                            </button>
                                        @endif
                                        @if($stat->is_working_day && $stat->absent_count > 0)
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="showAbsentUsers('{{ $stat->date }}', {{ $stat->absent_count }})">
                                                <i class="fas fa-eye"></i> Lihat Tidak Hadir
                                            </button>
                                        @endif
                                        @if($stat->is_working_day && $stat->late_count == 0 && $stat->absent_count == 0)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan daftar karyawan yang tidak hadir per tanggal -->
<div class="modal fade" id="absentUsersModal" tabindex="-1" role="dialog" aria-labelledby="absentUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="absentUsersModalLabel">
                    <i class="fas fa-user-times mr-1"></i>
                    Daftar Karyawan Tidak Hadir
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="absentUsersContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menampilkan daftar karyawan yang terlambat per tanggal -->
<div class="modal fade" id="lateUsersModal" tabindex="-1" role="dialog" aria-labelledby="lateUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lateUsersModalLabel">
                    <i class="fas fa-clock mr-1"></i>
                    Daftar Karyawan Terlambat
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="lateUsersContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-download mr-2"></i>Export Data Laporan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="exportForm" action="{{ route('admin.attendance.export_data') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_start_date">Dari Tanggal:</label>
                                <input type="date" class="form-control" id="export_start_date" name="start_date" 
                                       value="{{ request('start_date', $startDate->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_end_date">Sampai Tanggal:</label>
                                <input type="date" class="form-control" id="export_end_date" name="end_date" 
                                       value="{{ request('end_date', $endDate->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_user_id">Karyawan:</label>
                                <select class="form-control" id="export_user_id" name="user_id">
                                    <option value="">Semua Karyawan</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('user_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="export_branch_id">Cabang:</label>
                                <select class="form-control" id="export_branch_id" name="branch_id">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="export_format">Format Export:</label>
                        <select class="form-control" id="export_format" name="format" required>
                            <option value="">Pilih Format</option>
                            <option value="excel" selected>Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="include_absent" name="include_absent" value="1">
                            <label class="custom-control-label" for="include_absent">
                                Sertakan data karyawan yang tidak hadir
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="include_leave" name="include_leave" value="1">
                            <label class="custom-control-label" for="include_leave">
                                Sertakan data karyawan yang cuti
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="include_permission" name="include_permission" value="1">
                            <label class="custom-control-label" for="include_permission">
                                Sertakan data karyawan yang izin
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="exportBtn">
                        <i class="fas fa-download mr-1"></i>Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('AdminLTE-3.0.1/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

<script>
$(function () {
    // Validate filter form
    $('form[method="GET"]').on('submit', function(e) {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (!startDate || !endDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih rentang tanggal yang valid.'
            });
            return false;
        }
        
        if (startDate > endDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'
            });
            return false;
        }
    });

    // Data untuk grafik
    // eslint-disable-next-line
    const dailyData = @json($dailyStats);
    const labels = dailyData.map(item => moment(item.date).format('DD/MM/YYYY'));
    const totalData = dailyData.map(item => item.total);
    const lateData = dailyData.map(item => item.late_count);
    const absentData = dailyData.map(item => item.is_working_day ? item.absent_count : 0);
    
    // Data untuk status chart
    // eslint-disable-next-line
    const onTimeCount = @json($totalAttendance - $lateCount);
    // eslint-disable-next-line
    const lateCount = @json($lateCount);
    // eslint-disable-next-line
    const absentCount = @json($totalAbsentCount);

    // Grafik Absensi Harian
    const attendanceChart = new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Absensi',
                data: totalData,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Terlambat',
                data: lateData,
                borderColor: 'rgb(255, 205, 86)',
                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                tension: 0.1
            }, {
                label: 'Tidak Hadir',
                data: absentData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
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

    // Grafik Status Absensi
    const statusChart = new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tepat Waktu', 'Terlambat', 'Tidak Hadir'],
            datasets: [{
                data: [onTimeCount, lateCount, absentCount],
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 99, 132)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Handle export form submission
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        const format = $('#export_format').val();
        if (!format) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih format export terlebih dahulu.'
            });
            return;
        }

        const startDate = $('#export_start_date').val();
        const endDate = $('#export_end_date').val();
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih rentang tanggal yang valid.'
            });
            return;
        }

        if (startDate > endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.'
            });
            return;
        }

        // Show loading
        $('#exportBtn').html('<i class="fas fa-spinner fa-spin mr-1"></i>Exporting...');
        $('#exportBtn').prop('disabled', true);

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response, status, xhr) {
                // Create download link
                const blob = new Blob([response]);
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                
                // Set filename based on format
                const filename = 'attendance_report_' + moment().format('YYYY-MM-DD_HH-mm-ss');
                switch(format) {
                    case 'excel':
                        a.download = filename + '.xls';
                        break;
                    case 'csv':
                        a.download = filename + '.csv';
                        break;
                    case 'pdf':
                        a.download = filename + '.pdf';
                        break;
                }
                
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                // Reset button
                $('#exportBtn').html('<i class="fas fa-download mr-1"></i>Export Data');
                $('#exportBtn').prop('disabled', false);
                
                // Close modal
                $('#exportModal').modal('hide');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil di-export dan akan otomatis terdownload.'
                });
            },
            error: function(xhr, status, error) {
                // Reset button
                $('#exportBtn').html('<i class="fas fa-download mr-1"></i>Export Data');
                $('#exportBtn').prop('disabled', false);

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat export data. Silakan coba lagi.'
                });
            }
        });
    });

    // Sync filter values with export form
    $('#start_date, #end_date, #user_id, #branch_id').on('change', function() {
        const fieldName = $(this).attr('name');
        const value = $(this).val();
        $('#export_' + fieldName).val(value);
    });

    // Function to show absent users modal
    window.showAbsentUsers = function(date, count) {
        // Show loading
        $('#absentUsersContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>');
        $('#absentUsersModal').modal('show');

        // Get current filter values
        const userId = $('#user_id').val();
        const branchId = $('#branch_id').val();

        // Fetch absent users data
        $.ajax({
            url: '{{ route("admin.attendance.absent_users") }}',
            method: 'GET',
            data: {
                date: date,
                user_id: userId,
                branch_id: branchId
            },
            success: function(response) {
                if (response.success) {
                    let html = '<h6 class="mb-3">Tanggal: ' + response.date + '</h6>';
                    
                    // Check if it's a holiday
                    if (response.message && response.message.includes('hari libur')) {
                        html += '<div class="text-center"><i class="fas fa-calendar-times text-info fa-2x"></i><p class="mt-2 text-info">Hari ini adalah hari libur</p></div>';
                    } else if (response.data.length > 0) {
                        html += '<div class="table-responsive"><table class="table table-bordered table-striped">';
                        html += '<thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Cabang</th></tr></thead>';
                        html += '<tbody>';
                        
                        response.data.forEach(function(user, index) {
                            html += '<tr>';
                            html += '<td>' + (index + 1) + '</td>';
                            html += '<td>' + user.name + '</td>';
                            html += '<td>' + user.email + '</td>';
                            html += '<td>' + (user.branch ? user.branch.name : 'Tidak ada cabang') + '</td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                    } else {
                        html += '<div class="text-center"><i class="fas fa-check-circle text-success fa-2x"></i><p class="mt-2">Tidak ada karyawan yang tidak hadir pada tanggal ini.</p></div>';
                    }
                    
                    $('#absentUsersContent').html(html);
                } else {
                    $('#absentUsersContent').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Terjadi kesalahan saat memuat data.</p></div>');
                }
            },
            error: function() {
                $('#absentUsersContent').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Terjadi kesalahan saat memuat data.</p></div>');
            }
        });
    };

    // Function to show late users modal
    window.showLateUsers = function(date, count) {
        // Show loading
        $('#lateUsersContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>');
        $('#lateUsersModal').modal('show');

        // Get current filter values
        const userId = $('#user_id').val();
        const branchId = $('#branch_id').val();

        // Fetch late users data
        $.ajax({
            url: '{{ route("admin.attendance.late_users") }}',
            method: 'GET',
            data: {
                date: date,
                user_id: userId,
                branch_id: branchId
            },
            success: function(response) {
                if (response.success) {
                    let html = '<h6 class="mb-3">Tanggal: ' + response.date + '</h6>';
                    
                    if (response.data.length > 0) {
                        html += '<div class="table-responsive"><table class="table table-bordered table-striped">';
                        html += '<thead><tr><th>No</th><th>Nama</th><th>Email</th><th>Cabang</th><th>Waktu Check-in</th><th>Keterlambatan</th></tr></thead>';
                        html += '<tbody>';
                        
                        response.data.forEach(function(attendance, index) {
                            html += '<tr>';
                            html += '<td>' + (index + 1) + '</td>';
                            html += '<td>' + attendance.user.name + '</td>';
                            html += '<td>' + attendance.user.email + '</td>';
                            html += '<td>' + (attendance.user.branch ? attendance.user.branch.name : 'Tidak ada cabang') + '</td>';
                            html += '<td>' + attendance.check_in + '</td>';
                            html += '<td><span class="badge badge-warning">' + attendance.late_minutes + ' menit</span></td>';
                            html += '</tr>';
                        });
                        
                        html += '</tbody></table></div>';
                    } else {
                        html += '<div class="text-center"><i class="fas fa-check-circle text-success fa-2x"></i><p class="mt-2">Tidak ada karyawan yang terlambat pada tanggal ini.</p></div>';
                    }
                    
                    $('#lateUsersContent').html(html);
                } else {
                    $('#lateUsersContent').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Terjadi kesalahan saat memuat data.</p></div>');
                }
            },
            error: function() {
                $('#lateUsersContent').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p class="mt-2">Terjadi kesalahan saat memuat data.</p></div>');
            }
        });
    };
});
</script>
@endpush 