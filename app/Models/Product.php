<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_product_id',
        'user_id'
    ];

    /**
     * Get the category that owns the product.
     */
    public function categoryProduct()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category name or 'N/A' if no category is assigned.
     */
    public function getCategoryNameAttribute()
    {
        return $this->categoryProduct->name ?? 'N/A';
    }
}
