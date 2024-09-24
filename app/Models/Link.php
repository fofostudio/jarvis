<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'url', 'favicon', 'category_link_id'];

    public function categoryLink()
    {
        return $this->belongsTo(CategoryLink::class);
    }
}
