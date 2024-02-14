<?php

namespace App\Http\SmsGateways\Gateways;


use Exception;
use GuzzleHttp\Client;
use App\Enums\Activity;
use App\Models\SmsGateway;
use App\Services\SmsAbstract;
use Illuminate\Support\Facades\Log;

class Twofactor extends SmsAbstract
{

    public string $apiKey;
    public string $baseUrl;
    public string $module;
    public string $from;

    public function __construct()
    {
        parent::__construct();
        $this->smsGateway = SmsGateway::with('gatewayOptions')->where(['slug' => 'twofactor'])->first();
        if (!blank($this->smsGateway)) {
            $this->smsGatewayOption = $this->smsGateway->gatewayOptions->pluck('value', 'option');
            $this->gateway = new Client();
            $this->baseUrl = 'https://2factor.in/API/R1/';
            $this->apiKey = $this->smsGatewayOption['twofactor_api_key'];
            $this->module = $this->smsGatewayOption['twofactor_module'];
            $this->from = $this->smsGatewayOption['twofactor_from'];
        }
    }

    public function status(): bool
    {
        $paymentGateways = SmsGateway::where(['slug' => 'twofactor', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function send($code, $phone, $message): void
    {
        try {
            $options = [
                'form_params' => [
                    'module' => $this->module,
                    'apikey' => $this->apiKey,
                    'to' => $code . $phone,
                    'from' => $this->from,
                    'msg' => $message
                ]
            ];

            $request = $this->gateway->request('POST', $this->baseUrl);
            $this->gateway->sendAsync($request, $options)->wait();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
