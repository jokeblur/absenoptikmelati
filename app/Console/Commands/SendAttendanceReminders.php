<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\PushNotificationHelper;
use Carbon\Carbon;

class SendAttendanceReminders extends Command
{
    protected $signature = 'attendance:reminders';
    protected $description = 'Kirim notifikasi pengingat absen masuk, pulang, istirahat, dan selesai istirahat';

    public function handle()
    {
        $now = Carbon::now();
        $reminderMinutes = 10; // Kirim 10 menit sebelum

        $users = User::where('role', 'employee')->whereNotNull('push_subscription')->get();

        foreach ($users as $user) {
            $schedule = $user->workSchedules()->where('day', strtolower($now->format('l')))->first();
            if (!$schedule) continue;

            // Jam masuk
            if ($schedule->start_time) {
                $start = Carbon::createFromFormat('H:i:s', $schedule->start_time)->setDate($now->year, $now->month, $now->day);
                if ($now->diffInMinutes($start, false) === $reminderMinutes) {
                    PushNotificationHelper::sendPushNotification(
                        $user->push_subscription,
                        'Ingat Absen Masuk',
                        'Jangan lupa absen masuk jam ' . $start->format('H:i')
                    );
                }
            }

            // Jam pulang
            if ($schedule->end_time) {
                $end = Carbon::createFromFormat('H:i:s', $schedule->end_time)->setDate($now->year, $now->month, $now->day);
                if ($now->diffInMinutes($end, false) === $reminderMinutes) {
                    PushNotificationHelper::sendPushNotification(
                        $user->push_subscription,
                        'Ingat Absen Pulang',
                        'Jangan lupa absen pulang jam ' . $end->format('H:i')
                    );
                }
            }

            // Jam istirahat
            if ($schedule->break_start) {
                $breakStart = Carbon::createFromFormat('H:i:s', $schedule->break_start)->setDate($now->year, $now->month, $now->day);
                if ($now->diffInMinutes($breakStart, false) === $reminderMinutes) {
                    PushNotificationHelper::sendPushNotification(
                        $user->push_subscription,
                        'Waktunya Istirahat',
                        'Jangan lupa istirahat jam ' . $breakStart->format('H:i')
                    );
                }
            }

            // Selesai istirahat
            if ($schedule->break_end) {
                $breakEnd = Carbon::createFromFormat('H:i:s', $schedule->break_end)->setDate($now->year, $now->month, $now->day);
                if ($now->diffInMinutes($breakEnd, false) === $reminderMinutes) {
                    PushNotificationHelper::sendPushNotification(
                        $user->push_subscription,
                        'Selesai Istirahat',
                        'Jangan lupa kembali bekerja jam ' . $breakEnd->format('H:i')
                    );
                }
            }
        }
    }
} 