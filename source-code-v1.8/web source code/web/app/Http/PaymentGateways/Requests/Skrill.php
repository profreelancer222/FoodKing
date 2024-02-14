<?php

namespace App\Http\PaymentGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Skrill extends FormRequest
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
        if (request()->skrill_status == Activity::ENABLE) {
            return [
                'skrill_merchant_email'        => ['required', 'string'],
                'skrill_merchant_api_password' => ['required', 'string'],
                'skrill_mode'                  => ['required', 'string'],
                'skrill_status'                => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'skrill_merchant_email'        => ['nullable', 'string'],
                'skrill_merchant_api_password' => ['nullable', 'string'],
                'skrill_mode'                  => ['nullable', 'string'],
                'skrill_status'                => ['nullable', 'numeric'],
            ];
        }
    }
}
