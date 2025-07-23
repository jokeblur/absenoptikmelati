<?php
namespace App\Http\Controllers;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Admin Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\LateEmployeeController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\WorkScheduleController;

// Employee Controllers

use App\Http\Controllers\EmployeeController as DashboardEmployeeController;
use App\Http\Controllers\Employee\AttendanceController as EmployeeAttendanceController;
use App\Http\Controllers\Employee\LeavePermissionController;


Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    // Arahkan user ke dashboard yang sesuai setelah login berdasarkan role
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'karyawan') {
            return redirect()->route('employee.dashboard');
        }
    }
    return view('dashboard'); // Default fallback jika belum login atau role tidak ditemukan
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Rute untuk Manajemen Admin
    Route::get('admins', [\App\Http\Controllers\Admin\AdminController::class, 'indexAdmins'])->name('admins.index');
    Route::get('admins/create', [\App\Http\Controllers\Admin\AdminController::class, 'createAdmin'])->name('admins.create');
    Route::post('admins', [\App\Http\Controllers\Admin\AdminController::class, 'storeAdmin'])->name('admins.store');
    Route::get('admins/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editAdmin'])->name('admins.edit');
    Route::put('admins/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateAdmin'])->name('admins.update');
    Route::delete('admins/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'destroyAdmin'])->name('admins.destroy');

    // Rute untuk Profil Admin
    Route::get('profile', [\App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('profile');
    Route::post('profile/update', [\App\Http\Controllers\Admin\AdminController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/password', [\App\Http\Controllers\Admin\AdminController::class, 'updatePassword'])->name('profile.password');

    // Rute untuk Manajemen Karyawan
    Route::get('employees/{user}/schedule-details', [AdminEmployeeController::class, 'getWorkScheduleDetails'])->name('employees.schedule-details');
    Route::resource('employees', AdminEmployeeController::class); // Ini akan membuat CRUD routes
    Route::resource('work-schedules', WorkScheduleController::class)
        ->parameters(['work-schedules' => 'user'])
        ->except(['create', 'store', 'show', 'destroy']);
    // Contoh: /admin/employees, /admin/employees/create, /admin/employees/{id}, etc.
    

    // Rute untuk Persetujuan Cuti
    Route::get('leaves', [AdminLeaveController::class, 'index'])->name('leaves.index');
    Route::get('leaves/simple', [AdminLeaveController::class, 'simple'])->name('leaves.simple');
    Route::get('leaves/test', function() { return view('admin.leaves.test'); })->name('leaves.test');
    Route::post('leaves/{leave}/approve', [AdminLeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('leaves/{leave}/reject', [AdminLeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('leaves/{leave}', [\App\Http\Controllers\Admin\LeaveController::class, 'show'])->name('leaves.show');

    // Rute untuk Persetujuan Izin
    Route::get('permissions', [AdminPermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/{permission}/approve', [AdminPermissionController::class, 'approve'])->name('permissions.approve');
    Route::post('permissions/{permission}/reject', [AdminPermissionController::class, 'reject'])->name('permissions.reject');
    Route::get('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'show'])->name('permissions.show');

    // Rute untuk Management Absensi
     Route::resource('attendances', AdminAttendanceController::class);
    Route::get('attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
    
    // Rute tambahan untuk Management Absensi
    Route::get('attendance/report', [AdminAttendanceController::class, 'report'])->name('attendance.report');
    Route::get('attendance/settings', [AdminAttendanceController::class, 'settings'])->name('attendance.settings');
    Route::post('attendance/settings/save', [AdminAttendanceController::class, 'saveSettings'])->name('attendance.settings.save');
    Route::get('attendance/export', [AdminAttendanceController::class, 'export'])->name('attendance.export');
    Route::post('attendance/export-data', [AdminAttendanceController::class, 'exportData'])->name('attendance.export_data');
    Route::get('attendance/preview', [AdminAttendanceController::class, 'preview'])->name('attendance.preview');
    Route::post('attendance/export', [AdminAttendanceController::class, 'exportData'])->name('attendance.export');
    
    Route::get('late-employees', [LateEmployeeController::class, 'index'])->name('late_employees.index');
    // Anda mungkin perlu rute API untuk data Datatables di sini juga
    Route::get('api/late-employees', [LateEmployeeController::class, 'getLateEmployeesData'])->name('api.late_employees.data');

    // Notifikasi Admin
    Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/latest', [\App\Http\Controllers\Admin\NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');

});


Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [DashboardEmployeeController::class, 'index'])->name('dashboard');

    // Profil Karyawan
    Route::get('/profile', [DashboardEmployeeController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [DashboardEmployeeController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [DashboardEmployeeController::class, 'updatePassword'])->name('profile.password');

    // Absensi
    Route::post('/absensi/masuk', [EmployeeAttendanceController::class, 'clockIn'])->name('absensi.clock_in');
    Route::post('/absensi/pulang', [EmployeeAttendanceController::class, 'clockOut'])->name('absensi.clock_out');
    Route::post('/absensi/istirahat/mulai', [EmployeeAttendanceController::class, 'breakStart'])->name('absensi.break_start');
    Route::post('/absensi/istirahat/selesai', [EmployeeAttendanceController::class, 'breakEnd'])->name('absensi.break_end');

    // Pengajuan Cuti & Izin
    Route::get('/pengajuan', [LeavePermissionController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan/cuti', [LeavePermissionController::class, 'storeLeave'])->name('pengajuan.store_leave');
    Route::post('/pengajuan/izin', [LeavePermissionController::class, 'storePermission'])->name('pengajuan.store_permission');

    // Riwayat
    Route::get('/riwayat', [DashboardEmployeeController::class, 'history'])->name('history');
    Route::post('/employee/save-subscription', [\App\Http\Controllers\Employee\NotificationController::class, 'saveSubscription']);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
