<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'int',
        'total_amount' => 'int',
        'discount_amount' => 'int',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maid()
    {
        return $this->belongsTo(User::class, 'maid_id');
    }

    public function schedule()
    {
        return $this->belongsTo(MaidSchedule::class, 'schedule_id');
    }
}
