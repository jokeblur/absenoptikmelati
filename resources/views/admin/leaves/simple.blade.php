@extends('layouts.admin')

@section('page_title', 'Persetujuan Cuti')
@section('breadcrumb_item', 'Persetujuan Cuti')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Daftar Pengajuan Cuti (Simple View)
                </h3>
            </div>
            <div class="card-body">
                @php
                    $leaves = \App\Models\Leave::with('user')->get();
                @endphp
                
                @if($leaves->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                            <tbody>
                                @foreach($leaves as $index => $leave)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $leave->user->name ?? 'N/A' }}</td>
                                        <td>{{ $leave->user->email ?? 'N/A' }}</td>
                                        <td>{{ $leave->start_date }}</td>
                                        <td>{{ $leave->end_date }}</td>
                                        <td>{{ $leave->duration }} hari</td>
                                        <td>{{ Str::limit($leave->reason, 50) }}</td>
                                        <td>
                                            @if($leave->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($leave->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($leave->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" data-id="{{ $leave->id }}" class="btn btn-info btn-sm viewLeave">Detail</a>
                                            @if($leave->status === 'pending')
                                                <a href="javascript:void(0)" data-id="{{ $leave->id }}" class="btn btn-success btn-sm approveLeave">Setujui</a>
                                                <a href="javascript:void(0)" data-id="{{ $leave->id }}" class="btn btn-danger btn-sm rejectLeave">Tolak</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-3">Belum ada pengajuan cuti</p>
                    </div>
                @endif
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
@endsection

@push('scripts')
<script>
$(function () {
    // View Detail
    $('body').on('click', '.viewLeave', function () {
        var leaveId = $(this).data('id');
        $.get("{{ route('admin.leaves.index') }}/" + leaveId, function (data) {
            $('#detail-employee-name').text(data.user.name);
            $('#detail-employee-email').text(data.user.email);
            $('#detail-start-date').text(data.start_date);
            $('#detail-end-date').text(data.end_date);
            $('#detail-duration').text(data.duration + ' hari');
            $('#detail-status').html('<span class="badge badge-' + getStatusBadgeClass(data.status) + '">' + data.status.toUpperCase() + '</span>');
            $('#detail-created-at').text(data.created_at);
            $('#detail-reason').text(data.reason);
            
            if (data.admin_notes) {
                $('#detail-admin-notes').text(data.admin_notes);
                $('#admin-notes-section').show();
            } else {
                $('#admin-notes-section').hide();
            }
            
            $('#leaveDetailModal').modal('show');
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
});
</script>
@endpush 