<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class SimilarPropertiesController extends Controller
{
    public function getSimilarProperties($propertyId)
    {
        // Retrieve the details of the specified property
        $property = Property::findOrFail($propertyId);
    
        // Define criteria for similarity (e.g., same city, same district, same type)
        $similarProperties = Property::with(['images' => function ($query) {
                $query->select('property_id', 'url')->first(); // Select only the first image URL
            }, 'sales', 'rents'])
            ->select('id', 'title', 'city', 'district') // Select specific columns
            ->where('city', $property->city)
            ->where('district', $property->district)
            ->where('type', $property->type)
            ->where('id', '!=', $property->id) // Exclude the current property
            ->take(5) // Limit the number of similar properties to fetch
            ->get();
    
        // Calculate the price for each similar property
        foreach ($similarProperties as $similarProperty) {
            $price = $similarProperty->price();
            $similarProperty->setAttribute('price', $price);
        }
    
        return response()->json($similarProperties);
    }
    

}
