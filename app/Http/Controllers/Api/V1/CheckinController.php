<?php

namespace App\Http\Controllers\Api\V1;

use App\Ejaculation;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckinController extends Controller
{
    public function index()
    {
        throw new \LogicException('not implemented yet');
    }

    public function store(Request $request)
    {
        throw new \LogicException('not implemented yet');
    }

    public function show(Ejaculation $checkin)
    {
        $owner = $checkin->user;
        if (!$owner->isMe()) {
            if ($owner->is_protected) {
                throw new AccessDeniedHttpException('このユーザはチェックイン履歴を公開していません');
            }
            if ($checkin->is_private) {
                throw new AccessDeniedHttpException('非公開チェックインのため、表示できません');
            }
        }

        return new EjaculationResource($checkin);
    }

    public function update(Request $request, Ejaculation $checkin)
    {
        throw new \LogicException('not implemented yet');
    }

    public function destroy($checkin)
    {
        $ejaculation = Ejaculation::find($checkin);

        if ($ejaculation !== null) {
            $this->authorize('edit', $ejaculation);

            DB::transaction(function () use ($ejaculation) {
                $ejaculation->tags()->detach();
                $ejaculation->delete();
            });
        }

        return response()->noContent();
    }
}
