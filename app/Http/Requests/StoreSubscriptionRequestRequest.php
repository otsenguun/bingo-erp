<?php

namespace App\Http\Requests;

use App\Rules\CustomDomainValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get data for validation.
     *
     * @return array
     */
    public function validationData()
    {
        $data = $this->all();

        if ($this->input('payment_method') === 'manual') {
            $data['transaction_id'] = $this->input('transaction_id');
            $data['document_path'] = $this->input('document_path');
        }

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'plan_id' => ['required'],
            'quantity' => ['required', 'min:1', 'max:60'],
            'payment_method' => [
                'required',
                Rule::in(getActivePaymentMethods()->pluck('identifier')->toArray()),
            ],
        ];

        if ($this->input('payment_method') === 'manual') {
            $rules['transaction_id'] = ['required_without:document_path', 'max:255'];
            $rules['document_path'] = [
                'required_without:transaction_id',
                function ($attribute, $value, $fail) {
                    if ($this->filled('document_path') && !$this->hasFile('document_path')) {
                        $fail('The ' . $attribute . ' must be a file.');
                    }
                },
                'max:' . (1024 * 100),
            ];
        }

        return $rules;
    }
}
