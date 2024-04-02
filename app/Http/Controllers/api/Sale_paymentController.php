<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale_payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\Sale_paymentRequest;
use App\Http\Requests\Sale_paymentUpdateRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Sale_paymentResource;


class Sale_paymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment = Sale_payment::all();
        return Sale_paymentResource::collection($payment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Sale_paymentRequest   $request)
    {
        
        
        $validatedRequest = $request->validated();
        Sale_payment::create($validatedRequest);

        // $validatedRequest = $request->validate([
        //     'buyer_id' => 'required|numeric',
        //     'sales_offer_id' => 'required|numeric|unique:sales_payments',
        //     'transaction_id' => 'required',
        // ]);

        // $validatedRequest = Validator::make($request->all(), [
        //     'sales_offer_id' => 'required|numeric|unique:sales_payments',
        //     'transaction_id' => 'required',
        // ]);
        // if($validatedRequest->fails()){
        //     $errors = $validatedRequest->errors()->all();
        //     return response()->json(['errors' => $errors], 422);
        // }

        // Sale_payment::create($request->all());

        // Sale_payment::create([
        //     'buyer_id' => $request->buyer_id,
        //     'sales_offer_id' => $request->sales_offer_id,
        //     'transaction_id' => $request->transaction_id,
        // ]);
    
        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale_payment $payment)
    {
        return new Sale_paymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(/* Sale_paymentUpdateRequest */ Request   $request, Sale_payment $payment)
    {
        // $validatedRequest = $request->validate([
        //     'transaction_id' => 'required|numeric',
        // ]);

        $validatedRequest = $request->validated();


        // $validatedRequest = Validator::make($request->all(), [
        //     'sales_offer_id' => ['numeric',Rule::unique('sales_payments')->ignore($payment->id),],
        //     'transaction_id' => 'required',
        //     'status' => 'string',
        // ]);
        // if($validatedRequest->fails()){
        //     $errors = $validatedRequest->errors()->all();
        //     return response()->json(['errors' => $errors], 422);
        // }

        $payment->update($request->all());
        return response()->json(['message' => 'Data updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale_payment $payment)
    {
        $payment->delete();
        return 'Delete';
    }
}
