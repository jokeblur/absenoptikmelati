@extends('layouts.admin')

@section('page_title', 'Persetujuan Cuti')
@section('breadcrumb_item', 'Persetujuan Cuti')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Daftar Pengajuan Cuti
                </h3>
            </div>
            <div class="card-body">
                <table id="leavesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan</th>
                            <th>Email</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Cuti -->
<div class="modal fade" id="leaveDetailModal" tabindex="-1" role="dialog" aria-labelledby="leaveDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveDetailModalLabel">Detail Pengajuan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Karyawan:</strong> <span id="detail-employee-name"></span></p>
                        <p><strong>Email:</strong> <span id="detail-employee-email"></span></p>
                        <p><strong>Tanggal Mulai:</strong> <span id="detail-start-date"></span></p>
                        <p><strong>Tanggal Selesai:</strong> <span id="detail-end-date"></span></p>
                        <p><strong>Durasi:</strong> <span id="detail-duration"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span id="detail-status"></span></p>
                        <p><strong>Tanggal Pengajuan:</strong> <span id="detail-created-at"></span></p>
                        <p><strong>Alasan:</strong></p>
                        <div class="border p-2 bg-light">
                            <span id="detail-reason"></span>
                        </div>
                        <div id="admin-notes-section" style="display: none;">
                            <p><strong>Catatan Admin:</strong></p>
                            <div class="border p-2 bg-light">
                                <span id="detail-admin-notes"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve/Reject -->
<div class="modal fade" id="approveRejectModal" tabindex="-1" role="dialog" aria-labelledby="approveRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveRejectModalLabel">Persetujuan Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveRejectForm">
                <div class="modal-body">
                    <input type="hidden" id="leave-id" name="leave_id">
                    <input type="hidden" id="action-type" name="action_type">
                    
                    <div class="form-group">
                        <label for="admin-notes">Catatan Admin (Opsional)</label>
                        <textarea class="form-control" id="admin-notes" name="admin_notes" rows="3" placeholder="Masukkan catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="action-message"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="submit-btn">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    {{-- SweetAlert2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

<script>
$(function () {
    console.log('Initializing DataTable...');
    
    try {
        var table = $('#leavesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: false, // Disable responsive
            ajax: {
                url: "{{ route('admin.leaves.index') }}",
                type: "GET",
                error: function (xhr, error, thrown) {
                    console.error('DataTables error:', error);
                    console.error('XHR:', xhr);
                    alert('Error loading data: ' + error);
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'employee_email', name: 'employee_email'},
                {data: 'start_date', name: 'start_date', render: function(data){ return formatDateID(data); }},
                {data: 'end_date', name: 'end_date', render: function(data){ return formatDateID(data); }},
                {data: 'duration', name: 'duration'},
                {data: 'reason', name: 'reason'},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[3, 'desc']], // Urutkan berdasarkan tanggal mulai (terbaru)
            pageLength: 10
        });
        
        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        alert('Error initializing DataTable: ' + error.message);
    }

    // View Detail
    $('body').on('click', '.viewLeave', function () {
        var leaveId = $(this).data('id');
        console.log('Viewing leave ID:', leaveId);
        
        $.get("{{ route('admin.leaves.index') }}/" + leaveId, function (data) {
            $('#detail-employee-name').text(data.user.name);
            $('#detail-employee-email').text(data.user.email);
            $('#detail-start-date').text(formatDateID(data.start_date));
            $('#detail-end-date').text(formatDateID(data.end_date));
            $('#detail-duration').text(data.duration + ' hari');
            $('#detail-status').html('<span class="badge badge-' + getStatusBadgeClass(data.status) + '">' + data.status.toUpperCase() + '</span>');
            $('#detail-created-at').text(formatDateTimeID(data.created_at));
            $('#detail-reason').text(data.reason);
            
            if (data.admin_notes) {
                $('#detail-admin-notes').text(data.admin_notes);
                $('#admin-notes-section').show();
            } else {
                $('#admin-notes-section').hide();
            }
            
            $('#leaveDetailModal').modal('show');
        }).fail(function(xhr, status, error) {
            console.error('Error loading leave details:', error);
            Swal.fire('Error!', 'Gagal memuat detail cuti.', 'error');
        });
    });

    // Approve Leave
    $('body').on('click', '.approveLeave', function () {
        var leaveId = $(this).data('id');
        $('#leave-id').val(leaveId);
        $('#action-type').val('approve');
        $('#action-message').text('Anda akan menyetujui pengajuan cuti ini.');
        $('#submit-btn').removeClass('btn-danger').addClass('btn-success').text('Setujui');
        $('#approveRejectModal').modal('show');
    });

    // Reject Leave
    $('body').on('click', '.rejectLeave', function () {
        var leaveId = $(this).data('id');
        $('#leave-id').val(leaveId);
        $('#action-type').val('reject');
        $('#action-message').text('Anda akan menolak pengajuan cuti ini.');
        $('#submit-btn').removeClass('btn-success').addClass('btn-danger').text('Tolak');
        $('#approveRejectModal').modal('show');
    });

    // Submit Approve/Reject
    $('#approveRejectForm').submit(function(e) {
        e.preventDefault();
        var leaveId = $('#leave-id').val();
        var actionType = $('#action-type').val();
        var adminNotes = $('#admin-notes').val();
        
        $.ajax({
            url: "{{ route('admin.leaves.index') }}/" + leaveId + "/" + actionType,
            type: "POST",
            data: {
                admin_notes: adminNotes,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#approveRejectModal').modal('hide');
                $('#approveRejectForm')[0].reset();
                table.ajax.reload();
                Swal.fire('Berhasil!', response.success, 'success');
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire('Gagal!', errorMessage, 'error');
            }
        });
    });

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'approved': return 'success';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }

    // Helper untuk format tanggal dd/mm/yyyy (jika belum ada)
    function formatDateID(dateStr) {
        if (!dateStr) return '-';
        var d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        var day = String(d.getDate()).padStart(2, '0');
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var year = d.getFullYear();
        return day + '/' + month + '/' + year;
    }
    function formatDateTimeID(dateTimeStr) {
        if (!dateTimeStr) return '-';
        var d = new Date(dateTimeStr.replace(' ', 'T'));
        if (isNaN(d)) return dateTimeStr;
        var day = String(d.getDate()).padStart(2, '0');
        var month = String(d.getMonth() + 1).padStart(2, '0');
        var year = d.getFullYear();
        var hours = String(d.getHours()).padStart(2, '0');
        var minutes = String(d.getMinutes()).padStart(2, '0');
        var seconds = String(d.getSeconds()).padStart(2, '0');
        return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes + ':' + seconds;
    }
});
</script>
@endpush 