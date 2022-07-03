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
        /** @var \App\User $user */
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
        $average = 0;
        $divisor = 0;
        $medianSources = [];
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
            , ['user_id' => $user->id]);
        foreach ($averageSources as $item) {
            // 経過時間記録対象外のレコードがあったら、それより古いデータは平均の計算に加えない
            if ($item->discard_elapsed_time) {
                break;
            }
            $average += $item->span;
            $divisor++;
            $medianSources[] = $item->span;
        }
        if ($divisor > 0) {
            $average /= $divisor;
        }
        $median = collect($medianSources)->sort()->median();

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
            , ['user_id' => $user->id]);

        $total = $user->ejaculations()->count();

        $view->with(compact('latestEjaculation', 'currentSession', 'average', 'median', 'summary', 'total'));
    }
}
