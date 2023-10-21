<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterServices extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_name',
    ];

    public function maid()
    {
        return $this->belongsToMany(User::class, 'maid_services', 'service_id', 'user_id')->withTimestamps();
    }
}
