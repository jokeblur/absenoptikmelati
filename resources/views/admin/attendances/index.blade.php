@extends('layouts.admin')

@section('page_title', 'Manajemen Absensi Karyawan')
@section('breadcrumb_item', 'Absensi')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    {{-- Tempus Dominus (Date/Time Picker untuk AdminLTE 3) --}}
    <link rel="stylesheet" href="{{ asset('AdminLTE-3.0.1/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    {{-- SweetAlert2 CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Absensi Karyawan (Per Event)</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="createNewAttendance">
                        <i class="fas fa-plus"></i> Tambah Absensi Manual
                    </button>
                </div>
            </div>
            <div class="card-body"> {{-- Pastikan ini tertutup dengan benar --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filter_start_date">Dari Tanggal:</label>
                        <div class="input-group date" id="filter_start_date_picker" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#filter_start_date_picker" data-toggle="datetimepicker" id="filter_start_date"/>
                            <div class="input-group-append" data-target="#filter_start_date_picker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_end_date">Sampai Tanggal:</label>
                        <div class="input-group date" id="filter_end_date_picker" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#filter_end_date_picker" data-toggle="datetimepicker" id="filter_end_date"/>
                            <div class="input-group-append" data-target="#filter_end_date_picker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_user_id">Karyawan:</label>
                        <select class="form-control" id="filter_user_id">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_branch_id">Cabang:</label>
                        <select class="form-control" id="filter_branch_id">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_late_status">Status Keterlambatan:</label>
                        <select class="form-control" id="filter_late_status">
                            <option value="">Semua Status</option>
                            <option value="late">Terlambat</option>
                            <option value="on_time">Tepat Waktu</option>
                        </select>
                    </div>
                    <div class="col-md-3 align-self-end mt-2">
                        <button type="button" class="btn btn-info btn-block" id="filterBtn">Filter</button>
                    </div>
                    <div class="col-md-3 align-self-end mt-2">
                        <button type="button" class="btn btn-secondary btn-block" id="resetFilterBtn">Reset Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="attendance-table" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Cabang</th>
                                <th>Tanggal</th>
                                <th>Absen Masuk</th>
                                <th>Mulai Istirahat</th>
                                <th>Selesai Istirahat</th>
                                <th>Absen Pulang</th>
                                <th>Status</th>
                                <th>Keterlambatan</th>
                                <th>Lokasi</th>
                                <th>Tipe</th>
                                <th width="150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> {{-- Penutup div.card-body yang hilang --}}
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
                <form id="attendanceForm" name="attendanceForm" class="form-horizontal">
                   <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="form-group">
                        <label for="user_id" class="col-sm-12 control-label">Karyawan</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text user_id_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="branch_id" class="col-sm-12 control-label">Cabang</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="branch_id" name="branch_id">
                                <option value="">Pilih Cabang (Opsional)</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text branch_id_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="timestamp" class="col-sm-12 control-label">Tanggal & Waktu Absensi</label>
                        <div class="col-sm-12">
                            <div class="input-group date" id="attendance_timestamp_picker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#attendance_timestamp_picker" id="timestamp" name="timestamp" required/>
                                <div class="input-group-append" data-target="#attendance_timestamp_picker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i> <i class="far fa-clock ml-2"></i></div>
                                </div>
                            </div>
                            <span class="text-danger error-text timestamp_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="col-sm-12 control-label">Tipe Absensi</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="check_in">Masuk (Check-in)</option>
                                <option value="check_out">Pulang (Check-out)</option>
                            </select>
                            <span class="text-danger error-text type_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="col-sm-12 control-label">Status Absensi</label>
                        <div class="col-sm-12">
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="on_time">Tepat Waktu</option>
                                <option value="late">Terlambat</option>
                                <option value="early_out">Pulang Cepat</option>
                                <option value="no_check_out">Tidak Check-out</option>
                                <option value="absent">Tidak Hadir (Manual)</option>
                            </select>
                            <span class="text-danger error-text status_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="latitude" class="col-sm-12 control-label">Latitude (Opsional)</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Misal: -6.1234567">
                            <span class="text-danger error-text latitude_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="longitude" class="col-sm-12 control-label">Longitude (Opsional)</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Misal: 106.1234567">
                            <span class="text-danger error-text longitude_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="late_minutes" class="col-sm-12 control-label">Keterlambatan (Menit)</label>
                        <div class="col-sm-12">
                            <input type="number" class="form-control" id="late_minutes" name="late_minutes" placeholder="0" min="0">
                            <small class="form-text text-muted">Jumlah menit keterlambatan (0 jika tepat waktu)</small>
                            <span class="text-danger error-text late_minutes_error"></span>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan Absensi</button>
                    </div>
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
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-3.0.1/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    {{-- Moment JS (diperlukan oleh Tempus Dominus) --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/moment/moment.min.js') }}"></script>
    {{-- Tempus Dominus (Date/Time Picker) --}}
    <script src="{{ asset('AdminLTE-3.0.1/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
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

          // --- Tambahan: Isi otomatis filter tanggal dari query string ---
          function getQueryParam(name) {
              const url = new URL(window.location.href);
              return url.searchParams.get(name);
          }
          var startDate = getQueryParam('start_date');
          var endDate = getQueryParam('end_date');
          if (startDate) {
              $('#filter_start_date').val(startDate);
              $('#filter_start_date_picker').datetimepicker('date', moment(startDate, 'YYYY-MM-DD'));
          }
          if (endDate) {
              $('#filter_end_date').val(endDate);
              $('#filter_end_date_picker').datetimepicker('date', moment(endDate, 'YYYY-MM-DD'));
          }

          // --- End tambahan ---

          // Inisialisasi Datepicker dan Timepicker untuk Form Modal
          try {
              $('#attendance_timestamp_picker').datetimepicker({
                  format: 'DD/MM/YYYY HH:mm:ss', // Format baru: tanggal dan waktu
                  sideBySide: true, // Tampilkan date dan time picker berdampingan
                  icons: {
                      time: 'fa fa-clock',
                      date: 'fa fa-calendar',
                      up: 'fa fa-arrow-up',
                      down: 'fa fa-arrow-down',
                      previous: 'fa fa-chevron-left',
                      next: 'fa fa-chevron-right',
                      today: 'fa fa-calendar-check',
                      clear: 'fa fa-trash',
                      close: 'fa fa-times'
                  },
                  allowInputToggle: true,
                  ignoreReadonly: true
              });

              // Event handler untuk tombol "Tambah Absensi Manual"
              $('#createNewAttendance').click(function () {
                  $('#attendance_id').val('');
                  $('#attendanceForm').trigger("reset");
                  $('#modelHeading').html("Tambah Absensi Manual");
                  $('#saveBtn').val("create-attendance");
                  $('#saveBtn').html('Simpan Absensi');
                  $('#ajaxModel').modal('show');
                  
                  // Clear any previous errors
                  $('.error-text').text('');
                  
                  // Set default timestamp to current time
                  var now = moment().format('DD/MM/YYYY HH:mm:ss');
                  $('#timestamp').val(now);
              });

              // Inisialisasi Datepicker untuk Filter
              $('#filter_start_date_picker').datetimepicker({
                  format: 'DD/MM/YYYY',
                  icons: {
                      time: 'fa fa-clock',
                      date: 'fa fa-calendar',
                      up: 'fa fa-arrow-up',
                      down: 'fa fa-arrow-down',
                      previous: 'fa fa-chevron-left',
                      next: 'fa fa-chevron-right',
                      today: 'fa fa-calendar-check',
                      clear: 'fa fa-trash',
                      close: 'fa fa-times'
                  },
                  allowInputToggle: true,
                  ignoreReadonly: true
              });
              $('#filter_end_date_picker').datetimepicker({
                  format: 'DD/MM/YYYY',
                  icons: {
                      time: 'fa fa-clock',
                      date: 'fa fa-calendar',
                      up: 'fa fa-arrow-up',
                      down: 'fa fa-arrow-down',
                      previous: 'fa fa-chevron-left',
                      next: 'fa fa-chevron-right',
                      today: 'fa fa-calendar-check',
                      clear: 'fa fa-trash',
                      close: 'fa fa-times'
                  },
                  allowInputToggle: true,
                  ignoreReadonly: true
              });
          } catch (e) {
              console.error('Datepicker initialization error:', e);
          }

          // Inisialisasi Datatables
          var table = $('#attendance-table').DataTable({
              processing: true,
              serverSide: true,
              responsive: false, // Disable responsive
              ajax: {
                  url: "{{ route('admin.attendances.index') }}",
                  data: function (d) {
                      d.start_date = $('#filter_start_date').val();
                      d.end_date = $('#filter_end_date').val();
                      d.user_id = $('#filter_user_id').val();
                      d.branch_id = $('#filter_branch_id').val(); // Tambahkan filter cabang
                      d.late_status = $('#filter_late_status').val(); // Tambahkan filter status keterlambatan
                  }
              },
              columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                  {data: 'employee_name', name: 'user.name'},
                  {data: 'branch_name', name: 'branch.name'}, // Nama cabang
                  {data: 'date', name: 'attendances.created_at'}, // Tanggal dari timestamp (sudah dd/mm/yyyy dari backend)
                  {data: 'time', name: 'attendances.created_at'}, // Waktu dari timestamp
                  {data: 'break_start_time', name: 'break_start', orderable: false, searchable: false},
                  {data: 'break_end_time', name: 'break_end', orderable: false, searchable: false},
                  {data: 'check_out_time', name: 'check_out', orderable: false, searchable: false}, // Kolom absen pulang
                  {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                  {data: 'late_badge', name: 'late', orderable: false, searchable: false}, // Kolom keterlambatan
                  {data: 'location', name: 'location', orderable: false, searchable: false},
                  {data: 'type_badge', name: 'type', orderable: false, searchable: false},
                  {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              order: [[3, 'desc'], [4, 'desc']] // Order by date then time (terbaru di atas)
          });

          // Event handler untuk tombol "Filter"
          $('#filterBtn').click(function () {
              // Konversi tanggal filter ke format Y-m-d sebelum reload DataTables
              var start = $('#filter_start_date').val();
              var end = $('#filter_end_date').val();
              if (start) {
                  var parts = start.split('/');
                  if (parts.length === 3) {
                      $('#filter_start_date').val(parts[2] + '-' + parts[1] + '-' + parts[0]);
                  }
              }
              if (end) {
                  var parts = end.split('/');
                  if (parts.length === 3) {
                      $('#filter_end_date').val(parts[2] + '-' + parts[1] + '-' + parts[0]);
                  }
              }
              table.draw();
              // Setelah draw, kembalikan ke format dd/mm/yyyy agar user tidak bingung
              if (start && parts.length === 3) {
                  $('#filter_start_date').val(parts[0] + '/' + parts[1] + '/' + parts[2]);
              }
              if (end && parts.length === 3) {
                  $('#filter_end_date').val(parts[0] + '/' + parts[1] + '/' + parts[2]);
              }
          });

          // Event handler untuk tombol "Reset Filter"
          $('#resetFilterBtn').click(function () {
              $('#filter_start_date').val('');
              $('#filter_end_date').val('');
              $('#filter_user_id').val('').trigger('change'); // Reset dropdown karyawan
              $('#filter_branch_id').val('').trigger('change'); // Reset dropdown cabang
              $('#filter_late_status').val('').trigger('change'); // Reset dropdown status keterlambatan
              table.draw();
          });


          // Event handler untuk tombol "Tambah Absensi Manual"
          $('#createNewAttendance').click(function () {
              $('#saveBtn').val("create-attendance");
              $('#attendance_id').val('');
              $('#attendanceForm').trigger("reset"); // Reset semua input form
              $('#modelHeading').html("Tambah Catatan Absensi Manual");
              $('.error-text').html(''); // Bersihkan pesan error
              $('#user_id').val('').trigger('change'); // Reset dropdown karyawan
              $('#branch_id').val('').trigger('change'); // Reset dropdown cabang
              $('#ajaxModel').modal('show');
          });

          // Event handler untuk tombol "Edit"
          $('body').on('click', '.editAttendance', function () {
              var attendance_id = $(this).data('id');
              // Perbaikan: Gunakan route('admin.attendances.edit') atau route('admin.attendances.show')
              // Karena kita pakai method 'show' di controller untuk ambil data edit.
              $.get("{{ route('admin.attendances.show', ':id') }}".replace(':id', attendance_id), function (data) {
                  $('#modelHeading').html("Edit Catatan Absensi");
                  $('#saveBtn').val("edit-attendance");
                  $('#ajaxModel').modal('show');
                  $('#attendance_id').val(data.id);
                  $('#user_id').val(data.user_id).trigger('change');
                  $('#branch_id').val(data.branch_id).trigger('change');
                  $('#timestamp').val(moment(data.timestamp).format('YYYY-MM-DD HH:mm:ss')); // Format timestamp
                  // Ubah ke format tampilan dd/mm/yyyy HH:mm:ss jika ingin tampil di input
                  // $('#timestamp').val(moment(data.timestamp).format('DD/MM/YYYY HH:mm:ss'));
                  $('#type').val(data.type).trigger('change');
                  $('#status').val(data.status).trigger('change');
                  $('#latitude').val(data.latitude);
                  $('#longitude').val(data.longitude);
                  $('#late_minutes').val(data.late_minutes); // Set late_minutes
                  $('.error-text').html(''); // Bersihkan pesan error
              }).fail(function(data) {
                  Swal.fire('Error!', 'Gagal mengambil data catatan absensi.', 'error');
              });
          });

          // Event handler untuk submit form (Tambah/Edit)
          $('#saveBtn').click(function (e) {
              e.preventDefault();
              $(this).html('Mengirim..');

              var formData = new FormData($('#attendanceForm')[0]);
              var saveBtn = $('#saveBtn').val();
              var attendance_id = $('#attendance_id').val();
              var url;
              var type;

              if (saveBtn === "edit-attendance" && attendance_id) {
                  url = "{{ route('admin.attendances.update', ':id') }}".replace(':id', attendance_id);
                  type = 'POST'; // Menggunakan POST untuk PUT override
                  formData.append('_method', 'PUT'); // Tambahkan method override untuk PUT
              } else {
                  url = "{{ route('admin.attendances.store') }}";
                  type = 'POST';
              }

              $.ajax({
                  data: formData,
                  url: url,
                  type: type,
                  contentType: false,
                  processData: false,
                  success: function (data) {
                      $('#attendanceForm').trigger("reset");
                      $('#ajaxModel').modal('hide');
                      table.draw(); // Refresh Datatables
                      $('#saveBtn').html('Simpan Absensi');
                      $('#saveBtn').val('create-attendance');
                      Swal.fire('Berhasil!', data.success, 'success');
                  },
                  error: function (data) {
                      console.log('Error:', data);
                      $('#saveBtn').html('Simpan Absensi');
                      // Tampilkan pesan error validasi
                      if(data.responseJSON && data.responseJSON.errors) {
                          $.each(data.responseJSON.errors, function(key, val){
                              $('.'+key+'_error').text(val[0]);
                          });
                      }
                      Swal.fire('Error!', data.responseJSON.error || 'Terjadi kesalahan. Periksa input Anda.', 'error');
                  }
              });
          });

          // Event handler untuk tombol "Edit"
          $('body').on('click', '.editAttendance', function () {
              var attendance_id = $(this).data('id');
              $.get("{{ route('admin.attendances.show', ':id') }}".replace(':id', attendance_id), function (data) {
                  $('#modelHeading').html("Edit Absensi");
                  $('#saveBtn').val("edit-attendance");
                  $('#saveBtn').html('Update Absensi');
                  $('#ajaxModel').modal('show');
                  $('#attendance_id').val(data.id);
                  $('#user_id').val(data.user_id);
                  $('#branch_id').val(data.branch_id);
                  $('#timestamp').val(data.timestamp);
                  $('#type').val(data.type);
                  $('#status').val(data.status);
                  $('#latitude').val(data.latitude);
                  $('#longitude').val(data.longitude);
                  $('#late_minutes').val(data.late_minutes);
                  
                  // Clear any previous errors
                  $('.error-text').text('');
              }).fail(function(xhr) {
                  Swal.fire('Error!', 'Tidak dapat memuat data absensi.', 'error');
              });
          });

          // Event handler untuk tombol "Delete"
          $('body').on('click', '.deleteAttendance', function () {
              var attendance_id = $(this).data("id");
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
                          type: "POST", // Menggunakan POST untuk DELETE override
                          url: "{{ route('admin.attendances.destroy', ':id') }}".replace(':id', attendance_id),
                          data: {
                              _method: 'DELETE', // Method override untuk DELETE
                          },
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
    </script>
@endpush