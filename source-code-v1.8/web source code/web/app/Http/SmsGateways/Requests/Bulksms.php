<?php

namespace App\Http\SmsGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Bulksms extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->bulksms_status == Activity::ENABLE) {
            return [
                'bulksms_username' => ['required', 'string'],
                'bulksms_password' => ['required', 'string'],
                'bulksms_status'   => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'bulksms_username' => ['nullable', 'string'],
                'bulksms_password' => ['nullable', 'string'],
                'bulksms_status'   => ['nullable', 'string'],
            ];
        }
    }
}
