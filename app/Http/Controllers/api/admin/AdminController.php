<?php

namespace App\Http\Controllers\api\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sale_payment;
use App\Models\Rent_payment;
use App\Models\Sale_offer;
use App\Models\Rent_offer;
use App\Models\PropertyRent;
use App\Models\PropertySale;
use App\Models\User;
use Carbon\Carbon;
use DB;


class AdminController extends Controller
{
    public function index(){
        // Get the current date and time
        $currentTime = Carbon::now();

        // Calculate the date and time 24 hours ago
        $previous24Hours = $currentTime->subHours(24);

        // Sum the total amount paid for sales payments in the last 24 hours
        $totalSalesAmountPaid = Sale_payment::where('sales_payments.created_at', '>=', $previous24Hours)
            ->join('sales_offers', 'sales_payments.sales_offer_id', '=', 'sales_offers.id')
            ->sum('sales_offers.offered_price');


        // Sum the total amount paid for rents payments in the last 24 hours
        $totalRentsAmountPaid = Rent_payment::where('rents_payments.created_at', '>=', $previous24Hours)
            ->join('rents_offers', 'rents_payments.rents_offer_id', '=', 'rents_offers.id')
            ->sum('rents_offers.offered_price');

        // Count the number of users registered in the last 24 hours
        $usersCount = User::where('created_at', '>=', $previous24Hours)->count();

        // Fetch sales payments with related offer and buyer (user) name
        $salesPayments = Sale_payment::where('created_at', '>=', $previous24Hours)
        ->with(['saleOffer', 'saleOffer.buyer']) // Eager load offer and buyer
        ->get();

        // Fetch rents payments with related offer and buyer (user) name
        $rentsPayments = Rent_payment::where('created_at', '>=', $previous24Hours)
        ->with(['rentOffer', 'rentOffer.buyer']) // Eager load offer and buyer
        ->get();

        // Transform sales payments data
        $salesPaymentsData = $salesPayments->map(function ($payment) {
            return [
                'payment_date' => $payment->created_at->format('Y-m-d h:i A'), 
                'status' => $payment->status, 
                'amount_paid' => $payment->saleOffer->offered_price, 
                'user_name' => $payment->saleOffer->buyer->name,
                'payment_type' => 'Sale',
            ];
        });

        // Transform rents payments data
        $rentsPaymentsData = $rentsPayments->map(function ($payment) {
            return [
                'payment_date' => $payment->created_at->format('Y-m-d h:i A'),
                'status' => $payment->status, 
                'amount_paid' => $payment->rentOffer->offered_price, 
                'user_name' => $payment->rentOffer->buyer->name, 
                'payment_type' => 'Rent',
            ];
        });

        // Combine sales and rents payments data
        $combinedPaymentsData = $salesPaymentsData->concat($rentsPaymentsData);

        return response()->json([
            'total_sales_amount_paid' => $totalSalesAmountPaid,
            'total_rents_amount_paid' => $totalRentsAmountPaid,
            'users_count' => $usersCount,
            'payments' => $combinedPaymentsData,
        ]);
    }

    public function rentOffers(){
        $rentOffers = Rent_offer::with('buyer', 'propertyRent.property.images', 'propertyRent.lister')
            ->get()
            ->map(function ($rentOffer) {

                $property = $rentOffer->propertyRent->property;
                $images = $property->images->pluck('url')->toArray();

                // Format the response data
                return [
                    'buyer_name' => $rentOffer->buyer->name,
                    'offered_price' => $rentOffer->offered_price,
                    'message' => $rentOffer->message,
                    'status' => $rentOffer->status,
                    'offer_creation_date' => $rentOffer->created_at->format('Y-m-d H:i A'),
                    'lister_name' => $rentOffer->propertyRent->lister->name,
                    'period' => $rentOffer->propertyRent->period,
                    'price' => $rentOffer->propertyRent->updated_price ?? $rentOffer->propertyRent->price,
                    'list_creation_date' => $rentOffer->propertyRent->created_at->format('Y-m-d H:i A'),
                    'property' => [
                        'id' => $rentOffer->propertyRent->property->id,
                        'title' => $rentOffer->propertyRent->property->title,
                        'city' => $rentOffer->propertyRent->property->city,
                        'district' => $rentOffer->propertyRent->property->district,
                        'street' => $rentOffer->propertyRent->property->street,
                        'type' => $rentOffer->propertyRent->property->type,
                        'description' => $rentOffer->propertyRent->property->description,
                        'area' => $rentOffer->propertyRent->property->area,  
                        'images' => $images,
                    ],
                ];
            });

        return response()->json($rentOffers);
    }

