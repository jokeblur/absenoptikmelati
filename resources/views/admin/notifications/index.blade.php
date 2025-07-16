@extends('layouts.admin')

@section('page_title', 'Notifikasi')
@section('breadcrumb_item', 'Notifikasi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>
                    Daftar Notifikasi
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" id="markAllRead">
                        <i class="fas fa-check-double mr-1"></i>
                        Tandai Semua Dibaca
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Pesan</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $index => $notification)
                            <tr class="{{ $notification->is_read ? '' : 'table-warning' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ $notification->message }}</td>
                                <td>
                                    @if($notification->type == 'leave_request')
                                        <span class="badge badge-info">Pengajuan Cuti</span>
                                    @elseif($notification->type == 'permission_request')
                                        <span class="badge badge-warning">Pengajuan Izin</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $notification->type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->is_read)
                                        <span class="badge badge-success">Dibaca</span>
                                    @else
                                        <span class="badge badge-warning">Baru</span>
                                    @endif
                                </td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if(!$notification->is_read)
                                        <button type="button" class="btn btn-sm btn-primary mark-read" data-id="{{ $notification->id }}">
                                            <i class="fas fa-check mr-1"></i>
                                            Tandai Dibaca
                                        </button>
                                    @else
                                        <span class="text-muted">Sudah dibaca</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada notifikasi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mark single notification as read
    $('.mark-read').on('click', function() {
        var notificationId = $(this).data('id');
        var $row = $(this).closest('tr');
        
        $.ajax({
            url: '{{ route("admin.notifications.read", ":id") }}'.replace(':id', notificationId),
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $row.removeClass('table-warning');
                $row.find('td:eq(4)').html('<span class="badge badge-success">Dibaca</span>');
                $row.find('td:eq(6)').html('<span class="text-muted">Sudah dibaca</span>');
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    });
    
    // Mark all notifications as read
    $('#markAllRead').on('click', function() {
        var unreadNotifications = $('.mark-read');
        
        if (unreadNotifications.length === 0) {
            alert('Tidak ada notifikasi yang belum dibaca');
            return;
        }
        
        if (confirm('Tandai semua notifikasi sebagai sudah dibaca?')) {
            unreadNotifications.each(function() {
                var notificationId = $(this).data('id');
                var $row = $(this).closest('tr');
                
                $.ajax({
                    url: '{{ route("admin.notifications.read", ":id") }}'.replace(':id', notificationId),
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $row.removeClass('table-warning');
                        $row.find('td:eq(4)').html('<span class="badge badge-success">Dibaca</span>');
                        $row.find('td:eq(6)').html('<span class="text-muted">Sudah dibaca</span>');
                    }
                });
            });
            
            setTimeout(function() {
                location.reload();
            }, 1000);
        }
    });
});
</script>
@endpush 