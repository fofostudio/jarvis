<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order', 'monthly_goal', 'monthly_points', 'task_description'];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function calculateTotalPoints()
    {
        return $this->groups()->sum('points');
    }
}
