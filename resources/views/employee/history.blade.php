@extends('layouts.employee')

@section('title', 'Riwayat Absensi')

@push('styles')
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
                            <table class="table table-striped">
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

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $attendances->links() }}
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
                                    <table class="table table-striped">
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
                                    <table class="table table-striped">
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
<script>
$(document).ready(function() {
    console.log('History page loaded');
    
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