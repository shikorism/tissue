<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    /** @var int ユーザーごとの作成数制限 */
    const PER_USER_LIMIT = 500;

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
