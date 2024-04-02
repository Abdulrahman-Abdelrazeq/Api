<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rent_offer;
use App\Models\Sale_offer;
use App\Models\User;


class AgentOffersController extends Controller
{
    public function getAllUserOffersByListerId($listerId)
    {
        $cleanedOffers = [];

        // Fetch all rent offers for the lister
        $rentOffers = Rent_offer::with(['propertyRent.property.images', 'propertyRent.lister'])
            ->whereHas('propertyRent', function ($query) use ($listerId) {
                $query->where('lister_id', $listerId);
            })
            ->get();

        foreach ($rentOffers as $offer) {
            $cleanedOffer = $this->cleanOfferData($offer, 'Rent');
            $cleanedOffers[] = $cleanedOffer;
        }

        // Fetch all sale offers for the lister
        $saleOffers = Sale_offer::with(['propertySale.property.images', 'propertySale.lister'])
            ->whereHas('propertySale', function ($query) use ($listerId) {
                $query->where('lister_id', $listerId);
            })
            ->get();

        foreach ($saleOffers as $offer) {
            $cleanedOffer = $this->cleanOfferData($offer, 'Sale');
            $cleanedOffers[] = $cleanedOffer;
        }

        // Sort and return the cleaned offers
        return collect($cleanedOffers)->sortByDesc('offer_date')->values()->all();
    }

    // Helper function to clean offer data
    // private function cleanOfferData($offer, $offerType)
    // {
    //     $property = $offer->{$offerType === 'Rent' ? 'propertyRent' : 'propertySale'};
    //     $propertyTitle = optional(optional($property)->property)->title;
    //     $propertyImages = optional(optional($property)->property)->images;
    //     $firstImageUrl = isset($propertyImages[0]) ? $propertyImages[0]['url'] : null;

    //     $listerName = optional($property)->lister->name;

    //     $cleanedOffer = [
    //         'offer_id' => $offer->id,
    //         'offer_buyer' => $offer->buyer_id,
    //         'property_title' => $propertyTitle,
    //         'property_image' => $firstImageUrl,
    //         'offer_for' => $offerType,
    //         'lister_name' => $listerName,
    //         'offer_price' => $offer->offered_price,
    //         'offer_message' => $offer->message,
    //         'offer_status' => $offer->status,
    //         'offer_date' => $offer->created_at->format('Y-m-d H:i A'),
    //     ];

    //     if ($offerType === 'Rent') {
    //         $cleanedOffer['period'] = optional($offer->propertyRent)->period;
    //     }

    //     return $cleanedOffer;
    // }
    private function cleanOfferData($offer, $offerType)
    {
        $property = $offer->{$offerType === 'Rent' ? 'propertyRent' : 'propertySale'};
        $propertyTitle = optional(optional($property)->property)->title;
        $propertyImages = optional(optional($property->property)->images);
        $firstImageUrl = isset($propertyImages[0]) ? $propertyImages[0]['url'] : null;

        $listerName = optional($property->lister)->name;

        // Retrieve buyer information
        $buyerName = null;
        $buyerEmail = null;

        if ($offer->buyer_id) {
            $buyer = User::find($offer->buyer_id);
            if ($buyer) {
                $buyerName = $buyer->name;
                $buyerEmail = $buyer->email;
            }
        }

        $cleanedOffer = [
            'offer_id' => $offer->id,
            'property_title' => $propertyTitle,
            'property_image' => $firstImageUrl,
            'offer_for' => $offerType,
            'lister_name' => $listerName,
            'buyer_name' => $buyerName,
            'buyer_email' => $buyerEmail,
            'offer_price' => $offer->offered_price,
            'offer_message' => $offer->message,
            'offer_status' => $offer->status,
            // 'offer_date' => $offer->created_at->format('Y-m-d H:i A'),
        ];

        if ($offerType === 'Rent') {
            $cleanedOffer['period'] = optional($offer->propertyRent)->period;
        }

        return $cleanedOffer;
    }



}
