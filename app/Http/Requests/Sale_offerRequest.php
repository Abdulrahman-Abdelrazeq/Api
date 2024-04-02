<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Sale_offerRequest extends FormRequest
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
            'buyer_id' => 'required|numeric',
            'property_sale_id' => 'required|numeric',
            'offered_price' => 'required|numeric',
            'message' => 'string',
        ];
    }
}
