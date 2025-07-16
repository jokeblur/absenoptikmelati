@extends('layouts.admin')

@section('page_title', 'Persetujuan Izin Karyawan')
@section('breadcrumb_item', 'Persetujuan Izin')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengajuan Izin</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="permission-table" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Email Karyawan</th>
                                <th>Tanggal Izin</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th width="200px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="detailPermissionModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="detailModalHeading">Detail Pengajuan Izin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Nama Karyawan:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_employee_name"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Email Karyawan:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_employee_email"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Tanggal Izin:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_permission_date"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Alasan:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_reason"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_status"></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Catatan Admin:</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="detail_admin_notes"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminNotesModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="notesModalHeading"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="notesForm">
                    <input type="hidden" name="permission_id" id="permission_id_for_notes">
                    <input type="hidden" name="action_type" id="action_type">
                    <div class="form-group">
                        <label for="admin_notes">Catatan Admin:</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                        <span class="text-danger error-text admin_notes_error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitNotesBtn">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
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

    <script type="text/javascript">
      $(function () {
          // Setup CSRF token for all AJAX requests
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          // Inisialisasi Datatables
          var table = $('#permission-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: "{{ route('admin.permissions.index') }}",
              columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                  {data: 'employee_name', name: 'user.name'}, // Akses relasi user.name
                  {data: 'employee_email', name: 'user.email'}, // Akses relasi user.email
                  {data: 'permission_date', name: 'permission_date', render: function(data){ return formatDateID(data); }},
                  {data: 'reason', name: 'reason'},
                  {data: 'status_badge', name: 'status', orderable: false, searchable: false}, // Tampilkan badge
                  {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              order: [[0, 'desc']] // Default order berdasarkan tanggal pengajuan terbaru
          });

          // Event handler untuk tombol "Detail"
          $('body').on('click', '.viewPermission', function () {
              var permission_id = $(this).data('id');
              $.get("{{ route('admin.permissions.index') }}" +'/' + permission_id, function (data) {
                  $('#detail_employee_name').text(data.user.name);
                  $('#detail_employee_email').text(data.user.email);
                  $('#detail_permission_date').text(formatDateID(data.permission_date));
                  $('#detail_reason').text(data.reason);
                  $('#detail_status').html('<span class="badge ' + (data.status === 'pending' ? 'badge-warning' : (data.status === 'approved' ? 'badge-success' : 'badge-danger')) + '">' + data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</span>');
                  $('#detail_admin_notes').text(data.admin_notes || '-');
                  $('#detailPermissionModal').modal('show');
              }).fail(function(data) {
                  Swal.fire('Error!', 'Gagal mengambil detail izin.', 'error');
              });
          });

          // Event handler untuk tombol "Setujui"
          $('body').on('click', '.approvePermission', function () {
              var permission_id = $(this).data('id');
              $('#permission_id_for_notes').val(permission_id);
              $('#action_type').val('approve');
              $('#notesModalHeading').html('Setujui Pengajuan Izin');
              $('#admin_notes').attr('required', false).val(''); // Catatan tidak wajib saat setuju
              $('.error-text').html('');
              $('#adminNotesModal').modal('show');
          });

          // Event handler untuk tombol "Tolak"
          $('body').on('click', '.rejectPermission', function () {
              var permission_id = $(this).data('id');
              $('#permission_id_for_notes').val(permission_id);
              $('#action_type').val('reject');
              $('#notesModalHeading').html('Tolak Pengajuan Izin');
              $('#admin_notes').attr('required', true).val(''); // Catatan wajib saat tolak
              $('.error-text').html('');
              $('#adminNotesModal').modal('show');
          });

          // Submit form catatan admin (Approve/Reject)
          $('#submitNotesBtn').click(function (e) {
              e.preventDefault();
              $(this).html('Memproses..');

              var permission_id = $('#permission_id_for_notes').val();
              var action_type = $('#action_type').val();
              var admin_notes = $('#admin_notes').val();
              var url = '';

              if (action_type === 'approve') {
                  url = "{{ url('admin/permissions') }}/" + permission_id + "/approve";
              } else if (action_type === 'reject') {
                  url = "{{ url('admin/permissions') }}/" + permission_id + "/reject";
              }

              $.ajax({
                  data: { admin_notes: admin_notes },
                  url: url,
                  type: "POST",
                  dataType: 'json',
                  success: function (data) {
                      $('#adminNotesModal').modal('hide');
                      table.draw(); // Refresh Datatables
                      $('#submitNotesBtn').html('Simpan');
                      Swal.fire('Berhasil!', data.success, 'success');
                  },
                  error: function (data) {
                      console.log('Error:', data);
                      $('#submitNotesBtn').html('Simpan');
                      if (data.responseJSON && data.responseJSON.errors) {
                          $.each(data.responseJSON.errors, function(key, val){
                              $('.'+key+'_error').text(val[0]);
                          });
                      }
                      Swal.fire('Error!', data.responseJSON.error || 'Terjadi kesalahan.', 'error');
                  }
              });
          });
      });

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
    </script>
@endpush