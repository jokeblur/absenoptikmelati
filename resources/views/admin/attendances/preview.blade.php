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
                    <td>{{ is_array($attendance) ? $attendance['user']['name'] : $attendance->user->name }}</td>
                    <td>{{ is_array($attendance) ? ($attendance['user']['branch']['name'] ?? 'N/A') : ($attendance->branch->name ?? 'N/A') }}</td>
                    <td>{{ \Carbon\Carbon::parse(is_array($attendance) ? $attendance['date'] : $attendance->timestamp)->format('d/m/Y') }}</td>
                    <td>
                        @if(is_array($attendance))
                            @if($attendance['check_in'])
                                {{ \Carbon\Carbon::parse($attendance['check_in'])->format('H:i:s') }}
                            @elseif($attendance['check_out'])
                                {{ \Carbon\Carbon::parse($attendance['check_out'])->format('H:i:s') }}
                            @else
                                -
                            @endif
                        @else
                            {{ \Carbon\Carbon::parse($attendance->timestamp)->format('H:i:s') }}
                        @endif
                    </td>
                    <td>
                        @if(is_array($attendance))
                            @if($attendance['check_in'] && !$attendance['check_out'])
                                <span class="badge badge-primary">Check-in</span>
                            @elseif($attendance['check_out'])
                                <span class="badge badge-secondary">Check-out</span>
                            @else
                                <span class="badge badge-danger">Tidak Hadir</span>
                            @endif
                        @else
                            @if($attendance->type === 'check_in')
                                <span class="badge badge-primary">Check-in</span>
                            @else
                                <span class="badge badge-secondary">Check-out</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if(is_array($attendance))
                            @if(isset($attendance['is_absent']) && $attendance['is_absent'])
                                <span class="badge badge-danger">Tidak Hadir</span>
                            @else
                                @switch($attendance['status_in'] ?? $attendance['status_out'] ?? 'on_time')
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
                                    @default
                                        <span class="badge badge-secondary">{{ ucfirst($attendance['status_in'] ?? $attendance['status_out'] ?? 'on_time') }}</span>
                                @endswitch
                            @endif
                        @else
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
                        @endif
                    </td>
                    <td>
                        @if(is_array($attendance))
                            @if(($attendance['latitude_in'] ?? $attendance['latitude_out']) && ($attendance['longitude_in'] ?? $attendance['longitude_out']))
                                {{ $attendance['latitude_in'] ?? $attendance['latitude_out'] }}, {{ $attendance['longitude_in'] ?? $attendance['longitude_out'] }}
                            @else
                                -
                            @endif
                        @else
                            @if($attendance->latitude && $attendance->longitude)
                                {{ $attendance->latitude }}, {{ $attendance->longitude }}
                            @else
                                -
                            @endif
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