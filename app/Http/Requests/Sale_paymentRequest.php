<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Sale_paymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sales_offer_id' => 'required|numeric|unique:sales_payments',
            'transaction_id' => 'required',
            'status' => 'string',
        ];
    }
}
