<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Rent_offer extends Model
{
    protected $fillable = ['buyer_id', 'property_rent_id', 'offered_price', 'message', 'status'];
    protected $table = 'rents_offers';
    use HasFactory;

    public function propertyRent()
    {
        return $this->belongsTo(PropertyRent::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function rentPayment()
    {
        return $this->hasOne(Rent_payment::class);
    }
}
