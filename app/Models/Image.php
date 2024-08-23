<?php

namespace App\Models;

use App\Models\OperativeReport;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path'];

    public function operativeReport()
    {
        return $this->belongsTo(OperativeReport::class);
    }
}
