<?php

namespace App\Http\SmsGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Twofactor extends FormRequest
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
        if (request()->twofactor_status == Activity::ENABLE) {
            return [
                'twofactor_api_key' => ['required', 'string'],
                'twofactor_module' => ['required', 'string'],
                'twofactor_from'   => ['required', 'string'],
                'twofactor_status' => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'twofactor_api_key' => ['nullable', 'string'],
                'twofactor_module' => ['nullable', 'string'],
                'twofactor_from'   => ['nullable', 'string'],
                'twofactor_status' => ['nullable', 'string'],
            ];
        }
    }
}