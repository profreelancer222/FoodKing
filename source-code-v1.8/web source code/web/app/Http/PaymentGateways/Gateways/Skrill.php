<?php

namespace App\Http\PaymentGateways\Gateways;


use Exception;
use App\Enums\Activity;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;
use Smartisan\Settings\Facades\Settings;

class Skrill extends PaymentAbstract
{
    public mixed $response;
    private mixed $skrill;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'skrill'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->skrill               = new SkrillRequest();
            $this->skrill->pay_to_email = $this->paymentGatewayOption['skrill_merchant_email'];
        }
    }

    public function payment($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $currencyCode = 'USD';
            $currencyId   = Settings::group('site')->get('site_default_currency');
            if (!blank($currencyId)) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $currencyCode = $currency->code;
                }
            }

            $this->skrill->return_url     = route('payment.successful', ['order' => $order, 'paymentGateway' => 'skrill']);
            $this->skrill->cancel_url     = route('payment.cancel', ['order' => $order, 'paymentGateway' => 'skrill']);
            $this->skrill->status_url     = route('payment.success', ['order' => $order, 'paymentGateway' => 'skrill']);
            $this->skrill->prepare_only   = 1;
            $this->skrill->amount         = $order->total;
            $this->skrill->currency       = $currencyCode;
            $this->skrill->language       = 'EN';
            $this->skrill->invoice_id     = $order->order_serial_no;
            $this->skrill->customer_email = $order->user?->email;

            $client = new SkrillClient($this->skrill);
            $sid    = $client->generateSID();

            $jsonSID = json_decode($sid);
            if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
                return redirect()->route('payment.index', [
                    'order'          => $order,
                    'paymentGateway' => 'skrill'
                ])->with('error', $jsonSID->message ?? trans('all.message.something_wrong'));
            } else {
                $redirectUrl = $client->paymentRedirectUrl($sid);
                return redirect()->away($redirectUrl);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'order'          => $order,
                'paymentGateway' => 'skrill'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'skrill', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request): void
    {
        try {
            if (isset($request['transaction_id'])) {
                $this->paymentService->payment($order, 'skrill', $request['transaction_id']);
            } else {
                Log::info(trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
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
