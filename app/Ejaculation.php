<?php

namespace App;

use App\Utilities\Formatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ejaculation extends Model
{
    use HasFactory;

    const SOURCE_WEB = 'web';
    const SOURCE_CSV = 'csv';
    const SOURCE_WEBHOOK = 'webhook';
    const SOURCE_API = 'api';

    protected $fillable = [
        'user_id', 'ejaculated_date',
        'note', 'geo_latitude', 'geo_longitude', 'link', 'source',
        'is_private', 'is_too_sensitive', 'discard_elapsed_time',
        'checkin_webhook_id', 'oauth_access_token_id',
    ];

    protected $casts = [
        'ejaculated_date' => 'datetime'
    ];

    /** @var bool|null */
    private $memoizedIsMuted;

    /**
     * 除外タグミュートのヒット情報を得るためのサブクエリを生成する。ejaculationsテーブルにleft joinして使うことを想定。
     * @return \Illuminate\Database\Query\Builder
     */
    public static function queryTagFilterMatches()
    {
        return DB::table('ejaculations')
            ->select('ejaculations.id as ejaculation_id', DB::raw('count(*) as is_removed_by_tag_filter'))
            ->join('related_ejaculation_tags', 'ejaculations.id', '=', 'related_ejaculation_tags.ejaculation_id')
            ->join('tags', 'related_ejaculation_tags.tag_id', '=', 'tags.id')
            ->join('tag_filters', function ($join) {
                $join->on('tags.normalized_name', '=', 'tag_filters.normalized_tag_name')
                    ->where([
                        'tag_filters.user_id' => Auth::id(),
                        'tag_filters.mode' => TagFilter::MODE_REMOVE
                    ]);
            })
            ->groupBy('ejaculations.id');
    }

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Ejaculation $ejaculation) {
            $ejaculation->normalized_link = app(Formatter::class)->normalizeUrl($ejaculation->link);
        });
        self::updating(function (Ejaculation $ejaculation) {
            $ejaculation->normalized_link = app(Formatter::class)->normalizeUrl($ejaculation->link);
        });
    }

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

    public function relatedTags()
    {
        return $this->belongsToMany(Tag::class, 'related_ejaculation_tags');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function scopeVisibleToTimeline(Builder $query)
    {
        return $query->whereIn('ejaculations.source', [Ejaculation::SOURCE_WEB, Ejaculation::SOURCE_WEBHOOK, Ejaculation::SOURCE_API]);
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

    public function scopeWithMutedStatus(Builder $query)
    {
        if (Auth::check()) {
            return $query
                ->withCount([
                    'relatedTags as is_muted' => function ($query) {
                        $query->join('tag_filters', function ($join) {
                            $join->on('tags.normalized_name', '=', 'tag_filters.normalized_tag_name')
                                ->where('tag_filters.user_id', Auth::id());
                        });
                    },
                ])
                ->removeMuted();
        } else {
            return $query->addSelect(DB::raw('0 AS is_muted'));
        }
    }

    public function scopeRemoveMuted(Builder $query)
    {
        if (Auth::check()) {
            return $query
                ->leftJoinSub(self::queryTagFilterMatches(), 'tag_filter_matches', 'ejaculations.id', '=', 'tag_filter_matches.ejaculation_id')
                ->where(function ($query) {
                    $query->where('ejaculations.user_id', Auth::id())
                        ->orWhereRaw('COALESCE(tag_filter_matches.is_removed_by_tag_filter, 0) < 1');
                });
        } else {
            return $query;
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
            'is_private' => $this->is_private,
            'is_too_sensitive' => $this->is_too_sensitive,
        ]);
    }

    public function ejaculatedSpan(): string
    {
        if (array_key_exists('ejaculated_span', $this->attributes)) {
            if ($this->ejaculated_span === null) {
                return '精通';
            }
            if ($this->discard_elapsed_time) {
                return '0日 0時間 0分'; // TODO: 気の効いたフレーズにする
            }

            return $this->ejaculated_span;
        } else {
            $previous = Ejaculation::select('ejaculated_date')
                ->where('user_id', $this->user_id)
                ->where('ejaculated_date', '<', $this->ejaculated_date)
                ->orderByDesc('ejaculated_date')
                ->first();

            if ($previous === null) {
                return '精通';
            }
            if ($this->discard_elapsed_time) {
                return '0日 0時間 0分';
            }

            return $this->ejaculated_date->diff($previous->ejaculated_date)->format('%a日 %h時間 %i分');
        }
    }

    public function isMuted(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        if ($this->memoizedIsMuted === null) {
            if (array_key_exists('is_muted', $this->attributes)) {
                $this->memoizedIsMuted = $this->is_muted !== 0;
            } else {
                $count = $this->relatedTags()
                    ->join('tag_filters', function ($join) {
                        $join->on('tags.normalized_name', '=', 'tag_filters.normalized_tag_name')
                            ->where('tag_filters.user_id', Auth::id());
                    })
                    ->count();
                $this->memoizedIsMuted = $count !== 0;
            }
        }

        return $this->memoizedIsMuted;
    }
}
