<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promos extends Model
{
    use HasFactory;

    protected $casts = [
        'discount_percentage' => 'int',
    ];

    protected $fillable = ['title', 'description', 'discount_percentage', 'valid_from', 'valid_to'];
}
