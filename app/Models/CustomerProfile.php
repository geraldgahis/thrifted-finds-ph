<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;


#[Fillable(['user_id'])]
class CustomerProfile extends Model
{
    public function user()
    {
        // This profile belongs to a specific user
        return $this->belongsTo(User::class);
    }
}
