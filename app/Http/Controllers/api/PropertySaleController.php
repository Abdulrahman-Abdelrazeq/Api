<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertySale;
use App\Http\Resources\PropertySaleResource;
use App\Http\Requests\StorePropertySaleRequest;
use App\Http\Requests\UpdatePropertySaleRequest;

class PropertySaleController extends Controller
{
    public function index()
    {
        $propertySales = PropertySale::all();
        return PropertySaleResource::collection($propertySales);
    }

    public function show(PropertySale $propertySale)
    {
        return new PropertySaleResource($propertySale);
    }

    public function store(StorePropertySaleRequest $request)
    {
        $propertySale = PropertySale::create($request->validated());
        return new PropertySaleResource($propertySale);
    }

    public function update(UpdatePropertySaleRequest $request, PropertySale $propertySale)
    {

        

        $data = $request->validated();

        if (isset($data['price'])) {
            $data['updated_price'] = $data['price'];
            unset($data['price']);
        }
        $propertySale->update($data);

        return new PropertySaleResource($propertySale);
    }

    public function destroy(PropertySale $propertySale)
    {
        // if (auth()->user()->id !== $propertySale->lister_id) {
        //     return response()->json(['message' => 'Unauthorized. You do not have permission to perform this action.'], 403);
        // }
        
        $propertySale->delete();
        return response()->json(['message' => 'Property Sale deleted successfully']);
    }
}
