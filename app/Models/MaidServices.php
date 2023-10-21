<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaidServices extends Model
{
    use HasFactory;

    public function maid()
    {
        return $this->belongsTo(User::class, 'maid_id');
    }

    public function service()
    {
        return $this->belongsTo(MasterServices::class, 'service_id');
    }
}
