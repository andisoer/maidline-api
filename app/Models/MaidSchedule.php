<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaidSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'maid_id', 'start_date', 'end_date', 'duration', 'session'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maid()
    {
        return $this->belongsTo(User::class, 'maid_id');
    }
}
