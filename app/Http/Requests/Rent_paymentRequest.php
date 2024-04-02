<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Rent_paymentRequest extends FormRequest
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
            'rents_offer_id' => 'required|numeric|unique:rents_payments',
            'transaction_id' => 'required',
            'status' => 'string',
        ];
    }
}
