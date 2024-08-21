<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'access_mode', 'color', 'logo'];

    public function girls()
    {
        return $this->hasMany(Girl::class);
    }
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'girls', 'platform_id', 'group_id')->distinct();
    }
}
