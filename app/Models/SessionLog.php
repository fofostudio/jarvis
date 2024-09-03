<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'first_login',
        'last_logout',
        'login_count',
        'ip_address',
        'user_agent',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'first_login' => 'datetime',
        'last_logout' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
