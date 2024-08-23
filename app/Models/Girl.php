<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Girl extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'internal_id', 'username', 'password', 'platform_id', 'group_id'];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function workPlanAssignments()
{
    return $this->hasMany(WorkPlanAssignment::class);
}
}
