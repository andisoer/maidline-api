<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaidExperience extends Model
{
    use HasFactory;

    public function maid()
    {
        return $this->belongsTo(User::class, 'maid_id');
    }
}
