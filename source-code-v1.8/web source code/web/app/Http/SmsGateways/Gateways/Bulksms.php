<?php

namespace App\Http\SmsGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Models\SmsGateway;
use App\Services\SmsAbstract;
use Illuminate\Support\Facades\Log;

class Bulksms extends SmsAbstract
{

    public string $username;
    public string $password;
    public string $baseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->smsGateway = SmsGateway::with('gatewayOptions')->where(['slug' => 'bulksms'])->first();
        if (!blank($this->smsGateway)) {
            $this->smsGatewayOption = $this->smsGateway->gatewayOptions->pluck('value', 'option');
            $this->baseUrl = 'https://api.bulksms.com/v1/messages?auto-unicode=true&longMessageMaxParts=30';
            $this->username = $this->smsGatewayOption['bulksms_username'];
            $this->password = $this->smsGatewayOption['bulksms_password'];
        }
    }

    public function status(): bool
    {
        $paymentGateways = SmsGateway::where(['slug' => 'bulksms', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function send($code, $phone, $message): void
    {
        try {

            $messages = array(
                array('to' => $code . $phone, 'body' => $message),
            );

            $ch = curl_init();
            $headers = array(
                'Content-Type:application/json',
                'Authorization:Basic ' . base64_encode("$this->username:$this->password")
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messages));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
