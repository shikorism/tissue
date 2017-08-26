<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ejaculation extends Model
{
    //

    protected $fillable = [
        'user_id', 'ejaculated_date',
        'note', 'geo_latitude', 'geo_longitude',
        'is_private'
    ];
}
