<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Asumsi karyawan adalah user dengan role 'karyawan'
use App\Models\Branch; // Jika ada tabel cabang
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('role', 'karyawan')->with('branch')->select('*'); // Ambil user dengan role karyawan
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('branch_name', function(User $user) {
                    return $user->branch->name ?? '-'; // Tampilkan nama cabang
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editEmployee">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteEmployee">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $branches = Branch::all(); // Untuk dropdown di form tambah/edit
        return view('admin.employees.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'karyawan', // Set role sebagai karyawan
            'branch_id' => $request->branch_id,
        ]);

        return response()->json(['success'=>'Karyawan berhasil ditambahkan.']);
    }

    public function show($id)
    {
        $employee = User::where('role', 'karyawan')->with('branch')->find($id);
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|min:6', // Password bisa kosong jika tidak diubah
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $employee = User::where('role', 'karyawan')->find($id);
        $employee->name = $request->name;
        $employee->email = $request->email;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->branch_id = $request->branch_id;
        $employee->save();

        return response()->json(['success'=>'Data karyawan berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        User::where('role', 'karyawan')->find($id)->delete();
        return response()->json(['success'=>'Karyawan berhasil dihapus.']);
    }

    public function edit($id) // Parameter $id otomatis terisi dari rute {employee}
    {
        $employee = User::where('role', 'karyawan')->find($id);

        if (!$employee) {
            // Jika karyawan tidak ditemukan, kembalikan respon error
            return response()->json(['error' => 'Karyawan tidak ditemukan.'], 404);
        }

        // Kembalikan data karyawan dalam bentuk JSON
        return response()->json($employee);
    }

    public function workSchedule($id)
    {
        $employee = User::findOrFail($id);
        $schedules = $employee->workSchedules()->get();
        return response()->json($schedules);
    }

    public function saveWorkSchedule(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $days = $request->input('days', []);
        $mapDays = [
            'senin','selasa','rabu','kamis','jumat','sabtu','minggu'
        ];
        foreach ($mapDays as $day) {
            $data = $days[$day] ?? null;
            if ($day === 'minggu') {
                // Minggu selalu libur
                $isHoliday = true;
                $clockIn = null;
                $clockOut = null;
            } else {
                $isHoliday = isset($data['is_holiday']) && $data['is_holiday'] ? true : false;
                $clockIn = $data['clock_in'] ?? null;
                $clockOut = $data['clock_out'] ?? null;
            }
            $employee->workSchedules()->updateOrCreate(
                ['day' => $day],
                [
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'is_holiday' => $isHoliday
                ]
            );
        }
        return response()->json(['success' => true]);
    }
}