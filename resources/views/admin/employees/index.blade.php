@extends('layouts.admin')

@section('page_title', 'Manajemen Karyawan')
@section('breadcrumb_item', 'Karyawan')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    {{-- SweetAlert2 CSS (opsional, untuk notifikasi yang lebih baik) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Karyawan</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="createNewEmployee">
                        <i class="fas fa-plus"></i> Tambah Karyawan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employee-table" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Cabang</th>
                                <th width="150px">Aksi</th>
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
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="employeeForm" name="employeeForm" class="form-horizontal">
                   <input type="hidden" name="employee_id" id="employee_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-12 control-label">Nama</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama" value="" maxlength="50" required="">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-12 control-label">Email</label>
                        <div class="col-sm-12">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" value="" required="">
                            <span class="text-danger error-text email_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-12 control-label">Password</label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password (kosongkan jika tidak diubah)" value="">
                            <span class="text-danger error-text password_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="branch_id" class="col-sm-12 control-label">Cabang</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="branch_id" name="branch_id">
                                <option value="">Pilih Cabang</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text branch_id_error"></span>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan Perubahan</button>
                        <a href="#" id="editWorkScheduleBtn" class="btn btn-info" style="display: none;">
                            <i class="fas fa-clock"></i> Edit Jadwal Kerja
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Jadwal Kerja -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Jadwal Kerja untuk <span id="employeeName"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body">
                <div id="scheduleDetailsContent">
                    <p class="text-center">Memuat jadwal...</p>
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
    {{-- SweetAlert2 JS (opsional) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>

    <script type="text/javascript">
      $(function () {
          // Check for success message from meta tag and display with SweetAlert
          const successMessage = $('meta[name="success-message"]').attr('content');
          if (successMessage) {
              Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: successMessage,
                  showConfirmButton: false,
                  timer: 2500
              });
          }

          // Setup CSRF token for all AJAX requests
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          // Inisialisasi Datatables
          var table = $('#employee-table').DataTable({
              processing: true,
              serverSide: true,
              responsive: false, // Disable responsive
              ajax: "{{ route('admin.employees.index') }}",
              columns: [
                   {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                  {data: 'name', name: 'name'},
                  {data: 'email', name: 'email'},
                  {data: 'branch_name', name: 'branch.name'},
                  {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              order: [[1, 'asc']], 
          });

          // Event handler untuk tombol "Tambah Karyawan"
          $('#createNewEmployee').click(function () {
              $('#saveBtn').val("create-employee");
              $('#employee_id').val('');
              $('#employeeForm').trigger("reset"); // Reset semua input form
              $('#modelHeading').html("Tambah Karyawan Baru");
              $('.error-text').html(''); // Bersihkan pesan error
              $('#ajaxModel').modal('show');
              $('#password').attr('required', true); // Password wajib saat tambah
              $('#editWorkScheduleBtn').hide(); // Sembunyikan tombol jadwal kerja saat membuat baru
          });

          // Event handler untuk tombol "Edit"
$('body').on('click', '.editEmployee', function () {
    var employee_id = $(this).data('id');
    // Ini memanggil rute /admin/employees/{id}/edit
    $.get("{{ route('admin.employees.index') }}" +'/' + employee_id + '/edit', function (data) {
        $('#modelHeading').html("Edit Karyawan");
        $('#saveBtn').val("edit-employee");
        $('#ajaxModel').modal('show');
        $('#employee_id').val(data.id);
        $('#name').val(data.name);
        $('#email').val(data.email);
        $('#branch_id').val(data.branch_id);
        $('#password').val('');
        $('#password').attr('required', false);
        $('.error-text').html('');

        // Tampilkan dan atur URL untuk tombol edit jadwal kerja
        var scheduleUrl = "{{ route('admin.work-schedules.edit', ['user' => ':id']) }}";
        scheduleUrl = scheduleUrl.replace(':id', data.id);
        $('#editWorkScheduleBtn').attr('href', scheduleUrl).show();

    }).fail(function(data) {
        Swal.fire('Error!', 'Gagal mengambil data karyawan.', 'error');
    });
});

          // Event handler untuk submit form (Tambah/Edit)
          $('#employeeForm').on('submit', function (e) {
              e.preventDefault();
              $('#saveBtn').html('Mengirim..');

              var formData = new FormData(this);
              var employee_id = $('#employee_id').val();
              var url = employee_id ? "{{ url('admin/employees') }}/" + employee_id : "{{ route('admin.employees.store') }}";
              var type = 'POST';

              if (employee_id) {
                  formData.append('_method', 'PUT');
              }

              $.ajax({
                  data: formData,
                  url: url,
                  type: type,
                  contentType: false,
                  processData: false,
                  success: function (data) {
                      $('#employeeForm').trigger("reset");
                      $('#ajaxModel').modal('hide');
                      table.draw(); // Refresh Datatables
                      $('#saveBtn').html('Simpan Perubahan');
                      Swal.fire('Berhasil!', data.success, 'success'); // Notifikasi sukses
                  },
                  error: function (data) {
                      console.log('Error:', data);
                      $('#saveBtn').html('Simpan Perubahan');
                      // Tampilkan pesan error validasi
                      $.each(data.responseJSON.errors, function(key, val){
                          $('.'+key+'_error').text(val[0]);
                      });
                      Swal.fire('Error!', 'Terjadi kesalahan. Periksa input Anda.', 'error'); // Notifikasi error umum
                  }
              });
          });

          // Event handler untuk tombol "Delete"
          $('body').on('click', '.deleteEmployee', function () {
              var employee_id = $(this).data("id");
              Swal.fire({
                  title: 'Anda Yakin?',
                  text: "Anda tidak akan bisa mengembalikan ini!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Ya, Hapus Saja!'
              }).then((result) => {
                  if (result.isConfirmed) {
                      $.ajax({
                          type: "DELETE",
                          url: "{{ route('admin.employees.store') }}"+'/'+employee_id, // Menggunakan rute DELETE
                          success: function (data) {
                              table.draw();
                              Swal.fire('Dihapus!', data.success, 'success');
                          },
                          error: function (data) {
                              console.log('Error:', data);
                              Swal.fire('Error!', 'Terjadi kesalahan saat menghapus.', 'error');
                          }
                      });
                  }
              });
          });

          // Event handler untuk tombol "Lihat Jadwal"
          $('body').on('click', '.view-schedule', function () {
              var userId = $(this).data('id');
              var userName = $(this).data('name');
              $('#employeeName').text(userName);
              $('#scheduleDetailsContent').html('<p class="text-center">Memuat jadwal...</p>');
              $('#scheduleModal').modal('show');

              var url = "{{ route('admin.employees.schedule-details', ['user' => ':userId']) }}";
              url = url.replace(':userId', userId);

              $.get(url, function (data) {
                  var content = '<table class="table table-bordered table-striped">';
                  content += '<thead class="table-dark"><tr><th>Hari</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Istirahat</th></tr></thead><tbody>';
                  
                  var days = {
                      'monday': 'Senin', 'tuesday': 'Selasa', 'wednesday': 'Rabu', 
                      'thursday': 'Kamis', 'friday': 'Jumat', 'saturday': 'Sabtu', 'sunday': 'Minggu'
                  };

                  $.each(days, function(key, dayName) {
                      var schedule = data[key];
                      content += '<tr>';
                      content += '<td>' + dayName + '</td>';

                      if (key === 'sunday') {
                          content += '<td colspan="3"><span class="badge bg-secondary">Hari Libur</span></td>';
                      } else if (schedule && (schedule.is_holiday == 0 || schedule.is_holiday == '0')) {
                          var clockIn = schedule.clock_in ? new Date('1970-01-01T' + schedule.clock_in).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                          var clockOut = schedule.clock_out ? new Date('1970-01-01T' + schedule.clock_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                          var breakTime = (schedule.break_start_time && schedule.break_end_time)
                              ? new Date('1970-01-01T' + schedule.break_start_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' - ' + new Date('1970-01-01T' + schedule.break_end_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
                              : '-';
                          
                          content += '<td><span class="badge bg-success">' + clockIn + '</span></td>';
                          content += '<td><span class="badge bg-danger">' + clockOut + '</span></td>';
                          content += '<td><span class="badge bg-info">' + breakTime + '</span></td>';
                      } else {
                          content += '<td colspan="3"><span class="badge bg-secondary">Hari Libur</span></td>';
                      }
                      content += '</tr>';
                  });

                  content += '</tbody></table>';
                  $('#scheduleDetailsContent').html(content);
              }).fail(function() {
                  $('#scheduleDetailsContent').html('<p class="text-center text-danger">Gagal memuat jadwal.</p>');
              });
        });
      });
    </script>
@endpush