<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance; // Pastikan ini ada
use App\Models\User; // Pastikan ini di-use
use App\Models\Branch; // Pastikan ini di-use jika Anda menggunakan model Branch
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{

     public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString(); // hasil: '2024-06-08'
        dd($today);

        // Ambil record absensi hari ini untuk user yang sedang login
        $attendanceToday = Attendance::where('user_id', $user->id)
    ->whereDate('date', $today)
    ->first();
    dd($today);
        // Tentukan status absensi
        $hasClockedIn = false;
        $hasClockedOut = false;

        if ($attendanceToday) {
            if ($attendanceToday->check_in !== null) {
                $hasClockedIn = true;
            }
            if ($attendanceToday->check_out !== null) {
                $hasClockedOut = true;
            }
        }


        // Debug log
        Log::info('AttendanceToday', [
            'user_id' => $user->id,
            'today' => $today,
            'attendanceToday' => $attendanceToday
        ]);

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
        // 1. Validasi Input Lokasi Masuk
        $request->validate([
            'latitude_in' => ['required', 'numeric'],
            'longitude_in' => ['required', 'numeric'],
        ], [
            // Pesan validasi kustom dalam Bahasa Indonesia
            'latitude_in.required' => 'Latitude lokasi masuk wajib diisi.',
            'latitude_in.numeric' => 'Latitude lokasi masuk harus berupa angka.',
            'longitude_in.required' => 'Longitude lokasi masuk wajib diisi.',
            'longitude_in.numeric' => 'Longitude lokasi masuk harus berupa angka.',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now();

        $customClockInTime = $user->custom_clock_in_time;
        $statusIn = 'hadir';
        $toleransi = 5; // menit

        $menitTerlambat = 0;

        if ($customClockInTime) {
            $customTime = Carbon::parse($customClockInTime);
            // Set tanggal customTime ke hari ini
            $customTime->setDate($currentTime->year, $currentTime->month, $currentTime->day);
            $batasTerlambat = $customTime->copy()->addMinutes($toleransi);

            if ($currentTime->gt($batasTerlambat)) {
                $statusIn = 'terlambat';
                $menitTerlambat = $batasTerlambat->diffInMinutes($currentTime);
            }
        }

        // Debug log
        Log::info('DEBUG_ABSEN_MASUK', [
            'customClockInTime' => $customClockInTime,
            'customTime' => isset($customTime) ? $customTime->toTimeString() : null,
            'batasTerlambat' => isset($batasTerlambat) ? $batasTerlambat->toTimeString() : null,
            'currentTime' => $currentTime->toTimeString(),
            'statusIn' => $statusIn,
        ]);

        // 2. Cek Status Absensi Hari Ini
        // Cari record absensi untuk user dan tanggal hari ini
        // Yang 'check_in'nya sudah ada tapi 'check_out'nya masih NULL (belum absen pulang)
        $existingClockIn = Attendance::where('user_id', $user->id)
                                        ->whereDate('date', $today)
                                        ->whereNotNull('check_in') // Sudah absen masuk
                                        ->whereNull('check_out')   // Tapi belum absen pulang
                                        ->first();

        if ($existingClockIn) {
            // Jika ditemukan record, berarti user sudah absen masuk dan belum absen pulang
            // Perbaikan: Gunakan array untuk respons JSON
            return response()->json(['message' => 'Anda sudah absen masuk hari ini dan belum absen pulang. Silakan absen pulang terlebih dahulu.'], 400);
        }

        // 3. Validasi Jarak ke Kantor Cabang
        $branch = $user->branch;
        // Tambahkan pengecekan apakah branch ada dan koordinatnya terisi
        if (!$branch || is_null($branch->latitude) || is_null($branch->longitude)) {
            // Perbaikan: Gunakan array untuk respons JSON
            return response()->json(['message' => 'Koordinat kantor cabang belum diatur oleh admin. Mohon hubungi admin.'], 400);
        }

        $userLatitude = $request->input('latitude_in');
        $userLongitude = $request->input('longitude_in');

        $distance = $this->calculateDistance($userLatitude, $userLongitude, $branch->latitude, $branch->longitude);
        // Gunakan radius dari branch, jika tidak ada gunakan config global
        $maxAllowedDistance = $branch->attendance_radius ?? config('app.max_attendance_distance', 100);

        if ($distance > $maxAllowedDistance) {
            // Perbaikan: Gunakan array untuk respons JSON
            return response()->json(['message' => 'Lokasi Anda terlalu jauh dari kantor cabang. (Jarak: ' . round($distance) . 'm)'], 400);
        }

        // 4. Validasi Jam Masuk Kustom
        // $statusIn = 'hadir'; // Status default jika tepat waktu
        // $customClockInTime = $user->custom_clock_in_time;

        // if ($customClockInTime) {
        //     $customTime = Carbon::parse($customClockInTime);
        //     // Ambil hanya waktu dari $currentTime untuk perbandingan (HH:MM:SS)
        //     // $currentHourMinuteSecond = $currentTime->format('H:i:s'); // Tidak digunakan langsung dalam perbandingan
        //     // $customTimeHourMinuteSecond = $customTime->format('H:i:s'); // Tidak digunakan langsung dalam perbandingan

            // Toleransi absen masuk lebih awal (misal: 15 menit)
            // $allowedEarlyIn = $customTime->copy()->subMinutes(15);
            // Toleransi keterlambatan (misal: 15 menit)
            // $allowedLateIn = $customTime->copy()->addMinutes(15);

            // Jika absen terlalu cepat
            // if ($currentTime->lt($allowedEarlyIn)) {
            //     // Perbaikan: Gunakan array untuk respons JSON
            //     return response()->json(['message' => 'Anda belum bisa absen masuk. Jam masuk kustom Anda adalah ' . $customTime->format('H:i') . '. Anda dapat absen mulai ' . $allowedEarlyIn->format('H:i') . '.'], 400);
            // }
            // Jika absen terlambat
            // if ($currentTime->gt($allowedLateIn)) {
            //     $statusIn = 'terlambat'; // Set status jika terlambat
            // }
            // Jika ingin membatasi tidak bisa absen masuk jika sudah terlalu larut (misal 2 jam setelah jam kustom)
            // $veryLateIn = $customTime->copy()->addHours(2);
            // if ($currentTime->gt($veryLateIn)) {
            //     return response()->json(['message' => 'Anda sudah terlalu terlambat untuk absen masuk hari ini.'], 400);
            // }
        // }

        // 5. Buat atau Perbarui Record Absensi (untuk absen masuk)
        // Gunakan firstOrNew untuk mencari record atau membuat instance baru jika tidak ada.
        $attendance = Attendance::firstOrNew(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ]
        );

        // Jika record sudah ada dan check_in tidak null, berarti user sudah absen masuk.
        // Ini seharusnya sudah tertangkap oleh $existingClockIn di awal, tapi sebagai pengaman.
        if ($attendance->exists && $attendance->check_in !== null) {
            return response()->json(['message' => 'Anda sudah absen masuk hari ini.'], 400);
        }

        // Isi atau perbarui detail absen masuk
        $attendance->check_in      = $currentTime;
        $attendance->latitude_in   = $userLatitude;
        $attendance->longitude_in  = $userLongitude;
        $attendance->type          = 'normal'; // Tipe absensi (normal, cuti, dll.)
        $attendance->status_in     = $statusIn; // Status saat masuk (hadir/terlambat)
        $attendance->late_minutes  = $menitTerlambat; // <--- Tambahkan ini
        // Pastikan kolom untuk absen pulang diatur ke null saat absen masuk
        $attendance->check_out     = null;
        $attendance->latitude_out  = null;
        $attendance->longitude_out = null;
        $attendance->status_out    = null;

        $attendance->save(); // Simpan atau perbarui record

        // Perbaikan: Gunakan array untuk respons JSON sukses
        return response()->json([
            'message' => 'Absen masuk berhasil!',
            'status_in' => $statusIn,
            'menit_terlambat' => $menitTerlambat
        ], 200);
    }

    public function clockOut(Request $request)
    {
        // 1. Validasi Input Lokasi Pulang
        $request->validate([
            'latitude_out' => ['required', 'numeric'],
            'longitude_out' => ['required', 'numeric'],
        ], [
            // Pesan validasi kustom dalam Bahasa Indonesia
            'latitude_out.required' => 'Latitude lokasi pulang wajib diisi.',
            'latitude_out.numeric' => 'Latitude lokasi pulang harus berupa angka.',
            'longitude_out.required' => 'Longitude lokasi pulang wajib diisi.',
            'longitude_out.numeric' => 'Longitude lokasi pulang harus berupa angka.',
        ]);

        $user = Auth::user();
        $today = Carbon::today();
        $currentTime = Carbon::now();

        // 2. Cari Record Absensi Hari Ini yang Siap untuk Absen Pulang
        // Cari record yang sudah absen masuk ('check_in' tidak null) tapi belum absen pulang ('check_out' null)
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $today)
                                ->whereNotNull('check_in') // Pastikan sudah ada waktu masuk
                                ->whereNull('check_out')   // Pastikan belum ada waktu pulang
                                ->first();

        // Jika tidak ditemukan record yang sesuai, berarti belum absen masuk atau sudah absen pulang
        if (!$attendance) {
            // Cek apakah sudah absen pulang untuk pesan yang lebih spesifik
            $hasClockedOut = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', $today)
                                    ->whereNotNull('check_out')
                                    ->exists();

            if ($hasClockedOut) {
                // Perbaikan: Gunakan array untuk respons JSON
                return response()->json(['message' => 'Anda sudah absen pulang hari ini.'], 400);
            } else {
                // Perbaikan: Gunakan array untuk respons JSON
                return response()->json(['message' => 'Anda belum absen masuk hari ini. Silakan absen masuk terlebih dahulu.'], 400);
            }
        }

        // 3. Validasi Jarak untuk Absen Pulang
        $branch = $user->branch;
        // Tambahkan pengecekan apakah branch ada dan koordinatnya terisi
        if (!$branch || is_null($branch->latitude) || is_null($branch->longitude)) {
            // Perbaikan: Gunakan array untuk respons JSON
            return response()->json(['message' => 'Koordinat kantor cabang belum diatur oleh admin. Mohon hubungi admin.'], 400);
        }

        $userLatitude = $request->input('latitude_out');
        $userLongitude = $request->input('longitude_out');

        $distance = $this->calculateDistance($userLatitude, $userLongitude, $branch->latitude, $branch->longitude);
        // Gunakan radius dari branch, jika tidak ada gunakan config global
        $maxAllowedDistance = $branch->attendance_radius ?? config('app.max_attendance_distance', 100);

        if ($distance > $maxAllowedDistance) {
            // Perbaikan: Gunakan array untuk respons JSON
            return response()->json(['message' => 'Lokasi Anda terlalu jauh dari kantor cabang saat absen pulang. (Jarak: ' . round($distance) . 'm)'], 400);
        }

        // 4. Validasi Jam Pulang Kustom
        $statusOut = 'pulang'; // Status default jika tepat waktu
        $customClockOutTime = $user->custom_clock_out_time;

        if ($customClockOutTime) {
            $customTime = Carbon::parse($customClockOutTime);
            // Toleransi absen pulang lebih awal (misal: 15 menit)
            $allowedEarlyOutTime = $customTime->copy()->subMinutes(15);
            // Toleransi keterlambatan/lembur (misal: 30 menit)
            $allowedLateOutTime = $customTime->copy()->addMinutes(30);

            // Jika absen terlalu cepat
            if ($currentTime->lt($allowedEarlyOutTime)) {
                // Perbaikan: Gunakan array untuk respons JSON
                return response()->json(['message' => 'Anda belum bisa absen pulang. Jam pulang kustom Anda adalah ' . $customTime->format('H:i') . '. Anda dapat absen pulang mulai ' . $allowedEarlyOutTime->format('H:i') . '.'], 400);
            }
            // Jika absen pulang setelah waktu toleransi lembur
            if ($currentTime->gt($allowedLateOutTime)) {
                $statusOut = 'lembur';
            }
        }

        // 5. Perbarui Record Absensi yang Sudah Ada dengan Data Pulang
        $attendance->update([
            'check_out'     => $currentTime, // Waktu absen pulang
            'latitude_out'  => $userLatitude,
            'longitude_out' => $userLongitude,
            'status_out'    => $statusOut, // Status saat pulang (pulang/lembur)
            // Kolom 'type' umumnya tidak diubah saat absen pulang, biarkan saja seperti saat absen masuk
            // 'type'          => 'normal',
        ]);

        // Perbaikan: Gunakan array untuk respons JSON sukses
        return response()->json(['message' => 'Absen pulang berhasil!'], 200);
    }
}