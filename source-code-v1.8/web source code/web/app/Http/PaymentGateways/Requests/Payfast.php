<?php

namespace App\Http\PaymentGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Payfast extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if (request()->payfast_status == Activity::ENABLE) {
            return [
                'payfast_merchant_id'  => ['required', 'string'],
                'payfast_merchant_key' => ['required', 'string'],
                'payfast_passphrase'   => ['required', 'string'],
                'payfast_mode'         => ['required', 'string'],
                'payfast_status'       => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'payfast_merchant_id'  => ['nullable', 'string'],
                'payfast_merchant_key' => ['nullable', 'string'],
                'payfast_passphrase'   => ['nullable', 'string'],
                'payfast_mode'         => ['nullable', 'string'],
                'payfast_status'       => ['nullable', 'numeric'],
            ];
        }
    }
}
