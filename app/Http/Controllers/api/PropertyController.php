<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\PropertyRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyRent;
use App\Models\PropertySale;
use App\Models\Image;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderBy = $request->input('order_by');

        if($orderBy == 'Highest_area') {
            $properties = Property::orderBy('area', "desc")->get();
        } else if ($orderBy == 'Lowest_area') {
            $properties = Property::orderBy('area', "asc")->get();
        }else {
            $properties = Property::all();
        }

        // Fetch price information for each property
        foreach ($properties as $property) {
            $propertyRent = PropertyRent::where('property_id', $property->id)->first();
            $propertySale = PropertySale::where('property_id', $property->id)->first();
            $image = Image::where('property_id', $property->id)->first();
            if($image){
                $property->image = $image->url;
            }

            // Check if there's a rental record
            if ($propertyRent) {
                if (!is_null($propertyRent->updated_price)) {
                    $property->old_price = $propertyRent->price; // Original price
                    $property->price = $propertyRent->updated_price; // Updated price
                } else {
                    $property->price = $propertyRent->price; // Regular price
                }
                $property->lister_id = $propertyRent->lister_id;
            }
            // Check if there's a sales record
            elseif ($propertySale) {
                if (!is_null($propertySale->updated_price)) {
                    $property->old_price = $propertySale->price; // Original price
                    $property->price = $propertySale->updated_price; // Updated price
                } else {
                    $property->price = $propertySale->price; // Regular price
                }
                $property->lister_id = $propertySale->lister_id;
            }  
        }

        return $properties;
    }

    public function index_rent_or_sale(Request $request)
    {
        $orderBy = $request->input('order_by');
        $status = $request->input('status');
        $query = Property::query();
        if($status == 'for_rent') {
            $query->where('status', $status);
        } else if ($status == 'for_sale') {
            $query->where('status', $status);
        }
        if($orderBy == 'Highest_area') {
            $properties = $query->orderBy('area', "desc")->get();
        } else if ($orderBy == 'Lowest_area') {
            $properties = $query->orderBy('area', "asc")->get();
        }else {
            $properties = $query->get();
        }

        foreach ($properties as $property) {
            $propertyRent = PropertyRent::where('property_id', $property->id)->first();
            $propertySale = PropertySale::where('property_id', $property->id)->first();
            $image = Image::where('property_id', $property->id)->first();
            if($image){
                $property->image = $image->url;
            }
            // Check if there's a rental record
            if ($propertyRent) {
                if (!is_null($propertyRent->updated_price)) {
                    $property->old_price = $propertyRent->price; // Original price
                    $property->price = $propertyRent->updated_price; // Updated price
                } else {
                    $property->price = $propertyRent->price; // Regular price
                }
            }
            // Check if there's a sales record
            elseif ($propertySale) {
                if (!is_null($propertySale->updated_price)) {
                    $property->old_price = $propertySale->price; // Original price
                    $property->price = $propertySale->updated_price; // Updated price
                } else {
                    $property->price = $propertySale->price; // Regular price
                }
            }  
        }

        return $properties;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropertyRequest $request)
    {
        $user = Auth::user();

        // Validate the request
        $validatedData = $request->validated();

        // Assign the user_id to the property
        // $validatedData['user_id'] = $user->id;

        // Create the property
        $property = Property::create($validatedData);

        // Return the created property
        return $property;
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $propertyRent = PropertyRent::where('property_id', $property->id)->first();
        $propertySale = PropertySale::where('property_id', $property->id)->first();
        // Check if there's a rental record
        if ($propertyRent) {
            if (!is_null($propertyRent->updated_price)) {
                $property->old_price = $propertyRent->price; // Original price
                $property->price = $propertyRent->updated_price; // Updated price
            } else {
                $property->price = $propertyRent->price; // Regular price
            }
            $property->property_rent_id = $propertyRent->id;
            $property->rental_period = $propertyRent->period;
        }
        // Check if there's a sales record
        elseif ($propertySale) {
            if (!is_null($propertySale->updated_price)) {
                $property->old_price = $propertySale->price; // Original price
                $property->price = $propertySale->updated_price; // Updated price
            } else {
                $property->price = $propertySale->price; // Regular price
            }
            $property->property_sale_id = $propertySale->id;
        } 

        // Eager load images along with the property
        $property->load('images');
        // Retrieve image URLs and store them in an array
        $property->image_id = $property->images->pluck('id')->toArray();
        $property->image = $property->images->pluck('url')->toArray();
        // Remove the loaded relationship from the property object
        $property->unsetRelation('images');

        return $property;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropertyRequest $request, Property $property)
    {
        // $user = Auth::user();

        // if ($property->user_id !== $user->id) {
        //     return response()->json(['error' => 'You are not authorized to update this property.'], 403);
        // }
        $requestValidation = $request->validated();
        $property->update($requestValidation);
        return $property;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // $user = Auth::user();
        // if ($property->user_id !== $user->id) {
        //     return response()->json(['error' => 'You are not authorized to delete this property.'], 403);
        // }

        $property->delete();
        return response()->json(['message' => 'Property deleted successfully']);
    }

    // public function search(Request $request)
    // {
    //     $city = $request->input('city');
    //     $district = $request->input('district');
    //     $propertyType = $request->input('propertyType');
    //     $status = $request->input('status');
    //     $area = $request->input('area');
    //     $beds = $request->input('beds');
    //     $baths = $request->input('baths');
    //     $price = $request->input('price');

    //     $query = Property::query();

    //     if ($city) {
    //         $query->where('city', 'like', "%$city%");
    //     }

    //     if ($district) {
    //         $query->where('district', 'like', "%$district%");
    //     }

    //     if ($propertyType) {
    //         $query->where('type', 'like', "%$propertyType%");
    //     }

    //     if ($status) {
    //         $query->where('status', $status);
    //     }

    //     if ($area) {
    //         $query->where('area', '>', $area);
    //     }        

    //     if ($beds) {
    //         $query->where('beds', $beds);
    //     }

    //     if ($baths) {
    //         $query->where('baths', $baths);
    //     }

    //     if ($price) {
    //         $query->whereHas('sales', function ($salesQuery) use ($price) {
    //             $salesQuery->where('price', '>=', $price);
    //         })->orWhereHas('rents', function ($rentsQuery) use ($price) {
    //             $rentsQuery->where('price', '>=', $price);
    //         });
    //     }

    //     $properties = $query->get();
    
    //     return response()->json($properties);
    // }

    public function search(Request $request)
    {
        $city = $request->input('city');
        $district = $request->input('district');
        $propertyType = $request->input('propertyType');
        $status = $request->input('status');
        $area = $request->input('area');
        $beds = $request->input('beds');
        $baths = $request->input('baths');
        $price = $request->input('price');
        $period = $request->input('period');

        $query = Property::query();

        // Apply price condition
        if ($price) {
            $query->where(function ($subQuery) use ($price) {
                $subQuery->whereHas('sales', function ($salesQuery) use ($price) {
                    $salesQuery->where('price', '>=', $price);
                })->orWhereHas('rents', function ($rentsQuery) use ($price) {
                    $rentsQuery->where('price', '>=', $price);
                });
            });
        }

        if ($period) {
            $query->whereHas('rents', function ($rentsQuery) use ($period) {
                $rentsQuery->where('period', $period);
            });
        }

        // Apply other filters
        if ($city) {
            $query->where('city', 'like', "%$city%");
        }

        if ($district) {
            $query->where('district', 'like', "%$district%");
        }

        if ($propertyType) {
            $query->where('type', 'like', "%$propertyType%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($area) {
            $query->where('area', '>', $area);
        }        

        if ($beds) {
            $query->where('beds', $beds);
        }

        if ($baths) {
            $query->where('baths', $baths);
        }

        // Get the properties matching the criteria
        $properties = $query->get();
        // Fetch price information for each property
        foreach ($properties as $property) {
            $propertyRent = PropertyRent::where('property_id', $property->id)->first();
            $propertySale = PropertySale::where('property_id', $property->id)->first();
            $image = Image::where('property_id', $property->id)->first();
            if($image){
                $property->image = $image->url;
            }

            // Check if there's a rental record
            if ($propertyRent) {
                $property->price = $propertyRent->price;
            }
            // Check if there's a sales record
            elseif ($propertySale) {
                $property->price = $propertySale->price;
            }  
        }

        return response()->json($properties);
    }

public function properties_agent(Request $request){
    $agent_id = $request->input('agent_id');

    // Query properties where the agent_id matches the lister_id in property_sales
    $propertiesForSale = Property::join('property_sales', 'properties.id', '=', 'property_sales.property_id')
        ->where('property_sales.lister_id', $agent_id)
        ->select('properties.*')
        ->get();

    // Query properties where the agent_id matches the lister_id in property_rents
    $propertiesForRent = Property::join('property_rents', 'properties.id', '=', 'property_rents.property_id')
        ->where('property_rents.lister_id', $agent_id)
        ->select('properties.*')
        ->get();

    // Merge the results of both queries
    $properties = $propertiesForSale->merge($propertiesForRent);

    foreach ($properties as $property) {
        $propertyRent = PropertyRent::where('property_id', $property->id)->first();
        $propertySale = PropertySale::where('property_id', $property->id)->first();
        $image = Image::where('property_id', $property->id)->first();
        if($image){
            $property->image = $image->url;
        }
        // Check if there's a rental record
        if ($propertyRent) {
            if (!is_null($propertyRent->updated_price)) {
                $property->old_price = $propertyRent->price; // Original price
                $property->price = $propertyRent->updated_price; // Updated price
            } else {
                $property->price = $propertyRent->price; // Regular price
            }
        }
        // Check if there's a sales record
        elseif ($propertySale) {
            if (!is_null($propertySale->updated_price)) {
                $property->old_price = $propertySale->price; // Original price
                $property->price = $propertySale->updated_price; // Updated price
            } else {
                $property->price = $propertySale->price; // Regular price
            }
        }  
    }
    return $properties;
}


}
