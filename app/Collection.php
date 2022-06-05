<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /** @var int ユーザーごとの作成数制限 */
    const PER_USER_LIMIT = 100;

    protected $fillable = [
        'title',
        'is_private',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CollectionItem::class);
    }
}
