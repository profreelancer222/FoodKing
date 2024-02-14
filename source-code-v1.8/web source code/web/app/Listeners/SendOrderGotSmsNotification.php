<?php

namespace App\Listeners;


use App\Events\SendOrderGotSms;
use App\Services\OrderGotSmsNotificationBuilder;
use Illuminate\Support\Facades\Log;

class SendOrderGotSmsNotification
{
    public function handle(SendOrderGotSms $event): void
    {
        try {
            $orderGotSmsNotificationBuilderService = new OrderGotSmsNotificationBuilder($event->info['order_id']);
            $orderGotSmsNotificationBuilderService->send();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
