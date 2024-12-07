<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    const PERSONAL_TOKEN_PER_USER_LIMIT = 20;

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
        'private_likes',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
         // 'email_verified_at' => 'datetime',
    ];

    /**
     * このユーザのメールアドレスから、Gravatarの画像URLを生成します。
     * @param int $size 画像サイズ
     * @return string Gravatar 画像URL
     */
    public function getProfileImageUrl($size = 30): string
    {
        $hash = md5(strtolower(trim($this->email)));

        return '//www.gravatar.com/avatar/' . $hash . '?s=' . $size . '&d=retro';
    }

    /**
     * このユーザがログイン中のユーザ本人であるかをチェックします。
     * @return bool 本人かどうか
     */
    public function isMe()
    {
        return Auth::check() && $this->id === Auth::user()->id;
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function ejaculations()
    {
        return $this->hasMany(Ejaculation::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function checkinWebhooks()
    {
        return $this->hasMany(CheckinWebhook::class);
    }

    public function tagFilters()
    {
        return $this->hasMany(TagFilter::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function checkinSummary(): ?array
    {
        $total = $this->ejaculations()->count();
        if ($total === 0) {
            return null;
        }

        // 現在のオナ禁セッションの経過時間
        $latestEjaculation = Ejaculation::select('ejaculated_date')
            ->where('user_id', $this->id)
            ->orderByDesc('ejaculated_date')
            ->first();
        if (!empty($latestEjaculation)) {
            $currentSession = (int) $latestEjaculation->ejaculated_date->diffInSeconds(Carbon::now());
        } else {
            $currentSession = null;
        }

        // 概況欄のデータ取得
        $average = 0;
        $divisor = 0;
        $averageSources = DB::select(<<<'SQL'
SELECT
  extract(epoch from ejaculated_date - lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)) AS span,
  discard_elapsed_time
FROM
  ejaculations
WHERE
  user_id = :user_id
ORDER BY
  ejaculated_date DESC
LIMIT
  30
SQL
            , ['user_id' => $this->id]);
        foreach ($averageSources as $item) {
            // 経過時間記録対象外のレコードがあったら、それより古いデータは平均の計算に加えない
            if ($item->discard_elapsed_time) {
                break;
            }
            $average += $item->span;
            $divisor++;
        }
        if ($divisor > 0) {
            $average /= $divisor;
        }

        $summary = DB::select(<<<'SQL'
SELECT
  max(span) AS longest,
  min(span) AS shortest,
  sum(span) AS total_times
FROM
  (
    SELECT
      extract(epoch from ejaculated_date - lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)) AS span,
      discard_elapsed_time
    FROM
      ejaculations
    WHERE
      user_id = :user_id
    ORDER BY
      ejaculated_date DESC
  ) AS temp
WHERE
  discard_elapsed_time = FALSE
SQL
            , ['user_id' => $this->id]);

        return [
            'current_session_elapsed' => $currentSession,
            'total_checkins' => $total,
            'total_times' => (int) $summary[0]->total_times,
            'average_interval' => $average,
            'longest_interval' => (int) $summary[0]->longest,
            'shortest_interval' => (int) $summary[0]->shortest,
        ];
    }

    public function scopeAdministrators($query)
    {
        return $query->where('is_admin', true);
    }
}
