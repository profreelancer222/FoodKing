<?php

namespace App\Services;


use App\Enums\Role;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderGotSmsNotificationBuilder
{
    public int $orderId;
    public object $order;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        $this->order = FrontendOrder::find($orderId);
    }

    public function send()
    {
        if (!blank($this->order)) {
            $smsAllAdmins = User::role(Role::ADMIN)->where(['branch_id' => 0])->whereNotNull('phone')->get();
            $smsBranchAdmins = User::role(Role::ADMIN)->where(['branch_id' => $this->order->branch_id])->whereNotNull('phone')->get();
            $smsBranchManagers = User::role(Role::BRANCH_MANAGER)->where(['branch_id' => $this->order->branch_id])->whereNotNull('phone')->get();

            $i = 0;
            $smsArrays = [];
            if (!blank($smsAllAdmins)) {
                foreach ($smsAllAdmins as $smsAllAdmin) {
                    $smsArrays[$i] = [
                        'code' => $smsAllAdmin->country_code,
                        'phone' => $smsAllAdmin->phone,
                    ];
                    $i++;
                }
            }

            if (!blank($smsBranchAdmins)) {
                foreach ($smsBranchAdmins as $smsBranchAdmin) {
                    $smsArrays[$i] = [
                        'code' => $smsBranchAdmin->country_code,
                        'phone' => $smsBranchAdmin->phone,
                    ];
                    $i++;
                }
            }

            if (!blank($smsBranchManagers)) {
                foreach ($smsBranchManagers as $smsBranchManager) {
                    $smsArrays[$i] = [
                        'code' => $smsBranchManager->country_code,
                        'phone' => $smsBranchManager->phone,
                    ];
                    $i++;
                }
            }

            if (count($smsArrays) > 0) {
                try {
                    $notificationAlert = NotificationAlert::where(['language' => 'admin_and_branch_manager_new_order_message'])->first();
                    if ($notificationAlert && $notificationAlert->sms == SwitchBox::ON) {
                        $message = 'Order ID : '.$this->order->order_serial_no . ' '.$notificationAlert->sms_message;
                        foreach ($smsArrays as $smsArray) {
                            $this->sms($smsArray['code'], $smsArray['phone'], $message);
                        }
                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    private function sms($code, $phone, $message): void
    {
        try {
            $smsManagerService = new SmsManagerService();
            $smsService        = new SmsService();
            if ($smsService->gateway() && $smsManagerService->gateway($smsService->gateway())->status()) {
                $smsManagerService->gateway($smsService->gateway())->send($code, $phone, $message);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
