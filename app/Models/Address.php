<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 
    'name',
    'phone',
    'province_state_postal_code',
    'street_name_house_number',
    'address_detail',
    'tag',
    'is_main'];

    protected $casts = [
        'is_main' => 'int',
        'user_id' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
