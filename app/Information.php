<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use SoftDeletes;

    const CATEGORIES = [
        0 => ['label' => 'お知らせ', 'class' => 'badge-info'],
        1 => ['label' => 'アップデート', 'class' => 'badge-success'],
        2 => ['label' => '不具合情報', 'class' => 'badge-danger'],
        3 => ['label' => 'メンテナンス', 'class' => 'badge-warning']
    ];

    protected $fillable = [
        'category', 'pinned', 'title', 'content'
    ];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];
}
