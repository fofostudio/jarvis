<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'group_category_id'];

    public function girls()
    {
        return $this->hasMany(Girl::class);
    }
    public function points()
    {
        return $this->hasMany(Point::class);
    }
    public function groupCategory()
{
    return $this->belongsTo(GroupCategory::class);
}


    public function operators()
    {
        return $this->belongsToMany(User::class, 'group_operator')
            ->withPivot('shift')
            ->withTimestamps();
    }
    public function workPlans()
    {
        return $this->hasMany(WorkPlan::class);
    }
    public function platforms()
    {
        return $this->belongsToMany(Platform::class, 'girls', 'group_id', 'platform_id')->distinct();
    }
    public function groupOperators()
    {
        return $this->hasMany(GroupOperator::class);
    }
    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
    public function category()
    {
        return $this->belongsTo(GroupCategory::class, 'group_category_id');
    }
}
