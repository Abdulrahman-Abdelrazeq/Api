<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'user_id', 'rating', 'comment'];

    public function users()
    {
        return $this->hasOne(User::class);
    }
}
