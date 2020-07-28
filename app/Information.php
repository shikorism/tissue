<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use SoftDeletes;

    const CATEGORIES = [
        0 => ['label' => 'お知らせ', 'class' => 'bg-info'],
        1 => ['label' => 'アップデート', 'class' => 'bg-success'],
        2 => ['label' => '不具合情報', 'class' => 'bg-danger'],
        3 => ['label' => 'メンテナンス', 'class' => 'bg-warning']
    ];

    protected $fillable = [
        'category', 'pinned', 'title', 'content'
    ];

    protected $dates = ['deleted_at'];
}
