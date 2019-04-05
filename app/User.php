<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'is_protected', 'accept_analytics',
        'display_name', 'description',
        'twitter_id', 'twitter_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * このユーザのメールアドレスから、Gravatarの画像URLを生成します。
     * @param int $size 画像サイズ
     * @return string Gravatar 画像URL
     */
    public function getProfileImageUrl($size = 30): string
    {
        $hash = md5(strtolower(trim($this->email)));

        return '//www.gravatar.com/avatar/' . $hash . '?s=' . $size;
    }

    /**
     * このユーザがログイン中のユーザ本人であるかをチェックします。
     * @return bool 本人かどうか
     */
    public function isMe()
    {
        return Auth::check() && $this->id === Auth::user()->id;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
