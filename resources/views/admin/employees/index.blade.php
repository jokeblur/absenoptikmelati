@extends('layouts.admin')

@section('page_title', 'Manajemen Karyawan')
@section('breadcrumb_item', 'Karyawan')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
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

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan modal setting jam kerja -->
<div class="modal fade" id="workScheduleModal" tabindex="-1" role="dialog" aria-labelledby="workScheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="workScheduleModalLabel">Setting Jam Kerja Karyawan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="workScheduleForm">
        <div class="modal-body">
          <input type="hidden" id="ws_employee_id" name="employee_id">
          <div id="workScheduleDays">
            <!-- Akan diisi dinamis oleh JS -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
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
    {{-- SweetAlert2 JS (opsional) --}}
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
          var table = $('#employee-table').DataTable({
              processing: true,
              serverSide: true,
              ajax: "{{ route('admin.employees.index') }}",
              columns: [
                   {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false}, // Nomor urut dari Yajra Datatables
                  {data: 'name', name: 'name'},
                  {data: 'email', name: 'email'},
                  {data: 'branch_name', name: 'branch_name'},
                  {data: 'custom_clock_in_time', name: 'custom_clock_in_time'},
                  {data: 'custom_clock_out_time', name: 'custom_clock_out_time'},

                   // Kolom baru dari addColumn di controller
                  {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              order: [[1, 'asc']], // Urutkan berdasarkan kolom kedua (nama) secara ascending sebagai default
              drawCallback: function(settings) {
                $('.editEmployee').each(function() {
                  var employee_id = $(this).data('id');
                  var $row = $(this).closest('tr');
                  if ($row.find('.btn-work-schedule').length === 0) {
                    $row.find('td:last').prepend('<button class="btn btn-sm btn-warning btn-work-schedule mr-1" data-id="'+employee_id+'"><i class="fa fa-clock"></i> Setting Jam Kerja</button>');
                  }
                });
                // Event handler tombol Setting Jam Kerja
                $('.btn-work-schedule').off('click').on('click', function() {
                  var employee_id = $(this).data('id');
                  $('#ws_employee_id').val(employee_id);
                  // Load jadwal kerja dari backend
                  $.get('/admin/employees/'+employee_id+'/work-schedule', function(data) {
                    var days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
                    var html = '';
                    days.forEach(function(day, idx) {
                      var dkey = day.toLowerCase();
                      var ws = data.find(x => x.day === dkey) || {};
                      html += '<div class="form-group row align-items-center">';
                      html += '<label class="col-sm-3 col-form-label">'+day+'</label>';
                      if(day === 'Minggu') {
                        html += '<div class="col-sm-9"><input type="hidden" name="days['+dkey+'][is_holiday]" value="1"><span class="badge badge-danger">Libur</span></div>';
                      } else {
                        html += '<div class="col-sm-4"><input type="time" class="form-control" name="days['+dkey+'][clock_in]" value="'+(ws.clock_in||'08:00')+'"></div>';
                        html += '<div class="col-sm-4"><input type="time" class="form-control" name="days['+dkey+'][clock_out]" value="'+(ws.clock_out||'17:00')+'"></div>';
                        html += '<div class="col-sm-1"><input type="checkbox" name="days['+dkey+'][is_holiday]" value="1" '+(ws.is_holiday?'checked':'')+'> Libur</div>';
                      }
                      html += '</div>';
                    });
                    $('#workScheduleDays').html(html);
                    $('#workScheduleModal').modal('show');
                  });
                });
              }
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
    }).fail(function(data) {
        Swal.fire('Error!', 'Gagal mengambil data karyawan.', 'error');
    });
});

          // Event handler untuk submit form (Tambah/Edit)
          $('#saveBtn').click(function (e) {
              e.preventDefault();
              $(this).html('Mengirim..');

              var formData = new FormData($('#employeeForm')[0]);
              var employee_id = $('#employee_id').val();
              var url = employee_id ? "{{ route('admin.employees.store') }}/" + employee_id : "{{ route('admin.employees.store') }}";
              var type = employee_id ? 'POST' : 'POST'; // Laravel menggunakan POST untuk PUT/PATCH

              if (employee_id) {
                  formData.append('_method', 'PUT'); // Tambahkan method override untuk PUT
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
      });

      // Submit form setting jam kerja
      $('#workScheduleForm').on('submit', function(e) {
        e.preventDefault();
        var employee_id = $('#ws_employee_id').val();
        var formData = $(this).serialize();
        $.post('/admin/employees/'+employee_id+'/work-schedule', formData, function(res) {
          $('#workScheduleModal').modal('hide');
          Swal.fire('Berhasil', 'Jadwal kerja berhasil disimpan', 'success');
        }).fail(function(xhr) {
          Swal.fire('Error', 'Gagal menyimpan jadwal kerja', 'error');
        });
      });
    </script>
@endpush