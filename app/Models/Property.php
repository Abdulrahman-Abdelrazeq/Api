<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable =["user_id","title","city","district","street","type","description","status","area","beds","baths"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function rents()
    {
        return $this->hasOne(PropertyRent::class);
    }

    public function sales()
    {
        return $this->hasOne(PropertySale::class);
    }

    public function price()
    {
        // Check if the property has a related sale
        $sale = $this->sales()->first();
        
        // Check if the property has a related rent
        $rent = $this->rents()->first();

        // Return the price from either sale or rent, prioritizing sale
        return $sale ? $sale->price : ($rent ? $rent->price : null);
    }
}
