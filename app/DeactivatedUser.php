<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 削除済Userのユーザー名履歴
 */
class DeactivatedUser extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name'
    ];
}
