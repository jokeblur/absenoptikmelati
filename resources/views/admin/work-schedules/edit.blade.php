@extends('layouts.admin')

@section('title', 'Edit Jadwal Kerja')

@section('content_header')
    <h1>Edit Jadwal Kerja untuk {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.work-schedules.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Mulai Istirahat</th>
                                <th>Selesai Istirahat</th>
                                <th style="width: 120px;">Shift (P/S)</th>
                                <th class="text-center">Hari Libur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $day => $name)
                                @php
                                    $schedule = $schedules[$day] ?? null;
                                @endphp
                                <tr id="row-{{ $day }}">
                                    <td><strong>{{ $name }}</strong></td>
                                    <td><input type="time" name="{{ $day }}_clock_in" class="form-control" value="{{ $schedule && $schedule->clock_in ? \Carbon\Carbon::parse($schedule->clock_in)->format('H:i') : '08:00' }}"></td>
                                    <td><input type="time" name="{{ $day }}_clock_out" class="form-control" value="{{ $schedule && $schedule->clock_out ? \Carbon\Carbon::parse($schedule->clock_out)->format('H:i') : '17:00' }}"></td>
                                    <td><input type="time" name="{{ $day }}_break_start_time" class="form-control" value="{{ $schedule && $schedule->break_start_time ? \Carbon\Carbon::parse($schedule->break_start_time)->format('H:i') : '12:00' }}"></td>
                                    <td><input type="time" name="{{ $day }}_break_end_time" class="form-control" value="{{ $schedule && $schedule->break_end_time ? \Carbon\Carbon::parse($schedule->break_end_time)->format('H:i') : '13:30' }}"></td>
                                    <td class="text-center">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input shift-checkbox" type="checkbox" data-day="{{ $day }}" data-shift="pagi" id="{{ $day }}_pagi">
                                            <label class="form-check-label" for="{{ $day }}_pagi">P</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input shift-checkbox" type="checkbox" data-day="{{ $day }}" data-shift="siang" id="{{ $day }}_siang">
                                            <label class="form-check-label" for="{{ $day }}_siang">S</label>
                                        </div>
                                    </td>
                                    <td class="text-center"><input type="checkbox" name="{{ $day }}_is_holiday" value="1" {{ ($schedule && $schedule->is_holiday) ? 'checked' : '' }}></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Simpan Jadwal</button>
            </form>
        </div>
    </div>
@stop

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shifts = {
        pagi: {
            clock_in: '09:00',
            clock_out: '18:00',
            break_start_time: '12:00',
            break_end_time: '13:30'
        },
        siang: {
            clock_in: '11:00',
            clock_out: '20:00',
            break_start_time: '15:00',
            break_end_time: '16:30'
        },
        default: {
            clock_in: '08:00',
            clock_out: '17:00',
            break_start_time: '12:00',
            break_end_time: '13:30'
        }
    };

    document.querySelectorAll('.shift-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const shiftType = this.dataset.shift;
            const row = document.getElementById(`row-${day}`);
            
            if (this.checked) {
                // If this checkbox is checked, uncheck the other one in the same row
                const otherCheckbox = row.querySelector(`.shift-checkbox[data-shift="${shiftType === 'pagi' ? 'siang' : 'pagi'}"]`);
                if (otherCheckbox) {
                    otherCheckbox.checked = false;
                }

                // Set the times for the selected shift
                const times = shifts[shiftType];
                if (row) {
                    row.querySelector(`input[name="${day}_clock_in"]`).value = times.clock_in;
                    row.querySelector(`input[name="${day}_clock_out"]`).value = times.clock_out;
                    row.querySelector(`input[name="${day}_break_start_time"]`).value = times.break_start_time;
                    row.querySelector(`input[name="${day}_break_end_time"]`).value = times.break_end_time;
                }
            } else {
                // If this checkbox is unchecked, revert to default times
                const times = shifts.default;
                if (row) {
                    row.querySelector(`input[name="${day}_clock_in"]`).value = times.clock_in;
                    row.querySelector(`input[name="${day}_clock_out"]`).value = times.clock_out;
                    row.querySelector(`input[name="${day}_break_start_time"]`).value = times.break_start_time;
                    row.querySelector(`input[name="${day}_break_end_time"]`).value = times.break_end_time;
                }
            }
        });
    });
});
</script>
@endpush 