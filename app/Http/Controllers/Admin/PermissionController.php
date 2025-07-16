<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PermissionController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::with('user')->select('permissions.*')->orderBy('created_at', 'desc'); // Ambil semua kolom permission dan user terkait, urutkan terbaru
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function(Permission $permission) {
                    return $permission->user->name ?? 'N/A'; // Nama karyawan yang mengajukan
                })
                ->addColumn('employee_email', function(Permission $permission) {
                    return $permission->user->email ?? 'N/A'; // Email karyawan
                })
                ->addColumn('permission_date', function(Permission $permission) {
                    return Carbon::parse($permission->permission_date)->format('d/m/Y');
                })
                ->addColumn('status_badge', function(Permission $permission) {
                    // Tampilkan status dengan badge AdminLTE
                    $badgeClass = '';
                    switch ($permission->status) {
                        case 'pending': $badgeClass = 'badge-warning'; break;
                        case 'approved': $badgeClass = 'badge-success'; break;
                        case 'rejected': $badgeClass = 'badge-danger'; break;
                        default: $badgeClass = 'badge-secondary'; break;
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($permission->status) . '</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="View" class="btn btn-info btn-sm viewPermission">Detail</a>';

                    // Hanya tampilkan tombol approve/reject jika status pending
                    if ($row->status === 'pending') {
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Approve" class="btn btn-success btn-sm approvePermission">Setujui</a>';
                        $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Reject" class="btn btn-danger btn-sm rejectPermission">Tolak</a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action']) // Pastikan ini di-render sebagai HTML
                ->make(true);
        }

        return view('admin.permissions.index');
    }

    /**
     * Menampilkan detail pengajuan izin (digunakan untuk modal detail).
     */
    public function show($id)
    {
        $permission = Permission::with('user')->find($id);
        if (!$permission) {
            return response()->json(['error' => 'Pengajuan izin tidak ditemukan.'], 404);
        }
        
        // Format date to d/m/Y (Indonesia)
        $permission->permission_date = Carbon::parse($permission->permission_date)->format('d/m/Y');
        
        return response()->json($permission);
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Request $request, Permission $permission)
    {
        if ($permission->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah tidak pending.'], 400);
        }

        $permission->status = 'approved';
        $permission->admin_notes = $request->admin_notes; // Simpan catatan admin jika ada
        $permission->save();

        return response()->json(['success' => 'Pengajuan izin berhasil disetujui.']);
    }

    /**
     * Menolak pengajuan izin.
     */
    public function reject(Request $request, Permission $permission)
    {
        if ($permission->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah tidak pending.'], 400);
        }

        $request->validate([
            'admin_notes' => 'required|string|max:255', // Alasan penolakan wajib
        ]);

        $permission->status = 'rejected';
        $permission->admin_notes = $request->admin_notes; // Simpan alasan penolakan
        $permission->save();

        return response()->json(['success' => 'Pengajuan izin berhasil ditolak.']);
    }
}