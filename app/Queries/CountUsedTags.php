<?php
declare(strict_types=1);

namespace App\Queries;

use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CountUsedTags
{
    private ?CarbonInterface $since = null;
    private ?CarbonInterface $until = null;
    private bool $includesMetadata = false;

    public function __construct(private ?User $operator, private User $user)
    {
    }

    public function since(?CarbonInterface $since): self
    {
        $this->since = $since;

        return $this;
    }

    public function until(?CarbonInterface $until): self
    {
        $this->until = $until;

        return $this;
    }

    public function setIncludesMetadata(bool $includesMetadata): self
    {
        $this->includesMetadata = $includesMetadata;

        return $this;
    }

    public function query()
    {
        if ($this->includesMetadata) {
            return $this->queryToEjaculationsAndMetadata();
        } else {
            return $this->queryToEjaculations();
        }
    }

    private function queryToEjaculations()
    {
        $dateCondition = [
            ['ejaculated_date', '<', $this->until ?: now()->addMonth()->startOfMonth()],
        ];
        if ($this->since !== null) {
            $dateCondition[] = ['ejaculated_date', '>=', $this->since];
        }

        $query = DB::table('ejaculations')
            ->join('ejaculation_tag', 'ejaculations.id', '=', 'ejaculation_tag.ejaculation_id')
            ->join('tags', 'ejaculation_tag.tag_id', '=', 'tags.id')
            ->selectRaw('tags.name, count(*) as count')
            ->where('ejaculations.user_id', $this->user->id)
            ->where($dateCondition);
        if ($this->operator === null || $this->user->id !== $this->operator->id) {
            $query = $query->where('ejaculations.is_private', false);
        }

        return $query->groupBy('tags.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function queryToEjaculationsAndMetadata()
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

        $dateSince = $this->since ?: Carbon::create(1);
        $dateUntil = $this->until ?: now()->addMonth()->startOfMonth();

        return DB::select($sql, [
            $this->user->id, false, $this->operator !== null && $this->user->id === $this->operator->id, $dateSince, $dateUntil,
            $this->user->id, false, $this->operator !== null && $this->user->id === $this->operator->id, $dateSince, $dateUntil
        ]);
    }
}
