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

    const SOURCE_WEB = 'web';
    const SOURCE_CSV = 'csv';
    const SOURCE_WEBHOOK = 'webhook';

    protected $fillable = [
        'user_id', 'ejaculated_date',
        'note', 'geo_latitude', 'geo_longitude', 'link', 'source',
        'is_private', 'is_too_sensitive',
        'checkin_webhook_id'
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

    public function scopeOnlyWebCheckin(Builder $query)
    {
        return $query->where('ejaculations.source', Ejaculation::SOURCE_WEB);
    }

    public function scopeWithLikes(Builder $query)
    {
        if (Auth::check()) {
            // TODO - このスコープを使うことでlikesが常に直近10件で絞られるのは汚染されすぎ感がある。別名を付与できないか？
            //      - (ejaculation_id, user_id) でユニークなわけですが、is_liked はサブクエリ発行させるのとLeft JoinしてNULLかどうかで結果を見るのどっちがいいんでしょうね
            return $query
                ->with([
                    'likes' => function ($query) {
                        $query->latest()->take(10);
                    },
                    'likes.user' => function ($query) {
                        $query->where('is_protected', false)
                            ->where('private_likes', false)
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
                        $query->where('is_protected', false)
                            ->where('private_likes', false);
                    }
                ])
                ->withCount('likes')
                ->addSelect(DB::raw('0 as is_liked'));
        }
    }

    /**
     * このチェックインと同じ情報を流用してチェックインするためのURLを生成
     * @return string
     */
    public function makeCheckinURL(): string
    {
        return route('checkin', [
            'link' => $this->link,
            'tags' => $this->textTags(),
            'is_too_sensitive' => $this->is_too_sensitive,
        ]);
    }
}
