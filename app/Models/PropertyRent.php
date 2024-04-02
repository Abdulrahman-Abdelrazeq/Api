<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRent extends Model
{
    use HasFactory;

    protected $table = 'property_rents';

    protected $fillable = [
        'property_id', 'lister_id', 'period', 'price', 'updated_price'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function lister()
    {
        return $this->belongsTo(User::class, 'lister_id');
    }
}
