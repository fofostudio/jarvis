<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function girls()
    {
        return $this->hasMany(Girl::class);
    }
    public function points()
    {
        return $this->hasMany(Point::class);
    }

   public function operators()
    {
        return $this->belongsToMany(User::class, 'group_operator')
                    ->withPivot('shift')
                    ->withTimestamps();

    }
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'girls', 'group_id', 'platform_id')->distinct();
    }
    public function groupOperators()
    {
        return $this->hasMany(GroupOperator::class);
    }
}
