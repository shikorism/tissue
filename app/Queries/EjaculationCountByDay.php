<?php
declare(strict_types=1);

namespace App\Queries;

use App\Ejaculation;
use App\User;
use Illuminate\Support\Facades\DB;

class EjaculationCountByDay
{
    public function __construct(private User $user)
    {
    }

    public function query()
    {
        return Ejaculation::select(DB::raw(
            <<<'SQL'
to_char(ejaculated_date, 'YYYY-MM-DD') AS "date",
count(*) AS "count"
SQL
        ))
            ->where('user_id', $this->user->id)
            ->groupBy(DB::raw("to_char(ejaculated_date, 'YYYY-MM-DD')"))
            ->orderBy(DB::raw("to_char(ejaculated_date, 'YYYY-MM-DD')"));
    }
}
