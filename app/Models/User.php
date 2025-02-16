<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(UserRoles::class, 'role_id'); // 'role_id' is the foreign key in the users table
    }

    public function services()
    {
        return $this->belongsToMany(MasterServices::class, 'maid_services', 'maid_id', 'service_id')->withTimestamps();
    }

    public function experiences()
    {
        return $this->hasMany(MaidExperience::class, 'maid_id');
    }

    public function schedules()
    {
        return $this->hasMany(MaidSchedule::class, 'maid_id');
    }

    public function hourlyPrice()
    {
        return $this->hasOne(MaidHourlyPrice::class, 'maid_id');
    }
}
