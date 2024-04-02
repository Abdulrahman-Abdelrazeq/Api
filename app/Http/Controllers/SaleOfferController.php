<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale_offer;
use Illuminate\Support\Facades\Response;
use App\Mail\SendMail;
use Mail;


class SaleOfferController extends Controller
{
    public function acceptOffer($id)
    {
        $offer = Sale_offer::findOrFail($id);
        $offer->status = 'accepted';
        $offer->save();
        $buyer_name = $offer->buyer->name;
        $buyer_email = $offer->buyer->email;
        $propertyTitle = $offer->propertySale->property->title;
        $mailData = [
            'title' => 'Mail from BAZAR',
            'body_1' => 'Your offer for ',
            'body_2' => $propertyTitle,
            'body_3' => ' has been accepted.',
            'buyer_name' => $buyer_name,
            'property_title' => $propertyTitle
        ];
        Mail::to($buyer_email)->send(new SendMail($mailData));
        return Response::json(['message' => 'Offer accepted successfully']);
    }

    /**
     * Reject the specified sales offer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rejectOffer($id)
    {
        $offer = Sale_offer::findOrFail($id);
        $offer->status = 'rejected';
        $offer->save();
        $buyer_name = $offer->buyer->name;
        $buyer_email = $offer->buyer->email;
        $propertyTitle = $offer->propertySale->property->title;
        $mailData = [
            'title' => 'Mail from BAZAR',
            'body_1' => 'Your offer for ',
            'body_2' => $propertyTitle,
            'body_3' => ' has been rejected.',
            'buyer_name' => $buyer_name,
            'property_title' => $propertyTitle
        ];
        Mail::to($buyer_email)->send(new SendMail($mailData));
        return Response::json(['message' => 'Offer rejected successfully']);
    }
}
