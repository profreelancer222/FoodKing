<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiningTableRequest extends FormRequest
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

        return [
            'name'      => [
                'required',
                'string',
                'max:190',
                Rule::unique('dining_tables', 'name')->where(function ($query) {
                    return $query->where('branch_id', $this->input('branch_id'));
                })->ignore($this->route('diningTable.id')),
            ],
            'size'      => ['numeric'],
            'branch_id' => ['required', 'numeric'],
            'status'    => ['required', 'numeric', 'max:24'],
        ];
    }
}