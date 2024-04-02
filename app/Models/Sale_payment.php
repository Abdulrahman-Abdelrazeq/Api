<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Sale_offer;

class Sale_payment extends Model
{
    protected $fillable = ['sales_offer_id', 'transaction_id', 'status'];
    protected $table = 'sales_payments';
    use HasFactory;

    public function saleOffer()
    {
        return $this->belongsTo(Sale_offer::class, 'sales_offer_id');
    }
}
