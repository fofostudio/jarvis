<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'identification', 'birth_date', 'phone', 'address', 'neighborhood', 'entry_date'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'entry_date' => 'date',
    ];
    public function sessionLogs()
    {
        return $this->hasMany(SessionLog::class);
    }

    public function breakLogs()
    {
        return $this->hasMany(BreakLog::class);
    }

    public function operativeReports()
    {
        return $this->hasMany(OperativeReport::class);
    }


    /**
     * Get the operative reports audited by the user.
     */
    public function auditedReports()
    {
        return $this->hasMany(OperativeReport::class, 'auditor_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_operator')->withPivot('shift')->withTimestamps();
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }
    public function groupOperators()
    {
        return $this->hasMany(GroupOperator::class);
    }

    public function platforms()
    {
        return $this->hasManyThrough(
            Platform::class,
            Girl::class,
            'group_id', // Clave foránea en la tabla girls
            'id', // Clave primaria en la tabla platforms
            'id', // Clave primaria en la tabla users
            'platform_id' // Clave foránea en la tabla girls
        )->distinct();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


}
