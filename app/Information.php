<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use SoftDeletes;

    const CATEGORIES = [
        0 => ['label' => 'お知らせ', 'class' => 'badge-info', 'slug' => 'news'],
        1 => ['label' => 'アップデート', 'class' => 'badge-success', 'slug' => 'update'],
        2 => ['label' => '不具合情報', 'class' => 'badge-danger', 'slug' => 'bug'],
        3 => ['label' => 'メンテナンス', 'class' => 'badge-warning', 'slug' => 'maintenance']
    ];

    protected $fillable = [
        'category', 'pinned', 'title', 'content'
    ];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];
}
