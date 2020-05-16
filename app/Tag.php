<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //

    protected $fillable = [
        'name'
    ];
    protected $visible = [
        'name'
    ];

    public function ejaculations()
    {
        return $this->belongsToMany('App\Ejaculation')->withTimestamps();
    }

    public function metadata()
    {
        return $this->belongsToMany('App\Metadata')->withTimestamps();
    }
}
