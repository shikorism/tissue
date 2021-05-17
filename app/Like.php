<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Like extends Model
{
    use HasEagerLimit;

    protected $fillable = ['user_id', 'ejaculation_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ejaculation()
    {
        return $this->belongsTo(Ejaculation::class)->withLikes();
    }
}
