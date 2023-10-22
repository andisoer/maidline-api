<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaidHourlyPrice extends Model
{
    use HasFactory;

    protected $fillable = ['maid_id', 'price'];

    public function maid()
    {
        return $this->belongsTo(User::class);
    }
}
