<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'idusuario',
        'email',
        'password_mask',
        'successful',
        'status',
        'ip_address',
        'user_agent',
        'attempted_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario', 'id');
    }
}
