@extends('layouts.admin')

@section('page_title', 'Export Data Absensi')
@section('breadcrumb_item', 'Export Data')

@push('styles')
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
    {{-- Date Range Picker --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-download mr-1"></i>
                    Export Data Absensi
                </h3>
            </div>
            <div class="card-body">
                <form id="exportForm" method="POST" action="{{ route('admin.attendance.export_data') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Dari Tanggal:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ date('Y-m-d', strtotime('-30 days')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">Sampai Tanggal:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">Karyawan:</label>
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="">Semua Karyawan</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch_id">Cabang:</label>
                                <select class="form-control" id="branch_id" name="branch_id">
                                    <option value="">Semua Cabang</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="format">Format Export:</label>
                                <select class="form-control" id="format" name="format" required>
                                    <option value="csv">CSV</option>
                                    <option value="excel">Excel</option>
                                    <option value="pdf">PDF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Tipe Data:</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="all">Semua Data</option>
                                    <option value="check_in">Check-in Saja</option>
                                    <option value="check_out">Check-out Saja</option>
                                    <option value="late">Keterlambatan</option>
                                    <option value="absent">Tidak Hadir</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="exportBtn">
                                <i class="fas fa-download mr-1"></i>
                                Export Data
                            </button>
                            <button type="button" class="btn btn-secondary" id="previewBtn">
                                <i class="fas fa-eye mr-1"></i>
                                Preview Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i>
                    Riwayat Export
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal Export</th>
                            <th>Periode</th>
                            <th>Format</th>
                            <th>Filter</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ date('d/m/Y H:i') }}</td>
                            <td>01/12/2023 - 31/12/2023</td>
                            <td><span class="badge badge-info">CSV</span></td>
                            <td>Semua Karyawan</td>
                            <td><span class="badge badge-success">Selesai</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Download</a>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime('-1 day')) }}</td>
                            <td>01/11/2023 - 30/11/2023</td>
                            <td><span class="badge badge-warning">Excel</span></td>
                            <td>Karyawan Terlambat</td>
                            <td><span class="badge badge-success">Selesai</span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Download</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Data Absensi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="exportFromPreview">Export Data Ini</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('AdminLTE-3.0.1/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('AdminLTE-3.0.1/plugins/daterangepicker/daterangepicker.js') }}"></script>

<script>
$(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Export form handler
    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        const format = $('#format').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (!startDate || !endDate) {
            Swal.fire('Error!', 'Pilih tanggal mulai dan selesai.', 'error');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            Swal.fire('Error!', 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.', 'error');
            return;
        }
        
        $('#exportBtn').html('<i class="fas fa-spinner fa-spin mr-1"></i>Exporting...');
        $('#exportBtn').prop('disabled', true);
        
        // Submit form
        this.submit();
        
        // Reset button after delay
        setTimeout(() => {
            $('#exportBtn').html('<i class="fas fa-download mr-1"></i>Export Data');
            $('#exportBtn').prop('disabled', false);
        }, 3000);
    });

    // Preview button handler
    $('#previewBtn').click(function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const userId = $('#user_id').val();
        const branchId = $('#branch_id').val();
        const type = $('#type').val();
        
        if (!startDate || !endDate) {
            Swal.fire('Error!', 'Pilih tanggal mulai dan selesai.', 'error');
            return;
        }
        
        $('#previewModal').modal('show');
        
        // Load preview data
        $.ajax({
            url: '{{ route("admin.attendance.preview") }}',
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate,
                user_id: userId,
                branch_id: branchId,
                type: type
            },
            success: function(response) {
                $('#previewContent').html(response);
            },
            error: function() {
                $('#previewContent').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x"></i><p>Gagal memuat preview data.</p></div>');
            }
        });
    });

    // Export from preview
    $('#exportFromPreview').click(function() {
        $('#previewModal').modal('hide');
        $('#exportForm').submit();
    });

    // Date validation
    $('#end_date').on('change', function() {
        const startDate = $('#start_date').val();
        const endDate = $(this).val();
        
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            Swal.fire('Warning!', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.', 'warning');
        }
    });
});
</script>
@endpush 