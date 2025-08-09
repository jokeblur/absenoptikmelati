@extends('layouts.admin')

@section('page_title', 'Laporan Keterlambatan Bulanan')
@section('breadcrumb_item', 'Laporan Keterlambatan Bulanan')

@push('styles')
    {{-- Chart.js --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/chart.js/Chart.min.js') }}"></script>
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    
    <style>
        /* Ensure table columns are visible */
        #lateTable {
            width: 100% !important;
        }
        
        #lateTable th,
        #lateTable td {
            white-space: nowrap;
            min-width: 100px;
        }
        
        /* Center align column No */
        #lateTable th:first-child,
        #lateTable td:first-child {
            text-align: center;
            width: 60px;
        }
        
        /* Make sure all columns are visible */
        .dataTables_wrapper .dataTables_scroll {
            overflow-x: auto;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter Laporan Keterlambatan Bulanan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-download"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.attendance.monthly_late_report') }}" class="row">
                    <div class="col-md-4">
                        <label for="month">Bulan:</label>
                        <select class="form-control" id="month" name="month" required>
                            @foreach($months as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $month == $monthNum ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year">Tahun:</label>
                        <select class="form-control" id="year" name="year" required>
                            @foreach($years as $yearOption)
                                <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                                    {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>Filter Laporan
                            </button>
                            <a href="{{ route('admin.attendance.monthly_late_report') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh mr-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Keseluruhan -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalLateMinutes }}</h3>
                <p>Total Menit Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalLateHours }}</h3>
                <p>Total Jam Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalLateDays }}</h3>
                <p>Total Hari Terlambat</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-times"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $totalAbsentDays ?? 0 }}</h3>
                <p>Total Hari Tidak Hadir</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-times"></i>
            </div>
        </div>
    </div>
</div>

<!-- Grafik -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Grafik Keterlambatan per Karyawan
                </h3>
            </div>
            <div class="card-body">
                <canvas id="lateChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi Keterlambatan
                </h3>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Detail -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i>
                    Detail Keterlambatan per Karyawan
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="lateTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Email</th>
                                <th>Cabang</th>
                                <th>Total Menit</th>
                                <th>Total Jam</th>
                                <th>Hari Terlambat</th>
                                <th>Hari Tidak Hadir</th>
                                <th>Rata-rata/Hari</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employeeLateStats as $index => $stat)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $stat['employee']->name }}</td>
                                <td>{{ $stat['employee']->email }}</td>
                                <td>{{ $stat['employee']->branch ? $stat['employee']->branch->name : 'N/A' }}</td>
                                <td>
                                    @if($stat['total_late_minutes'] > 0)
                                        <span class="badge badge-warning">{{ $stat['total_late_minutes'] }} menit</span>
                                    @else
                                        <span class="badge badge-success">0 menit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['total_late_hours'] > 0)
                                        <span class="badge badge-danger">{{ $stat['total_late_hours'] }} jam</span>
                                    @else
                                        <span class="badge badge-success">0 jam</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['late_days'] > 0)
                                        <span class="badge badge-info">{{ $stat['late_days'] }} hari</span>
                                    @else
                                        <span class="badge badge-success">0 hari</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($stat['absent_details']) && count($stat['absent_details']) > 0)
                                        <span class="badge badge-danger">{{ count($stat['absent_details']) }} hari</span>
                                    @else
                                        <span class="badge badge-success">0 hari</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['average_late_minutes'] > 0)
                                        <span class="badge badge-warning">{{ $stat['average_late_minutes'] }} menit</span>
                                    @else
                                        <span class="badge badge-success">0 menit</span>
                                    @endif
                                </td>
                                <td>
                                    @if($stat['late_days'] > 0 || (isset($stat['absent_details']) && count($stat['absent_details']) > 0))
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="showLateDetails('{{ $stat['employee']->name }}', {{ json_encode($stat['late_details']) }}, {{ json_encode($stat['absent_details'] ?? []) }})">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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

