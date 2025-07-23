<?php
namespace App\Helpers;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationHelper
{
    public static function sendPushNotification($subscription, $title, $body, $url = null)
    {
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT', 'mailto:admin@optikmelati.com'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];
        $webPush = new WebPush($auth);
        $payload = [
            'title' => $title,
            'body' => $body,
        ];
        if ($url) {
            $payload['url'] = $url;
        }
        $webPush->sendNotification(
            Subscription::create($subscription),
            json_encode($payload)
        );
        foreach ($webPush->flush() as $report) {
            // Optional: log or handle delivery report
        }
    }
} 