<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyRent;
use App\Http\Resources\PropertyRentResource;
use App\Http\Requests\StorePropertyRentRequest;
use App\Http\Requests\UpdatePropertyRentRequest;

class PropertyRentController extends Controller
{
    public function index()
    {
        $propertyRents = PropertyRent::all();
        return PropertyRentResource::collection($propertyRents);
    }

    public function show(PropertyRent $propertyRent)
    {
        return new PropertyRentResource($propertyRent);
    }

    public function store(StorePropertyRentRequest $request)
    {
        $propertyRent = PropertyRent::create($request->validated());
        return new PropertyRentResource($propertyRent);
    }

    public function update(UpdatePropertyRentRequest $request, PropertyRent $propertyRent)
    {
        

        $data = $request->validated();

        if (isset($data['price'])) {
            $data['updated_price'] = $data['price'];
            unset($data['price']);
        }
        $propertyRent->update($data);

        return new PropertyRentResource($propertyRent);
    }

    public function destroy(PropertyRent $propertyRent)
    {
        // if (auth()->user()->id !== $propertyRent->lister_id) {
        //     return response()->json(['message' => 'Unauthorized. You do not have permission to perform this action.'], 403);
        // }
        
        $propertyRent->delete();
        return response()->json(['message' => 'Property rent deleted successfully']);
    }
}
