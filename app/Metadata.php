<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'url';
    protected $keyType = 'string';

    protected $fillable = ['url', 'title', 'description', 'image', 'expires_at'];
    protected $visible = ['url', 'title', 'description', 'image', 'expires_at', 'tags'];

    protected $dates = ['created_at', 'updated_at', 'expires_at', 'error_at'];

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
