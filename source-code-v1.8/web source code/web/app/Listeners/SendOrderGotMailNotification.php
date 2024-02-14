<?php

namespace App\Listeners;


use App\Events\SendOrderGotMail;
use App\Services\OrderGotMailNotificationBuilder;
use Illuminate\Support\Facades\Log;

class SendOrderGotMailNotification
{
    public function handle(SendOrderGotMail $event): void
    {
        try{
            $orderMailNotificationBuilderService = new OrderGotMailNotificationBuilder($event->info['order_id']);
            $orderMailNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
