<?php

namespace App\Http\SmsGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Bulksmsbd extends FormRequest
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
        if (request()->bulksmsbd_status == Activity::ENABLE) {
            return [
                'bulksmsbd_api_key'  => ['required', 'string'],
                'bulksmsbd_sender_id' => ['required', 'string'],
                'bulksmsbd_status'   => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'bulksmsbd_api_key'  => ['nullable', 'string'],
                'bulksmsbd_sender_id' => ['nullable', 'string'],
                'bulksmsbd_status'   => ['nullable', 'string'],
            ];
        }
    }
}
