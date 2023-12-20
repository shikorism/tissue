<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentProvider extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'host';
    protected $keyType = 'string';

    protected $fillable = [
        'host',
        'robots',
        'robots_cached_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'robots_cached_at',
    ];
}
