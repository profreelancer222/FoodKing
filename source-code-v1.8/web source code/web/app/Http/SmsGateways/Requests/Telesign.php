<?php

namespace App\Http\SmsGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Telesign extends FormRequest
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
        if (request()->telesign_status == Activity::ENABLE) {
            return [
                'telesign_api_key'      => ['required', 'string'],
                'telesign_customer_id' => ['required', 'string'],
                'telesign_sender_id'   => ['required', 'string'],
                'telesign_status'      => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'telesign_api_key'      => ['nullable', 'string'],
                'telesign_customer_id' => ['nullable', 'string'],
                'telesign_sender_id'   => ['nullable', 'string'],
                'telesign_status'      => ['nullable', 'string'],
            ];
        }
    }
}