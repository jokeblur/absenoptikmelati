@extends('layouts.employee')

@section('title', 'Pengajuan Cuti & Izin')

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #2c3e50 !important;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #3498db !important;
        background-color: #ecf0f1 !important;
        border-color: #bdc3c7 #bdc3c7 #fff !important;
    }
    
    .nav-tabs .nav-link:hover {
        color: #3498db !important;
        border-color: #e9ecef #e9ecef #dee2e6 !important;
    }
    
    .form-label {
        color: #2c3e50 !important;
        font-weight: 500;
    }
    
    .card {
        border: 1px solid #bdc3c7;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    
    .card-body {
        background-color: #fff;
    }
    
    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
    }
    
    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }
</style>
@endpush

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4 text-center text-dark">Pengajuan Cuti & Izin</h3>

        <ul class="nav nav-tabs nav-fill mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-dark" id="cuti-tab" data-bs-toggle="tab" data-bs-target="#cuti" type="button" role="tab" aria-controls="cuti" aria-selected="true">
                    <i class="fas fa-calendar-alt me-1"></i>Pengajuan Cuti
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark" id="izin-tab" data-bs-toggle="tab" data-bs-target="#izin" type="button" role="tab" aria-controls="izin" aria-selected="false">
                    <i class="fas fa-file-alt me-1"></i>Pengajuan Izin
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="cuti" role="tabpanel" aria-labelledby="cuti-tab">
                <div class="card">
                    <div class="card-body">
                        <form id="leaveForm">
                            <div class="mb-3">
                                <label for="startDate" class="form-label text-dark">Tanggal Mulai Cuti</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="endDate" class="form-label text-dark">Tanggal Selesai Cuti</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="leaveReason" class="form-label text-dark">Alasan Cuti</label>
                                <textarea class="form-control" id="leaveReason" name="reason" rows="3" required></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan Cuti
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="izin" role="tabpanel" aria-labelledby="izin-tab">
                <div class="card">
                    <div class="card-body">
                        <form id="permissionForm">
                            <div class="mb-3">
                                <label for="permissionDate" class="form-label text-dark">Tanggal Izin</label>
                                <input type="date" class="form-control" id="permissionDate" name="permission_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="permissionReason" class="form-label text-dark">Alasan Izin</label>
                                <textarea class="form-control" id="permissionReason" name="reason" rows="3" required></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan Izin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        console.log('Submission page loaded');
        
        // Simple tab switching with jQuery
        $('#myTab button').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('bs-target');
            console.log('Tab switch to:', target);
            
            // Remove active from all tabs and panes
            $('#myTab button').removeClass('active');
            $('.tab-pane').removeClass('show active');
            
            // Add active to clicked tab
            $(this).addClass('active');
            $(target).addClass('show active');
        });

        // Loading functions
        function showLoading() {
            $('button[type="submit"]').prop('disabled', true);
        }
        
        function hideLoading() {
            $('button[type="submit"]').prop('disabled', false);
        }

        $('#leaveForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            $.ajax({
                url: "{{ route('employee.pengajuan.store_leave') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#leaveForm')[0].reset();
                },
                error: function(xhr) {
                    hideLoading();
                    let errorMessage = 'Terjadi kesalahan saat mengajukan cuti.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            });
        });

        $('#permissionForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            $.ajax({
                url: "{{ route('employee.pengajuan.store_permission') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    hideLoading();
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#permissionForm')[0].reset();
                },
                error: function(xhr) {
                    hideLoading();
                    let errorMessage = 'Terjadi kesalahan saat mengajukan izin.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire('Gagal!', errorMessage, 'error');
                }
            });
        });
    });
</script>
@endpush