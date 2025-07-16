@if($data->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Cabang</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                    <td>{{ $attendance->branch->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->timestamp)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->timestamp)->format('H:i:s') }}</td>
                    <td>
                        @if($attendance->type === 'check_in')
                            <span class="badge badge-primary">Check-in</span>
                        @else
                            <span class="badge badge-secondary">Check-out</span>
                        @endif
                    </td>
                    <td>
                        @switch($attendance->status)
                            @case('on_time')
                                <span class="badge badge-success">Tepat Waktu</span>
                                @break
                            @case('late')
                                <span class="badge badge-warning">Terlambat</span>
                                @break
                            @case('early_out')
                                <span class="badge badge-info">Pulang Cepat</span>
                                @break
                            @case('no_check_out')
                                <span class="badge badge-danger">Tidak Check-out</span>
                                @break
                            @case('absent')
                                <span class="badge badge-danger">Tidak Hadir</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ ucfirst($attendance->status) }}</span>
                        @endswitch
                    </td>
                    <td>
                        @if($attendance->latitude && $attendance->longitude)
                            {{ $attendance->latitude }}, {{ $attendance->longitude }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <small class="text-muted">
            Menampilkan {{ $data->count() }} data terbaru. 
            Total data yang akan di-export: {{ $data->count() > 50 ? 'Lebih dari 50' : $data->count() }}
        </small>
    </div>
@else
    <div class="text-center text-muted">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <p>Tidak ada data yang ditemukan untuk periode yang dipilih.</p>
    </div>
@endif 