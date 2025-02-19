<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class UserController extends Controller
{
    public function redirectMypage()
    {
        return redirect()->route('user.profile', ['name' => auth()->user()->name]);
    }

    public function profile($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        // チェックインの取得
        $query = Ejaculation::select(DB::raw(
            <<<'SQL'
ejaculations.id,
user_id,
ejaculated_date,
note,
is_private,
is_too_sensitive,
link,
source,
discard_elapsed_time,
to_char(before_dates.before_date, 'YYYY/MM/DD HH24:MI') AS before_date,
to_char(ejaculated_date - before_dates.before_date, 'FMDDD日 FMHH24時間 FMMI分') AS ejaculated_span
SQL
        ))
            ->joinSub($this->queryBeforeEjaculatedDates(), 'before_dates', 'before_dates.id', '=', 'ejaculations.id')
            ->where('user_id', $user->id);
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        $ejaculations = $query->orderBy('ejaculated_date', 'desc')
            ->with('user', 'tags')
            ->withLikes()
            ->withMutedStatus()
            ->paginate(20);

        // よく使っているタグ
        $tags = $this->countUsedTags($user);

        // シコ草
        $countByDayQuery = $this->countEjaculationByDay($user)
            ->where('ejaculated_date', '>=', now()->startOfMonth()->subMonths(9))
            ->where('ejaculated_date', '<', now()->addMonth()->startOfMonth())
            ->get();
        $countByDay = [];
        foreach ($countByDayQuery as $data) {
            $countByDay[] = [
                't' => Carbon::createFromFormat('Y/m/d', $data->date, 'UTC')->startOfDay()->timestamp,
                'count' => $data->count
            ];
        }

        return view('user.profile')->with(compact('user', 'ejaculations', 'tags', 'countByDay'));
    }

    public function stats($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        $availableMonths = $this->makeStatsAvailableMonths($user);
        $graphData = $this->makeGraphData($user);
        $tags = $this->countUsedTags($user);
        $tagsIncludesMetadata = collect($this->countUsedTagsIncludesMetadata($user));

        return view('user.stats.index')
            ->with(compact('user', 'graphData', 'availableMonths', 'tags', 'tagsIncludesMetadata'));
    }

    public function statsYearly(Request $request, $name, $year)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }
        $comparePrev = $request->query('compare') === 'prev';

        $validator = Validator::make(compact('year'), [
            'year' => 'required|date_format:Y'
        ]);
        if ($validator->fails()) {
            return redirect()->route('user.stats', compact('name'));
        }

        $availableMonths = $this->makeStatsAvailableMonths($user);
        if (!isset($availableMonths[$year])) {
            return redirect()->route('user.stats', compact('name'));
        }

        $dateSince = Carbon::createFromDate($year, 1, 1, config('app.timezone'))->startOfDay();
        $dateUntil = Carbon::createFromDate($year, 1, 1, config('app.timezone'))->addYear()->startOfDay();
        $graphData = $this->makeGraphData($user, $dateSince, $dateUntil);
        $tags = $this->countUsedTags($user, $dateSince, $dateUntil);
        $tagsIncludesMetadata = collect($this->countUsedTagsIncludesMetadata($user, $dateSince, $dateUntil));
        $mostFrequentlyUsedRanking = collect($this->countMostFrequentlyUsedOkazu($user, $dateSince, $dateUntil));

        $compareData = null;
        if ($comparePrev) {
            $compareDateSince = Carbon::createFromDate($year - 1, 1, 1, config('app.timezone'))->startOfDay();
            $compareDateUntil = Carbon::createFromDate($year - 1, 1, 1, config('app.timezone'))->addYear()->startOfDay();
            $compareData = $this->makeGraphData($user, $compareDateSince, $compareDateUntil);
        }

        return view('user.stats.yearly')
            ->with(compact('user', 'graphData', 'availableMonths', 'tags', 'tagsIncludesMetadata', 'mostFrequentlyUsedRanking', 'compareData'))
            ->with('currentYear', (int) $year);
    }

    public function statsMonthly($name, $year, $month)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        $validator = Validator::make(compact('year', 'month'), [
            'year' => 'required|date_format:Y',
            'month' => 'required|date_format:m'
        ]);
        if ($validator->fails()) {
            return redirect()->route('user.stats.yearly', compact('name', 'year'));
        }

        $availableMonths = $this->makeStatsAvailableMonths($user);
        if (!isset($availableMonths[$year]) || !in_array($month, $availableMonths[$year], false)) {
            return redirect()->route('user.stats.yearly', compact('name', 'year'));
        }

        $dateSince = Carbon::createFromDate($year, $month, 1, config('app.timezone'))->startOfDay();
        $dateUntil = Carbon::createFromDate($year, $month, 1, config('app.timezone'))->addMonth()->startOfDay();
        $graphData = $this->makeGraphData($user, $dateSince, $dateUntil);
        $tags = $this->countUsedTags($user, $dateSince, $dateUntil);
        $tagsIncludesMetadata = collect($this->countUsedTagsIncludesMetadata($user, $dateSince, $dateUntil));

        return view('user.stats.monthly')
            ->with(compact('user', 'graphData', 'availableMonths', 'tags', 'tagsIncludesMetadata'))
            ->with('currentYear', (int) $year)
            ->with('currentMonth', (int) $month);
    }

    public function okazu($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        // チェックインの取得
        $query = Ejaculation::select(DB::raw(
            <<<'SQL'
ejaculations.id,
user_id,
ejaculated_date,
note,
is_private,
is_too_sensitive,
link,
source,
discard_elapsed_time,
to_char(before_dates.before_date, 'YYYY/MM/DD HH24:MI') AS before_date,
to_char(ejaculated_date - before_dates.before_date, 'FMDDD日 FMHH24時間 FMMI分') AS ejaculated_span
SQL
        ))
            ->joinSub($this->queryBeforeEjaculatedDates(), 'before_dates', 'before_dates.id', '=', 'ejaculations.id')
            ->where('user_id', $user->id)
            ->where('link', '<>', '');
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('is_private', false);
        }
        $ejaculations = $query->orderBy('ejaculated_date', 'desc')
            ->with('user', 'tags')
            ->withLikes()
            ->withMutedStatus()
            ->paginate(20);

        return view('user.profile')->with(compact('user', 'ejaculations'));
    }

    public function likes($name)
    {
        $user = User::where('name', $name)->first();
        if (empty($user)) {
            abort(404);
        }

        $likes = $user->likes()
            ->select('likes.*')
            ->orderBy('likes.id', 'desc')
            ->with([
                'ejaculation' => function ($query) {
                    $query->with('user', 'tags')->withMutedStatus();
                }
            ])
            ->join('ejaculations', 'likes.ejaculation_id', '=', 'ejaculations.id')
            ->leftJoinSub(Ejaculation::queryTagFilterMatches(), 'tag_filter_matches', 'ejaculations.id', '=', 'tag_filter_matches.ejaculation_id')
            ->where(function ($query) {
                $query
                    ->where(function ($query) {
                        $query->where('ejaculations.user_id', Auth::id())
                            ->orWhere('ejaculations.is_private', false);
                    })->where(function ($query) {
                        $query->where('ejaculations.user_id', Auth::id())
                            ->orWhereRaw('COALESCE(tag_filter_matches.is_removed_by_tag_filter, 0) < 1');
                    });
            })
            ->paginate(20);

        return view('user.likes')->with(compact('user', 'likes'));
    }

    private function makeStatsAvailableMonths(User $user): array
    {
        $availableMonths = [];
        $oldest = $user->ejaculations()->orderBy('ejaculated_date')->first();
        if (isset($oldest)) {
            $oldestMonth = $oldest->ejaculated_date->startOfMonth();
            $currentMonth = now()->startOfMonth();
            for ($month = $currentMonth; $oldestMonth <= $currentMonth; $month = $month->subMonth()) {
                if (!isset($availableMonths[$month->year])) {
                    $availableMonths[$month->year] = [];
                }
                $availableMonths[$month->year][] = $month->month;
            }
        }

        return $availableMonths;
    }

    private function makeGraphData(User $user, CarbonInterface $dateSince = null, CarbonInterface $dateUntil = null): array
    {
        if ($dateUntil === null) {
            $dateUntil = now()->addMonth()->startOfMonth();
        }
        $dateCondition = [
            ['ejaculated_date', '<', $dateUntil],
        ];
        if ($dateSince !== null) {
            $dateCondition[] = ['ejaculated_date', '>=', $dateSince];
        }

        $groupByDay = $this->countEjaculationByDay($user)
            ->where($dateCondition)
            ->get();

        $groupByHour = Ejaculation::select(DB::raw(
            <<<'SQL'
to_char(ejaculated_date, 'HH24') AS "hour",
count(*) AS "count"
SQL
        ))
            ->where('user_id', $user->id)
            ->where($dateCondition)
            ->groupBy(DB::raw("to_char(ejaculated_date, 'HH24')"))
            ->orderBy(DB::raw('1'))
            ->get();

        $dailySum = [];
        $monthlySum = [];
        $yearlySum = [];
        $dowSum = array_fill(0, 7, 0);
        $hourlySum = array_fill(0, 24, 0);

        // 年間グラフ用の配列初期化
        if ($groupByDay->first() !== null) {
            $year = Carbon::createFromFormat('Y/m/d', $groupByDay->first()->date)->year;
            $currentYear = date('Y');
            for (; $year <= $currentYear; $year++) {
                $yearlySum[$year] = 0;
            }
        }

        foreach ($groupByDay as $data) {
            $date = Carbon::createFromFormat('Y/m/d', $data->date, 'UTC')->startOfDay();
            $yearAndMonth = $date->format('Y/m');

            $dailySum[] = ['t' => $date->timestamp, 'count' => $data->count];
            $yearlySum[$date->year] += $data->count;
            $dowSum[$date->dayOfWeek] += $data->count;
            $monthlySum[$yearAndMonth] = ($monthlySum[$yearAndMonth] ?? 0) + $data->count;
        }

        foreach ($groupByHour as $data) {
            $hour = (int)$data->hour;
            $hourlySum[$hour] += $data->count;
        }

        return [
            'dailySum' => $dailySum,
            'dowSum' => $dowSum,
            'monthlySum' => $monthlySum,
            'yearlyKey' => array_keys($yearlySum),
            'yearlySum' => array_values($yearlySum),
            'hourlyKey' => array_keys($hourlySum),
            'hourlySum' => array_values($hourlySum),
        ];
    }

    private function countEjaculationByDay(User $user)
    {
        return Ejaculation::select(DB::raw(
            <<<'SQL'
to_char(ejaculated_date, 'YYYY/MM/DD') AS "date",
count(*) AS "count"
SQL
        ))
            ->where('user_id', $user->id)
            ->groupBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"))
            ->orderBy(DB::raw("to_char(ejaculated_date, 'YYYY/MM/DD')"));
    }

    private function countUsedTags(User $user, CarbonInterface $dateSince = null, CarbonInterface $dateUntil = null)
    {
        if ($dateUntil === null) {
            $dateUntil = now()->addMonth()->startOfMonth();
        }
        $dateCondition = [
            ['ejaculated_date', '<', $dateUntil],
        ];
        if ($dateSince !== null) {
            $dateCondition[] = ['ejaculated_date', '>=', $dateSince];
        }

        $query = DB::table('ejaculations')
            ->join('ejaculation_tag', 'ejaculations.id', '=', 'ejaculation_tag.ejaculation_id')
            ->join('tags', 'ejaculation_tag.tag_id', '=', 'tags.id')
            ->selectRaw('tags.name, count(*) as count')
            ->where('ejaculations.user_id', $user->id)
            ->where($dateCondition);
        if (!Auth::check() || $user->id !== Auth::id()) {
            $query = $query->where('ejaculations.is_private', false);
        }

        return $query->groupBy('tags.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function countUsedTagsIncludesMetadata(User $user, CarbonInterface $dateSince = null, CarbonInterface $dateUntil = null)
    {
        $sql = <<<SQL
SELECT tg.name, count(*) count
FROM (
    SELECT DISTINCT ej.id ej_id, tg.id tg_id
    FROM ejaculations ej
    INNER JOIN (SELECT id FROM ejaculations WHERE user_id = ? AND is_private IN (?, ?) AND ejaculated_date >= ? AND ejaculated_date < ?) ej2 ON ej.id = ej2.id
    INNER JOIN ejaculation_tag et ON ej.id = et.ejaculation_id
    INNER JOIN tags tg ON et.tag_id = tg.id
    UNION
    SELECT DISTINCT ej.id ej_id, tg.id tg_id
    FROM ejaculations ej
    INNER JOIN (SELECT id FROM ejaculations WHERE user_id = ? AND is_private IN (?, ?) AND ejaculated_date >= ? AND ejaculated_date < ?) ej2 ON ej.id = ej2.id
    INNER JOIN metadata_tag mt ON ej.link = mt.metadata_url
    INNER JOIN tags tg ON mt.tag_id = tg.id
) ej_with_tag_id
INNER JOIN tags tg ON ej_with_tag_id.tg_id = tg.id
GROUP BY tg.name
ORDER BY count DESC
LIMIT 10
SQL;

        if ($dateSince === null) {
            $dateSince = Carbon::create(1);
        }
        if ($dateUntil === null) {
            $dateUntil = now()->addMonth()->startOfMonth();
        }

        return DB::select($sql, [
            $user->id, false, Auth::check() && $user->id === Auth::id(), $dateSince, $dateUntil,
            $user->id, false, Auth::check() && $user->id === Auth::id(), $dateSince, $dateUntil
        ]);
    }

    private function queryBeforeEjaculatedDates()
    {
        return DB::table('ejaculations')->selectRaw(
            <<<'SQL'
id,
(select ejaculated_date from ejaculations e2 where e2.ejaculated_date < ejaculations.ejaculated_date and e2.user_id = ejaculations.user_id order by e2.ejaculated_date desc limit 1) AS before_date
SQL
        );
    }

    private function countMostFrequentlyUsedOkazu(User $user, CarbonInterface $dateSince = null, CarbonInterface $dateUntil = null)
    {
        $sql = <<<SQL
SELECT normalized_link, count(*) as count
FROM ejaculations e
WHERE user_id = ? AND is_private IN (?, ?) AND ejaculated_date >= ? AND ejaculated_date < ? AND normalized_link <> ''
GROUP BY normalized_link HAVING count(*) >= 2
ORDER BY count DESC, normalized_link
LIMIT 10
SQL;

        if ($dateSince === null) {
            $dateSince = Carbon::create(1);
        }
        if ($dateUntil === null) {
            $dateUntil = now()->addMonth()->startOfMonth();
        }

        return DB::select($sql, [
            $user->id, false, Auth::check() && $user->id === Auth::id(), $dateSince, $dateUntil
        ]);
    }
}
