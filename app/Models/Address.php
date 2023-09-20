<?php

namespace App\Models;

use App\Traits\SafelySave;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use SafelySave;

    protected $fillable = [
        'region', 'district', 'address', 'user_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'user_id'];
}