    public function saleOffers(){
        $saleOffers = Sale_offer::with('buyer', 'propertySale.property', 'propertySale.lister')
            ->get()
            ->map(function ($saleOffer) {

                $property = $saleOffer->propertySale->property;
                $images = $property->images->pluck('url')->toArray();

                // Format the response data
                return [
                    'buyer_name' => $saleOffer->buyer->name,
                    'offered_price' => $saleOffer->offered_price,
                    'message' => $saleOffer->message,
                    'status' => $saleOffer->status,
                    'offer_creation_date' => $saleOffer->created_at->format('Y-m-d H:i A'),
                    'lister_name' => $saleOffer->propertySale->lister->name,
                    'price' => $saleOffer->propertySale->updated_price ?? $saleOffer->propertySale->price,
                    'list_creation_date' => $saleOffer->propertySale->created_at->format('Y-m-d H:i A'),
                    'property' => [
                        'id' => $saleOffer->propertySale->property->id,
                        'title' => $saleOffer->propertySale->property->title,
                        'city' => $saleOffer->propertySale->property->city,
                        'district' => $saleOffer->propertySale->property->district,
                        'street' => $saleOffer->propertySale->property->street,
                        'type' => $saleOffer->propertySale->property->type,
                        'description' => $saleOffer->propertySale->property->description,
                        'area' => $saleOffer->propertySale->property->area,  
                        'images' => $images,
                    ],
                ];
            });

        return response()->json($saleOffers);
    }

    public function payments(){
        
        $salesPayments = Sale_payment::
        with(['saleOffer', 'saleOffer.buyer', 'saleOffer.propertySale.property']) // Eager load offer and buyer
        ->get();

        
        $rentsPayments = Rent_payment::
        with(['rentOffer', 'rentOffer.buyer', 'rentOffer.propertyRent.property']) // Eager load offer and buyer
        ->get();


        // Transform sales payments data
        $salesPaymentsData = $salesPayments->map(function ($payment) {
            return [
                'property_title' => $payment->saleOffer->propertySale->property->title,
                'payment_date' => $payment->created_at->format('Y-m-d h:i A'), 
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id, 
                'amount_paid' => $payment->saleOffer->offered_price, 
                'user_name' => $payment->saleOffer->buyer->name,
                'payment_type' => 'Sale',
            ];
        });

        // Transform rents payments data
        $rentsPaymentsData = $rentsPayments->map(function ($payment) {
            return [
                'property_title' => $payment->rentOffer->propertyRent->property->title,
                'payment_date' => $payment->created_at->format('Y-m-d h:i A'),
                'status' => $payment->status, 
                'transaction_id' => $payment->transaction_id, 
                'amount_paid' => $payment->rentOffer->offered_price, 
                'user_name' => $payment->rentOffer->buyer->name, 
                'payment_type' => 'Rent',
            ];
        });

        // Combine sales and rents payments data
        $combinedPaymentsData = $salesPaymentsData->concat($rentsPaymentsData);

        return response()->json([
            'payments' => $combinedPaymentsData,
        ]);
    }

    public function users(){
        $users = User::get();

    return response()->json($users);

    }

    public function deleteUser(Request $request, $id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
