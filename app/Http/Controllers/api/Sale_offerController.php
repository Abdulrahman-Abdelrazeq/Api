<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale_offer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\Sale_offerRequest;
use App\Http\Requests\Sale_offerUpdateRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Sale_offerResource;
use Illuminate\Support\Facades\DB;


class Sale_offerController extends Controller
{
    // function __construct(){
    //     $this->middleware('auth:sanctum');
    // }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $saleOffers = Sale_offer::all();
    
    
        $responseData = [
            'data' => $saleOffers->toArray()
        ];

        return response()->json($responseData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( /* Request */ Sale_offerRequest $request)
    {

        $validatedRequest = $request->validated();
        Sale_offer::create($validatedRequest);

        // $validatedRequest = $request->validate([
        //     'buyer_id' => 'required|numeric',
        //     'property_sale_id' => 'required|numeric|unique:sales_offers',
        //     'offered_price' => 'required|numeric',
        //     'message' => 'string'
        // ]);

        // $validatedRequest = Validator::make($request->all(), [
        //     'buyer_id' => 'required|numeric',
        //     'property_sale_id' => 'required|numeric|unique:sales_offers',
        //     'offered_price' => 'required|numeric',
        //     'message' => 'string'
        // ]);
        // if($validatedRequest->fails()){
        //     $errors = $validatedRequest->errors()->all();
        //     return response()->json(['errors' => $errors], 422);
        // }

        // Sale_offer::create($request->all());

        // Sale_offer::create([
        //     'buyer_id' => $request->buyer_id,
        //     'property_sale_id' => $request->property_sale_id,
        //     'offered_price' => $request->offered_price,
        //     'message' => $request->message
        // ]);

        return response()->json(['message' => 'Data saved successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale_offer $sale_offer)
    {
        return new Sale_offerResource($sale_offer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( /* Request */ Sale_offerUpdateRequest $request, Sale_offer $sale_offer)
    {
        // $validatedRequest = $request->validate([
        //     'offered_price' => 'required|numeric',
        //     'message' => 'string',
        // ]);

        $validatedRequest = $request->validated();


        // $validatedRequest = Validator::make($request->all(), [
        //     'offered_price' => 'required|numeric',
        //     'message' => 'string'
        // ]);
        // if($validatedRequest->fails()){
        //     $errors = $validatedRequest->errors()->all();
        //     return response()->json(['errors' => $errors], 422);
        // }

        $sale_offer->update($request->all());

        return response()->json(['message' => 'Data updated successfully'], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale_offer $sale_offer)
    {
        $sale_offer->delete();
        return 'Delete';
    }


}
