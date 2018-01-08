<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    protected $fillable = [
        'name'
    ];

    public function ejaculations()
    {
        return $this->belongsToMany('App\Ejaculation')->withTimestamps();
    }
}
