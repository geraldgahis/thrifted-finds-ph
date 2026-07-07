<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug'])]
class Category extends Model
{
    // A category can have many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
