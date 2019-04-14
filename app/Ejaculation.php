<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Ejaculation extends Model
{
    use HasEagerLimit;

    protected $fillable = [
        'user_id', 'ejaculated_date',
        'note', 'geo_latitude', 'geo_longitude', 'link',
        'is_private'
    ];

    protected $dates = [
        'ejaculated_date'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function textTags()
    {
        return implode(' ', $this->tags->map(function ($v) {
            return $v->name;
        })->all());
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function scopeWithLikes(Builder $query)
    {
        if (Auth::check()) {
            // (ejaculation_id, user_id) でユニークなわけですが、サブクエリ発行させるのとLeft JoinしてNULLかどうかで結果を見るのどっちがいいんでしょうね
            return $query
                ->with([
                    'likes' => function ($query) {
                        $query->latest()->take(10);
                    },
                    'likes.user' => function ($query) {
                        $query->where('is_protected', false)
                            ->orWhere('id', Auth::id());
                    }
                ])
                ->withCount([
                    'likes',
                    'likes as is_liked' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }
                ]);
        } else {
            return $query
                ->with([
                    'likes' => function ($query) {
                        $query->latest()->take(10);
                    },
                    'likes.user' => function ($query) {
                        $query->where('is_protected', false);
                    }
                ])
                ->withCount('likes')
                ->addSelect(DB::raw('0 as is_liked'));
        }
    }
}
