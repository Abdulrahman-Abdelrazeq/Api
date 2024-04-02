<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rent_payment extends Model
{
    protected $fillable = ['rents_offer_id', 'transaction_id', 'status'];
    protected $table = 'rents_payments';
    use HasFactory;


    public function rentOffer()
    {
        return $this->belongsTo(Rent_offer::class, 'rents_offer_id');
    }
}
