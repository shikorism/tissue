<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Timelines;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PublicTimeline extends Controller
{
    public function __invoke(Request $request)
    {
        $inputs = $request->validate([
            'per_page' => 'nullable|integer|between:10,100',
        ]);

        $ejaculations = Ejaculation::join('users', 'users.id', '=', 'ejaculations.user_id')
            ->where('users.is_protected', false)
            ->where('ejaculations.is_private', false)
            ->where('ejaculations.link', '<>', '')
            ->where('ejaculations.ejaculated_date', '<=', Carbon::now())
            ->orderBy('ejaculations.ejaculated_date', 'desc')
            ->select('ejaculations.*')
            ->with('user', 'tags')
            ->withLikes()
            ->withMutedStatus()
            ->visibleToTimeline()
            ->paginate($inputs['per_page'] ?? 20);

        return response()->fromPaginator($ejaculations, EjaculationResource::class);
    }
}
