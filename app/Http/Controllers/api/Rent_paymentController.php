<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Rent_payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\Rent_paymentRequest;
use App\Http\Requests\Rent_paymentUpdateRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Rent_paymentResource;


class Rent_paymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment = Rent_payment::all();
        return Rent_paymentResource::collection($payment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( Rent_paymentRequest   $request)
    {
        
        
        $validatedRequest = $request->validated();
        Rent_payment::create($validatedRequest);

        // $validatedRequest = $request->validate([
        //     'rents_offer_id' => 'required|numeric|unique:rents_payments',
        //     'transaction_id' => 'required',
        // ]);

        // $validatedRequest = Validator::make($request->all(), [
        //     'rents_offer_id' => 'required|numeric|unique:rents_payments',
        //     'transaction_id' => 'required',
        // ]);
        // if($validatedRequest->fails()){
        //     $errors = $validatedRequest->errors()->all();
        //     return response()->json(['errors' => $errors], 422);
        // }

        // Rent_payment::create($request->all());

        // Rent_payment::create([
        //     'buyer_id' => $request->buyer_id,
        //     'rents_offer_id' => $request->rents_offer_id,
        //     'transaction_id' => $request->transaction_id,
        // ]);
    
        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rent_payment $payment)
    {
        return new Rent_paymentResource($payment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(/* Rent_paymentUpdateRequest */ Request   $request, Rent_payment $payment)
    {
        // $validatedRequest = $request->validate([
        //     'transaction_id' => 'required|numeric',
        // ]);

        $validatedRequest = $request->validated();


        // $validatedRequest = Validator::make($request->all(), [
        //     'rents_offer_id' => ['numeric',Rule::unique('rents_payments')->ignore($payment->id),],
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
    public function destroy(Rent_payment $payment)
    {
        $payment->delete();
        return 'Delete';
    }
}
