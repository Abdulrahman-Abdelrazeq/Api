<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rent_offer;
use App\Models\Sale_offer;

class UserOffersController extends Controller
{
    public function getAllUserOffers($userId){

        // Fetch all rent offers for the user
        $rentOffers = Rent_offer::with(['propertyRent.property.images','propertyRent.lister'])
        ->where('buyer_id', $userId)
        ->get()
        ->map(function ($offer){
            return [
                'offer_id' => $offer->id,
                'property_title' => $offer->propertyRent->property->title,
                'property_images' => $offer->propertyRent->property->images,
                'offer_for' => 'Rent',
                'period' => $offer->propertyRent->period,
                'lister_name' => $offer->propertyRent->lister->name,
                'offer_price' => $offer->offered_price,
                'offer_message' => $offer->message,
                'offer_status' => $offer->status,
                'offer_date' => $offer->created_at->format('Y-m-d H:i A'),
            ];

        });

        // Fetch all sale offers for the user
        $saleOffers = Sale_offer::with(['propertySale.property.images','propertySale.lister'])
        ->where('buyer_id', $userId)
        ->get()
        ->map(function ($offer){
            return [
                'offer_id' => $offer->id,
                'property_title' => $offer->propertySale->property->title,
                'property_images' => $offer->propertySale->property->images,
                'offer_for' => 'Sale',
                'lister_name' => $offer->propertySale->lister->name,
                'offer_price' => $offer->offered_price,
                'offer_message' => $offer->message,
                'offer_status' => $offer->status,
                'offer_date' => $offer->created_at->format('Y-m-d H:i A'),
            ];

        });

        $combinedOffers = $rentOffers->isEmpty() ? $saleOffers : $rentOffers->merge($saleOffers);
        $sortedOffers = $combinedOffers->sortByDesc('offer_date')->values()->all();
        
        $cleanedOffers = [];
        
        foreach ($sortedOffers as $offer) {
            $propertyImages = $offer['property_images'];
            $firstImageUrl = isset($propertyImages[0]) ? $propertyImages[0]['url'] : null;
        
            $cleanedOffer = [
                'offer_id' => $offer['offer_id'],
                'property_title' => $offer['property_title'],
                'property_image' => $firstImageUrl,
                'offer_for' => $offer['offer_for'],
                'lister_name' => $offer['lister_name'],
                'offer_price' => $offer['offer_price'],
                'offer_message' => $offer['offer_message'],
                'offer_status' => $offer['offer_status'],
                'offer_date' => $offer['offer_date'],
            ];
        
            // Check if the offer is for rent, then include the period key
            if ($offer['offer_for'] === 'Rent') {
                $cleanedOffer['period'] = $offer['period'];
            }
        
            $cleanedOffers[] = $cleanedOffer;
        }
        
        return $cleanedOffers;
        

    }
}
