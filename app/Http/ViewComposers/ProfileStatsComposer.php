<?php

namespace App\Http\ViewComposers;

use App\Ejaculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileStatsComposer
{
    public function __construct()
    {
    }

    public function compose(View $view)
    {
        // user変数に値が設定されてない場合は落とす
        if (!$view->offsetExists('user')) {
            throw new \LogicException('View data "user" was not exist.');
        }
        $user = $view->offsetGet('user');

        // 現在のオナ禁セッションの経過時間
        $latestEjaculation = Ejaculation::select('ejaculated_date')
            ->where('user_id', $user->id)
            ->orderByDesc('ejaculated_date')
            ->first();
        if (!empty($latestEjaculation)) {
            $currentSession = $latestEjaculation->ejaculated_date
                ->diff(Carbon::now())
                ->format('%a日 %h時間 %i分');
        } else {
            $currentSession = null;
        }

        // 概況欄のデータ取得
        $summary = DB::select(<<<'SQL'
SELECT
  avg(span) AS average,
  max(span) AS longest,
  min(span) AS shortest,
  sum(span) AS total_times,
  count(*) AS total_checkins
FROM
  (
    SELECT
      extract(epoch from ejaculated_date - lead(ejaculated_date, 1, NULL) OVER (ORDER BY ejaculated_date DESC)) AS span
    FROM
      ejaculations
    WHERE
      user_id = :user_id
    ORDER BY
      ejaculated_date DESC
  ) AS temp
SQL
            , ['user_id' => $user->id]);

        $view->with(compact('latestEjaculation', 'currentSession', 'summary'));
    }
}
