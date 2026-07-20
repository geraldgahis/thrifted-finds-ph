<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
        'category_id', 'title', 'slug', 'description', 
        'brand_id', 'size_tag', 'measurements', 'condition', 
        'price', 'status'
    ])]
class Product extends Model
{
    // A product belongs to one category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // A product has many images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Helper to get just the primary image
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
