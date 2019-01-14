<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ejaculation extends Model
{
    //

    protected $fillable = [
        'user_id', 'ejaculated_date',
        'note', 'geo_latitude', 'geo_longitude', 'link',
        'is_private'
    ];

    protected $dates = [
        'ejaculated_date'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function textTags()
    {
        return implode(' ', $this->tags->map(function ($v) {
            return $v->name;
        })->all());
    }
}
