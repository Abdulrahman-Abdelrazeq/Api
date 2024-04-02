<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Property_sale;
use App\Models\Sale_payment;

class Sale_offer extends Model
{
    protected $fillable = ['buyer_id', 'property_sale_id', 'offered_price', 'message', 'status'];
    protected $table = 'sales_offers';
    use HasFactory;

    public function propertySale()
    {
        return $this->belongsTo(PropertySale::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function salePayment()
    {
        return $this->hasOne(Sale_payment::class);
    }
}
