<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['summary', 'position'];

    public function scopeSorted($query)
    {
        return $query->orderBy('position')->orderBy('id');
    }
}
