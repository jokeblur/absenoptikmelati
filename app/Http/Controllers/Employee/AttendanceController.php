<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance; // Pastikan ini ada
use App\Models\User; // Pastikan ini di-use
use App\Models\Branch; // Pastikan ini di-use jika Anda menggunakan model Branch
use App\Models\WorkSchedule; // Pastikan ini di-use
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\PushNotificationHelper;

class AttendanceController extends Controller
{

     public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Ambil record absensi hari ini untuk user yang sedang login
        $attendanceToday = Attendance::where('user_id', $user->id)
    ->whereDate('date', $today)
    ->first();

        // Inisialisasi variabel untuk menghindari error di view
        $hasClockedIn = false;
        $hasClockedOut = false;

        if ($attendanceToday) {
            $hasClockedIn = !is_null($attendanceToday->check_in);
            $hasClockedOut = !is_null($attendanceToday->check_out);
        }

        return view('employee.dashboard', compact('hasClockedIn', 'hasClockedOut', 'attendanceToday'));
    }
    /**
     * Menghitung jarak antara dua koordinat Latitude dan Longitude menggunakan rumus Haversine.
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam meter
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance; // Jarak dalam meter
    }


    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude_in' => ['required', 'numeric'],
            'longitude_in' => ['required', 'numeric'],
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $currentTime = Carbon::now();
        $dayOfWeek = strtolower($today->format('l'));
        $schedule = WorkSchedule::where('user_id', $user->id)->where('day', $dayOfWeek)->first();

        if (!$schedule || $schedule->is_holiday || !$schedule->clock_in) {
            return response()->json(['message' => 'Tidak ada jadwal kerja aktif untuk hari ini atau hari ini adalah hari libur.'], 400);
        }

        $clockInTime = Carbon::parse($schedule->clock_in);
        $allowedEarlyIn = $clockInTime->copy()->subMinutes(30); // Boleh absen 30 menit lebih awal
        $lateTolerance = $clockInTime->copy()->addMinutes(5); // Toleransi keterlambatan 5 menit

        if ($currentTime->lt($allowedEarlyIn)) {
            return response()->json(['message' => 'Anda belum bisa absen masuk. Anda dapat absen mulai jam ' . $allowedEarlyIn->format('H:i') . '.'], 400);
        }

        $statusIn = 'hadir';
        $lateMinutes = 0;
        if ($currentTime->gt($lateTolerance)) {
                $statusIn = 'terlambat';
            $lateMinutes = $clockInTime->diffInMinutes($currentTime);
        }

        $existingClockIn = Attendance::where('user_id', $user->id)
                                        ->whereDate('date', $today->toDateString())
                                        ->first();

        if ($existingClockIn && $existingClockIn->check_in) {
            return response()->json(['message' => 'Anda sudah absen masuk hari ini.'], 400);
        }

        $branch = $user->branch;
        if (!$branch || is_null($branch->latitude) || is_null($branch->longitude)) {
            return response()->json(['message' => 'Koordinat kantor cabang belum diatur oleh admin.'], 400);
        }

        $distance = $this->calculateDistance($request->latitude_in, $request->longitude_in, $branch->latitude, $branch->longitude);
        $maxAllowedDistance = $branch->attendance_radius ?? 100;

        if ($distance > $maxAllowedDistance) {
            return response()->json(['message' => 'Lokasi Anda terlalu jauh dari kantor cabang. (Jarak: ' . round($distance) . 'm)'], 400);
        }

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today->toDateString()],
            [
                'check_in' => $currentTime,
                'latitude_in' => $request->latitude_in,
                'longitude_in' => $request->longitude_in,
                'type' => 'normal',
                'status_in' => $statusIn,
                'late_minutes' => $lateMinutes,
            ]
        );

        if ($user->push_subscription) {
            PushNotificationHelper::sendPushNotification(
                $user->push_subscription,
                'Absen Masuk Berhasil',
                'Selamat bekerja, jangan lupa semangat!'
            );
            if (now()->isSaturday()) {
                PushNotificationHelper::sendPushNotification(
                    $user->push_subscription,
                    'Hore!',
                    'Besok libur!'
                );
            }
        }

        return response()->json([
            'message' => 'Absen masuk berhasil!',
            'status_in' => $statusIn,
            'menit_terlambat' => $lateMinutes
        ], 200);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude_out' => ['required', 'numeric'],
            'longitude_out' => ['required', 'numeric'],
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $currentTime = Carbon::now();
        $dayOfWeek = strtolower($today->format('l'));
        $schedule = WorkSchedule::where('user_id', $user->id)->where('day', $dayOfWeek)->first();

        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today->toDateString())
                                ->whereNotNull('check_in')
                                ->whereNull('check_out')
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum absen masuk atau sudah absen pulang hari ini.'], 400);
        }

        if ($schedule && !$schedule->is_holiday && $schedule->clock_out) {
            $clockOutTime = Carbon::parse($schedule->clock_out);
            $allowedEarlyOut = $clockOutTime->copy()->subMinutes(15); // Boleh absen 15 menit lebih awal

            if ($currentTime->lt($allowedEarlyOut)) {
                return response()->json(['message' => 'Anda belum bisa absen pulang. Anda dapat absen pulang mulai jam ' . $allowedEarlyOut->format('H:i') . '.'], 400);
            }
        }
        
        $branch = $user->branch;
        if (!$branch || is_null($branch->latitude) || is_null($branch->longitude)) {
            return response()->json(['message' => 'Koordinat kantor cabang belum diatur oleh admin.'], 400);
        }
        
        $distance = $this->calculateDistance($request->latitude_out, $request->longitude_out, $branch->latitude, $branch->longitude);
        $maxAllowedDistance = $branch->attendance_radius ?? 100;

        if ($distance > $maxAllowedDistance) {
            return response()->json(['message' => 'Lokasi Anda terlalu jauh dari kantor cabang saat absen pulang. (Jarak: ' . round($distance) . 'm)'], 400);
        }

        $statusOut = 'pulang';
        if ($schedule && $schedule->clock_out) {
            $overtimeThreshold = Carbon::parse($schedule->clock_out)->addMinutes(30);
            if ($currentTime->gt($overtimeThreshold)) {
                $statusOut = 'lembur';
            }
        }

        $attendance->update([
            'check_out' => $currentTime,
            'latitude_out' => $request->latitude_out,
            'longitude_out' => $request->longitude_out,
            'status_out' => $statusOut,
        ]);

        if ($user->push_subscription) {
            PushNotificationHelper::sendPushNotification(
                $user->push_subscription,
                'Absen Pulang Berhasil',
                'Selamat istirahat, jangan lupa istirahat!'
            );
        }

        return response()->json(['message' => 'Absen pulang berhasil!'], 200);
    }

    public function breakStart(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentTime = Carbon::now();

        // Ambil jadwal kerja hari ini
        $dayOfWeek = strtolower($today->format('l')); // 'monday', 'tuesday', etc.
        $schedule = WorkSchedule::where('user_id', $user->id)->where('day', $dayOfWeek)->first();

        // Cek jika ada jadwal dan bukan hari libur
        if ($schedule && !$schedule->is_holiday && $schedule->break_start_time) {
            $breakStartTime = Carbon::parse($schedule->break_start_time);
            
            // TIDAK ADA TOLERANSI WAKTU - karyawan harus absen tepat pada jam istirahat atau setelahnya
            if ($currentTime->lt($breakStartTime)) {
                return response()->json(['message' => 'Anda belum bisa memulai istirahat. Jadwal istirahat Anda adalah jam ' . $breakStartTime->format('H:i') . '. Silakan tunggu hingga jam tersebut.'], 400);
            }
        }

        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today->toDateString())
                                ->whereNotNull('check_in')
                                ->whereNull('check_out')
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda harus absen masuk terlebih dahulu.'], 400);
        }

        if ($attendance->break_start) {
            return response()->json(['message' => 'Anda sudah memulai istirahat.'], 400);
        }

        $attendance->update(['break_start' => $currentTime]);

        if ($user->push_subscription) {
            PushNotificationHelper::sendPushNotification(
                $user->push_subscription,
                'Waktunya Istirahat!',
                'Selamat istirahat, jangan lupa istirahat!'
            );
        }

        return response()->json(['message' => 'Istirahat dimulai.']);
    }

    public function breakEnd(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $currentTime = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today->toDateString())
                                ->whereNotNull('break_start')
                                ->whereNull('break_end')
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda harus memulai istirahat terlebih dahulu.'], 400);
        }

        // Hitung keterlambatan - TETAP ADA karena untuk tracking keterlambatan kembali dari istirahat
        $lateMinutes = 0;
        $dayOfWeek = strtolower($today->format('l'));
        $schedule = WorkSchedule::where('user_id', $user->id)->where('day', $dayOfWeek)->first();

        if ($schedule && !$schedule->is_holiday && $schedule->break_end_time) {
            $breakEndTime = Carbon::parse($schedule->break_end_time);
            
            if ($currentTime->gt($breakEndTime)) {
                $lateMinutes = $currentTime->diffInMinutes($breakEndTime);
            }
        }

        $attendance->update([
            'break_end' => $currentTime,
            'break_late_minutes' => $lateMinutes
        ]);
        
        $responseMessage = 'Selesai istirahat.';
        if ($lateMinutes > 0) {
            $responseMessage .= " Anda terlambat kembali dari istirahat selama $lateMinutes menit.";
        }

        return response()->json(['message' => $responseMessage, 'late_minutes' => $lateMinutes]);
    }
}