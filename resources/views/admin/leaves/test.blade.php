@extends('layouts.admin')

@section('page_title', 'Test Leaves')
@section('breadcrumb_item', 'Test')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Test Data Leaves</h3>
            </div>
            <div class="card-body">
                @php
                    $leaves = \App\Models\Leave::with('user')->get();
                @endphp
                
                <p>Total leaves: {{ $leaves->count() }}</p>
                
                @if($leaves->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ $leave->id }}</td>
                                    <td>{{ $leave->user ? $leave->user->name : 'No User' }}</td>
                                    <td>{{ $leave->start_date }}</td>
                                    <td>{{ $leave->end_date }}</td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status == 'pending' ? 'warning' : ($leave->status == 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($leave->reason, 50) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No leaves found</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 