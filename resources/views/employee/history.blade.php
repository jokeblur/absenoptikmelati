@extends('layouts.employee')

@section('title', 'Riwayat Absensi')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<style>
    /* Apply Poppins font to history page */
    body {
        font-family: 'Poppins', sans-serif;
    }
    
    .card, .table, .badge, h1, h2, h3, h4, h5, h6, p, span, th, td {
        font-family: 'Poppins', sans-serif;
    }
    
    .card-header h4 {
        font-weight: 600;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .pagination {
        font-family: 'Poppins', sans-serif;
    }
    
    /* Custom tab style for better visibility */
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        font-weight: 500;
        background: #ecf0f1;
        border: 1px solid #bdc3c7;
        border-bottom: none;
        margin-bottom: -1px;
    }
    .nav-tabs .nav-link.active {
        color: #fff !important;
        background: #2c3e50 !important;
        border-color: #2c3e50 #2c3e50 #fff;
    }
    .tab-content {
        background: #fff;
        border: 1px solid #bdc3c7;
        border-top: none;
        padding: 1rem;
    }
    
    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 12px;
        font-family: 'Poppins', sans-serif;
    }
    
    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.15);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px;
        margin: 0 2px;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        border-color: #dc2626 !important;
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
        border-color: #b91c1c !important;
        color: white !important;
    }
    
    /* Table styling improvements */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="historyTab" role="tablist">
        <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="absensi-tab" data-bs-toggle="tab" data-bs-target="#absensi" type="button" role="tab" aria-controls="absensi" aria-selected="true">
                        <i class="fas fa-clock me-1"></i>Riwayat Absensi
                    </button>
        </li>
        <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cuti-tab" data-bs-toggle="tab" data-bs-target="#cuti" type="button" role="tab" aria-controls="cuti" aria-selected="false">
                        <i class="fas fa-calendar-alt me-1"></i>Riwayat Cuti
                    </button>
        </li>
        <li class="nav-item" role="presentation">
                    <button class="nav-link" id="izin-tab" data-bs-toggle="tab" data-bs-target="#izin" type="button" role="tab" aria-controls="izin" aria-selected="false">
                        <i class="fas fa-file-alt me-1"></i>Riwayat Izin
                    </button>
        </li>
    </ul>

            <!-- Tab Content -->
    <div class="tab-content" id="historyTabContent">
                <!-- Tab Absensi -->
        <div class="tab-pane fade show active" id="absensi" role="tabpanel" aria-labelledby="absensi-tab">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Riwayat Absensi Saya
                            </h4>
                        </div>
                    <div class="card-body">
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                        <th>Status</th>
                                        <th>Keterlambatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($attendance->check_in)
                                                    <span class="badge bg-success">
                                                        {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->check_out)
                                                    <span class="badge bg-danger">
                                                        {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->status_in == 'terlambat')
                                                    <span class="badge bg-warning">Terlambat</span>
                                                @elseif($attendance->status_in == 'on_time')
                                                    <span class="badge bg-success">Tepat Waktu</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($attendance->status_in ?? '-') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->late_minutes && $attendance->late_minutes > 0)
                                                    <span class="badge bg-warning">
                                                        {{ $attendance->late_minutes }} menit
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">-</span>
                            @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-3">Belum ada riwayat absensi</p>
        </div>
                            @endif
                                        </div>
                    </div>
                </div>

                <!-- Tab Cuti -->
                <div class="tab-pane fade" id="cuti" role="tabpanel" aria-labelledby="cuti-tab">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Riwayat Pengajuan Cuti
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($leaves->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="leaveTable">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Durasi</th>
                                                <th>Alasan</th>
                                                <th>Status</th>
                                                <th>Catatan Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($leaves as $leave)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($leave->created_at)->format('d/m/Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $leave->duration }} hari</span>
                                                    </td>
                                                    <td>{{ Str::limit($leave->reason, 50) }}</td>
                                                    <td>
                                                        @if($leave->status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($leave->status == 'approved')
                                                            <span class="badge bg-success">Disetujui</span>
                                                        @elseif($leave->status == 'rejected')
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($leave->admin_notes)
                                                            <span class="text-muted">{{ Str::limit($leave->admin_notes, 30) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">Belum ada riwayat pengajuan cuti</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Izin -->
                <div class="tab-pane fade" id="izin" role="tabpanel" aria-labelledby="izin-tab">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>
                                Riwayat Pengajuan Izin
                            </h4>
        </div>
                    <div class="card-body">
                            @if($permissions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="permissionTable">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pengajuan</th>
                                                <th>Tanggal Izin</th>
                                                <th>Alasan</th>
                                                <th>Status</th>
                                                <th>Catatan Admin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permissions as $permission)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($permission->created_at)->format('d/m/Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($permission->permission_date)->format('d/m/Y') }}</td>
                                                    <td>{{ Str::limit($permission->reason, 50) }}</td>
                                                    <td>
                                                        @if($permission->status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @elseif($permission->status == 'approved')
                                                            <span class="badge bg-success">Disetujui</span>
                                                        @elseif($permission->status == 'rejected')
                                                            <span class="badge bg-danger">Ditolak</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($permission->admin_notes)
                                                            <span class="text-muted">{{ Str::limit($permission->admin_notes, 30) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">Belum ada riwayat pengajuan izin</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
        </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    console.log('History page loaded');
    
    // Initialize DataTables for all tables
    if ($('#attendanceTable').length) {
        $('#attendanceTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            order: [[0, 'desc']], // Sort by date descending
            columnDefs: [
                {
                    targets: [1, 2, 3, 4], // Time columns and status
                    orderable: true,
                    searchable: true
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                console.log('Attendance DataTable initialized');
            }
        });
    }
    
    if ($('#leaveTable').length) {
        $('#leaveTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            order: [[0, 'desc']], // Sort by submission date descending
            columnDefs: [
                {
                    targets: [4, 6], // Reason and admin notes columns
                    orderable: false,
                    searchable: true
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                console.log('Leave DataTable initialized');
            }
        });
    }
    
    if ($('#permissionTable').length) {
        $('#permissionTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
            order: [[0, 'desc']], // Sort by submission date descending
            columnDefs: [
                {
                    targets: [2, 4], // Reason and admin notes columns
                    orderable: false,
                    searchable: true
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            initComplete: function() {
                console.log('Permission DataTable initialized');
            }
        });
    }
    
    // Test if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap is available');
        
        // Initialize tabs manually
        var triggerTabList = [].slice.call(document.querySelectorAll('#historyTab button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
                console.log('Tab clicked:', triggerEl.getAttribute('data-bs-target'));
                
                // Redraw DataTables when switching tabs to ensure proper rendering
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                        $('#attendanceTable').DataTable().columns.adjust().responsive.recalc();
                    }
                    if ($.fn.DataTable.isDataTable('#leaveTable')) {
                        $('#leaveTable').DataTable().columns.adjust().responsive.recalc();
                    }
                    if ($.fn.DataTable.isDataTable('#permissionTable')) {
                        $('#permissionTable').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 100);
            })
        })
        
    } else {
        console.log('Bootstrap not available, using manual fallback');
        
        // Manual fallback for tabs
        $('.nav-tabs .nav-link').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('bs-target');
            var $target = $(target);
            
            console.log('Manual tab click:', target);
            
            // Remove active class from all tabs and content
            $('.nav-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
            $('.tab-content .tab-pane').removeClass('show active');
            
            // Add active class to clicked tab and show target content
            $(this).addClass('active').attr('aria-selected', 'true');
            $target.addClass('show active');
            
            // Redraw DataTables when switching tabs
            setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#attendanceTable')) {
                    $('#attendanceTable').DataTable().columns.adjust().responsive.recalc();
                }
                if ($.fn.DataTable.isDataTable('#leaveTable')) {
                    $('#leaveTable').DataTable().columns.adjust().responsive.recalc();
                }
                if ($.fn.DataTable.isDataTable('#permissionTable')) {
                    $('#permissionTable').DataTable().columns.adjust().responsive.recalc();
                }
            }, 100);
            
            console.log('Manual tab switch to:', target);
        });
    }
    
    // Additional click handlers for debugging
    $('#cuti-tab').on('click', function() {
        console.log('Cuti tab clicked manually');
    });
    
    $('#izin-tab').on('click', function() {
        console.log('Izin tab clicked manually');
    });
    
    // Test tab functionality on page load
    setTimeout(function() {
        console.log('Testing tab functionality...');
        console.log('Active tab:', $('.nav-tabs .nav-link.active').attr('id'));
        console.log('Visible content:', $('.tab-content .tab-pane.show.active').attr('id'));
    }, 1000);
});
</script>
@endpush