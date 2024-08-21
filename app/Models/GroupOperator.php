<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupOperator extends Model
{
    use HasFactory;

    protected $table = 'group_operator';

    protected $fillable = [
        'group_id',
        'user_id',
        'shift'
    ];

    /**
     * Get the group that the operator is assigned to.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user (operator) assigned to the group.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include assignments for a specific shift.
     */
    public function scopeShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }
}
