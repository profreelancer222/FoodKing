<?php

namespace App\Http\SmsGateways\Gateways;


use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\SmsGateway;
use App\Services\SmsAbstract;
use Illuminate\Support\Facades\Log;

class Telesign extends SmsAbstract
{

    public string $apiKey;
    public string $customerId;
    public string $senderId;
    public string $baseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->smsGateway = SmsGateway::with('gatewayOptions')->where(['slug' => 'telesign'])->first();
        if (!blank($this->smsGateway)) {
            $this->smsGatewayOption = $this->smsGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway      = new Client();
            $this->baseUrl      = 'https://rest-ww.telesign.com/v1/messaging';
            $this->apiKey       = $this->smsGatewayOption['telesign_api_key'];
            $this->customerId   = $this->smsGatewayOption['telesign_customer_id'];
            $this->senderId     = $this->smsGatewayOption['telesign_sender_id'];
        }
    }

    public function status(): bool
    {
        $paymentGateways = SmsGateway::where(['slug' => 'telesign', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function send($code, $phone, $message): void
    {
        try {

            // Concatenate Customer ID and API Key with a colon
            $credentials         = $this->customerId . ':' . $this->apiKey;
            $credentials         = mb_convert_encoding($credentials, 'UTF-8');
            $credentials         = base64_encode($credentials);

            $this->gateway->request('POST', $this->baseUrl, [
                'form_params'       => [
                    'phone_number'  => $code . $phone,
                    'message'       => $message,
                    'message_type'  => 'ARN', // ARN : (Alerts, reminders, and notifications)
                    'sender_id'     => $this->senderId,
                    'is_primary'    => 'true',
                ],
                'headers' => [
                    'accept'        => 'application/json',
                    'content-type'  => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic ' . $credentials,
                ],
            ]);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