<!-- Modal untuk detail keterlambatan -->
<div class="modal fade" id="lateDetailsModal" tabindex="-1" role="dialog" aria-labelledby="lateDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lateDetailsModalLabel">
                    <i class="fas fa-clock mr-1"></i>
                    Detail Keterlambatan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="lateDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    {{-- DataTables JS --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#lateTable').DataTable({
                responsive: false, // Disable responsive to prevent expandable rows
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                },
                order: [[4, 'desc']], // Sort by total minutes descending
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                columnDefs: [
                    {
                        targets: 0, // Kolom No
                        orderable: false, // Tidak bisa diurutkan
                        searchable: false, // Tidak bisa dicari
                        className: 'text-center' // Center align
                    }
                ],
                scrollX: true, // Enable horizontal scrolling
                autoWidth: false // Disable auto width calculation
            });

            // Chart data
            const employeeNames = @json(collect($employeeLateStats)->pluck('employee.name'));
            const lateMinutes = @json(collect($employeeLateStats)->pluck('total_late_minutes'));
            const lateHours = @json(collect($employeeLateStats)->pluck('total_late_hours'));

            // Bar Chart
            const ctx1 = document.getElementById('lateChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: employeeNames,
                    datasets: [{
                        label: 'Total Jam Terlambat',
                        data: lateHours,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jam'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Karyawan'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

            // Pie Chart for distribution
            const ctx2 = document.getElementById('distributionChart').getContext('2d');
            const totalLate = {{ $totalLateMinutes }};
            const onTimeCount = {{ count($employeeLateStats) }} - {{ collect($employeeLateStats)->where('total_late_minutes', '>', 0)->count() }};
            const lateCount = {{ collect($employeeLateStats)->where('total_late_minutes', '>', 0)->count() }};

            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{
                        data: [onTimeCount, lateCount],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

        // Function to show late details
        function showLateDetails(employeeName, lateDetails, absentDetails) {
            let html = '<h6 class="mb-3">Detail Keterlambatan: ' + employeeName + '</h6>';
            
            // Tab untuk keterlambatan dan tidak hadir
            html += '<ul class="nav nav-tabs" id="detailTabs" role="tablist">';
            html += '<li class="nav-item" role="presentation">';
            html += '<a class="nav-link active" id="late-tab" data-toggle="tab" href="#late-content" role="tab" aria-controls="late-content" aria-selected="true">Keterlambatan</a>';
            html += '</li>';
            html += '<li class="nav-item" role="presentation">';
            html += '<a class="nav-link" id="absent-tab" data-toggle="tab" href="#absent-content" role="tab" aria-controls="absent-content" aria-selected="false">Tidak Hadir</a>';
            html += '</li>';
            html += '</ul>';
            
            html += '<div class="tab-content mt-3" id="detailTabContent">';
            
            // Tab Keterlambatan
            html += '<div class="tab-pane fade show active" id="late-content" role="tabpanel" aria-labelledby="late-tab">';
            if (lateDetails.length > 0) {
                html += '<div class="table-responsive"><table class="table table-bordered table-striped">';
                html += '<thead><tr><th>No</th><th>Tanggal</th><th>Waktu Check-in</th><th>Keterlambatan</th></tr></thead>';
                html += '<tbody>';
                
                lateDetails.forEach(function(detail, index) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td>' + detail.date + '</td>';
                    html += '<td>' + detail.check_in_time + '</td>';
                    html += '<td><span class="badge badge-warning">' + detail.late_minutes + ' menit</span></td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
            } else {
                html += '<div class="text-center"><i class="fas fa-check-circle text-success fa-2x"></i><p class="mt-2">Tidak ada data keterlambatan.</p></div>';
            }
            html += '</div>';
            
            // Tab Tidak Hadir
            html += '<div class="tab-pane fade" id="absent-content" role="tabpanel" aria-labelledby="absent-tab">';
            if (absentDetails && absentDetails.length > 0) {
                html += '<div class="table-responsive"><table class="table table-bordered table-striped">';
                html += '<thead><tr><th>No</th><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr></thead>';
                html += '<tbody>';
                
                absentDetails.forEach(function(detail, index) {
                    html += '<tr>';
                    html += '<td>' + (index + 1) + '</td>';
                    html += '<td>' + detail.date + '</td>';
                    html += '<td><span class="badge badge-danger">Tidak Hadir</span></td>';
                    html += '<td>' + (detail.reason || '-') + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
            } else {
                html += '<div class="text-center"><i class="fas fa-check-circle text-success fa-2x"></i><p class="mt-2">Tidak ada data tidak hadir.</p></div>';
            }
            html += '</div>';
            
            html += '</div>'; // End tab-content
            
            $('#lateDetailsContent').html(html);
            $('#lateDetailsModal').modal('show');
        }

        // Function to export to Excel
        function exportToExcel() {
            const month = $('#month').val();
            const year = $('#year').val();
            
            // Show loading
            Swal.fire({
                title: 'Menyiapkan file Excel...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create form and submit to generate XLSX
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.attendance.export_data") }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add parameters
            const params = {
                'start_date': year + '-' + month.toString().padStart(2, '0') + '-01',
                'end_date': year + '-' + month.toString().padStart(2, '0') + '-' + new Date(year, month, 0).getDate(),
                'format': 'excel',
                'type': 'late'
            };
            
            Object.keys(params).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = params[key];
                form.appendChild(input);
            });
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            // Close loading after delay
            setTimeout(() => {
                Swal.close();
            }, 2000);
        }
    </script>
@endpush 