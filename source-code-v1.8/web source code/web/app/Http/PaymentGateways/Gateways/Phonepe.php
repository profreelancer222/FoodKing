<?php

namespace App\Http\PaymentGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dipesh79\LaravelPhonePe\LaravelPhonePe;
use Illuminate\Support\Facades\Config;

class Phonepe extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway       = PaymentGateway::with('gatewayOptions')->where(['slug' => 'phonepe'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
        Config::set('phonepe.merchantId', $this->paymentGatewayOption['phonepe_merchant_id']);
        Config::set('phonepe.merchantUserId', $this->paymentGatewayOption['phonepe_merchant_user_id']);
        Config::set('phonepe.env', $this->paymentGatewayOption['phonepe_mode'] == GatewayMode::SANDBOX ? "staging" : "production");
        Config::set('phonepe.saltIndex', $this->paymentGatewayOption['phonepe_key_index']);
        Config::set('phonepe.saltKey', $this->paymentGatewayOption['phonepe_key']);
    }

    public function payment($order, $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {

            Config::set('phonepe.merchantTransactionId', "PHONEPE" . uniqid());
            Config::set('phonepe.redirectUrl', route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe']));
            Config::set('phonepe.callBackUrl', route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe']));


            $phonepe = new LaravelPhonePe();
            $url = $phonepe->makePayment(floatval($order->total), $order->user?->phone, route('payment.success', ['order' => $order, 'paymentGateway' => 'phonepe']));

            if ($url) {
                return redirect()->away($url);
            } else {
                return redirect()->route('payment.index', [
                    'order'          => $order,
                    'paymentGateway' => 'phonepe'
                ])->with('error', "JSON Data parsing error!");
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'order'          => $order,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'phonepe', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['transactionId']) && $request['code'] == "PAYMENT_SUCCESS") {
                $paymentService = new PaymentService;
                $paymentService->payment($order, 'phonepe', $request['transactionId']);
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order'          => $order,
                    'paymentGateway' => 'phonepe'
                ])->with('error', $this->response['message'] ?? trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'phonepe'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
