<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Menampilkan daftar pengajuan cuti.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Leave::with('user')->select('leaves.*')->orderBy('created_at', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function(Leave $leave) {
                    return $leave->user->name ?? 'N/A';
                })
                ->addColumn('employee_email', function(Leave $leave) {
                    return $leave->user->email ?? 'N/A';
                })
                ->addColumn('start_date', function(Leave $leave) {
                    return Carbon::parse($leave->start_date)->format('d/m/Y');
                })
                ->addColumn('end_date', function(Leave $leave) {
                    return Carbon::parse($leave->end_date)->format('d/m/Y');
                })
                ->addColumn('duration', function(Leave $leave) {
                    return $leave->duration . ' hari';
                })
                ->addColumn('status_badge', function(Leave $leave) {
                    $badgeClass = '';
                    switch ($leave->status) {
                        case 'pending': $badgeClass = 'badge-warning'; break;
                        case 'approved': $badgeClass = 'badge-success'; break;
                        case 'rejected': $badgeClass = 'badge-danger'; break;
                        default: $badgeClass = 'badge-secondary'; break;
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($leave->status) . '</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="View" class="btn btn-info btn-sm viewLeave">Detail</a>';

                    // Hanya tampilkan tombol approve/reject jika status pending
                    if ($row->status === 'pending') {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Approve" class="btn btn-success btn-sm approveLeave">Setujui</a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Reject" class="btn btn-danger btn-sm rejectLeave">Tolak</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.leaves.index');
    }

    /**
     * Menampilkan daftar pengajuan cuti (versi sederhana tanpa DataTables).
     */
    public function simple()
    {
        $leaves = Leave::with('user')->get();
        return view('admin.leaves.simple', compact('leaves'));
    }

    /**
     * Menampilkan detail pengajuan cuti.
     */
    public function show($id)
    {
        $leave = Leave::with('user')->find($id);
        if (!$leave) {
            return response()->json(['error' => 'Pengajuan cuti tidak ditemukan.'], 404);
        }
        
        // Format dates to d/m/Y (Indonesia)
        $leave->start_date = Carbon::parse($leave->start_date)->format('d/m/Y');
        $leave->end_date = Carbon::parse($leave->end_date)->format('d/m/Y');
        $leave->created_at = Carbon::parse($leave->created_at)->format('d/m/Y H:i:s');
        
        return response()->json($leave);
    }

    /**
     * Menyetujui pengajuan cuti.
     */
    public function approve(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah tidak pending.'], 400);
        }

        $leave->status = 'approved';
        $leave->admin_notes = $request->admin_notes;
        $leave->save();

        return response()->json(['success' => 'Pengajuan cuti berhasil disetujui.']);
    }

    /**
     * Menolak pengajuan cuti.
     */
    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah tidak pending.'], 400);
        }

        $leave->status = 'rejected';
        $leave->admin_notes = $request->admin_notes;
        $leave->save();

        return response()->json(['success' => 'Pengajuan cuti berhasil ditolak.']);
    }
}