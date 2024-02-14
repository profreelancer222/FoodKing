<?php

namespace App\Http\SmsGateways\Gateways;


use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\SmsGateway;
use App\Services\SmsAbstract;
use Illuminate\Support\Facades\Log;

class Bulksmsbd extends SmsAbstract
{

    public string $apiKey;
    public string $senderId;
    public string $baseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->smsGateway = SmsGateway::with('gatewayOptions')->where(['slug' => 'bulksmsbd'])->first();
        if (!blank($this->smsGateway)) {
            $this->smsGatewayOption = $this->smsGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway          = new Client();
            $this->baseUrl          = 'https://bulksmsbd.net/api/smsapi';
            $this->apiKey           = $this->smsGatewayOption['bulksmsbd_api_key'];
            $this->senderId         = $this->smsGatewayOption['bulksmsbd_sender_id'];
        }
    }

    public function status(): bool
    {
        $paymentGateways = SmsGateway::where(['slug' => 'bulksmsbd', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function send($code, $phone, $message): void
    {
        try {
            $info = [
                "api_key" => $this->apiKey,
                "senderid" => $this->senderId,
                "number" => $code . $phone,
                "message" => $message
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
