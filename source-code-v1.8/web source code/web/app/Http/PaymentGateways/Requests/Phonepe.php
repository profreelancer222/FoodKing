<?php

namespace App\Http\PaymentGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Phonepe extends FormRequest
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
        if (request()->phonepe_status == Activity::ENABLE) {
            return [
                'phonepe_merchant_id'      => ['required', 'string'],
                'phonepe_merchant_user_id' => ['required', 'string'],
                'phonepe_key_index'        => ['required', 'string'],
                'phonepe_key'              => ['required', 'string'],
                'phonepe_mode'             => ['required', 'string'],
                'phonepe_status'           => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'phonepe_merchant_id'      => ['nullable', 'string'],
                'phonepe_merchant_user_id' => ['nullable', 'string'],
                'phonepe_key_index'        => ['nullable', 'string'],
                'phonepe_key'              => ['nullable', 'string'],
                'phonepe_mode'             => ['nullable', 'string'],
                'phonepe_status'           => ['nullable', 'numeric'],
            ];
        }
    }
}
