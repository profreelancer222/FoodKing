<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use Exception;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;

class Payfast extends PaymentAbstract
{
    public mixed $response;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'payfast'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
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

            $merchantId  = $this->paymentGatewayOption['payfast_merchant_id'];
            $merchantKey = $this->paymentGatewayOption['payfast_merchant_key'];
            $passphrase  = $this->paymentGatewayOption['payfast_passphrase'];

            $data       = [
                'merchant_id'   => $merchantId,
                'merchant_key'  => $merchantKey,
                'return_url'    => route('payment.success', ['order' => $order, 'paymentGateway' => 'payfast']),
                'cancel_url'    => route('payment.cancel', ['order' => $order, 'paymentGateway' => 'payfast']),
                'notify_url'    => route('payment.success', ['order' => $order, 'paymentGateway' => 'payfast']),
                'name_first'    => $order->user?->FirstName,
                'name_last'     => $order->user?->LastName,
                'email_address' => $order->user?->email,
                'm_payment_id'  => $order->order_serial_no,
                'amount'        => number_format(sprintf('%.2f', $order->total), 2, '.', ''),
                'item_name'     => $order->order_serial_no
            ];

            $signature = $this->generateSignature($data, $passphrase);
            $data['signature'] = $signature;

            $payfastUrl = $this->paymentGatewayOption['payfast_mode'] == GatewayMode::SANDBOX ? 'https://sandbox.payfast.co.za​/eng/process' : 'https://www.payfast.co.za/eng/process';
            $payfastUrl .= '?' . http_build_query($data);

            if (isset($payfastUrl)) {
                return redirect()->away($payfastUrl);
            } else {
                return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'payfast'])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'payfast'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'payfast', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request)
    {
        try {

            if (isset($request['m_payment_id']) && $request['status'] == 'COMPLETED') {
                $this->paymentService->payment($order, 'payfast', $request['m_payment_id']);
                return redirect()->route('payment.successful', ['order' => $order])->with(
                    'success',
                    trans('all.message.payment_successful')
                );
            } else {
                return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'payfast'])->with(
                    'error',
                    trans('all.message.something_wrong')
                );
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', ['order' => $order, 'paymentGateway' => 'payfast'])->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order])->with(
            'error',
            trans('all.message.something_wrong')
        );
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    function generateSignature($data, $passPhrase = null)
    {
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }
        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }
}
