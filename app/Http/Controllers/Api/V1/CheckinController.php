<?php

namespace App\Http\Controllers\Api\V1;

use App\Ejaculation;
use App\Events\LinkDiscovered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckinStoreRequest;
use App\Http\Resources\EjaculationResource;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CheckinController extends Controller
{
    public function index()
    {
        throw new \LogicException('not implemented yet');
    }

    public function store(CheckinStoreRequest $request)
    {
        $inputs = $request->validated();

        $ejaculatedDate = empty($inputs['checked_in_at']) ? now() : new Carbon($inputs['checked_in_at']);
        $ejaculatedDate = $ejaculatedDate->setTimezone(date_default_timezone_get())->startOfMinute();
        if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $ejaculatedDate])->count()) {
            throw new UnprocessableEntityHttpException('Checkin already exists in this time');
        }

        $ejaculation = DB::transaction(function () use ($inputs, $ejaculatedDate) {
            $ejaculation = Ejaculation::create([
                'user_id' => Auth::id(),
                'ejaculated_date' => $ejaculatedDate,
                'note' => $inputs['note'] ?? '',
                'link' => $inputs['link'] ?? '',
                'source' => Ejaculation::SOURCE_API,
                'is_private' => (bool)($inputs['is_private'] ?? false),
                'is_too_sensitive' => (bool)($inputs['is_too_sensitive'] ?? false),
                'discard_elapsed_time' => (bool)($inputs['discard_elapsed_time'] ?? false),
                'oauth_access_token_id' => Auth::user()->token()->id,
            ]);

            $tagIds = [];
            if (!empty($inputs['tags'])) {
                foreach ($inputs['tags'] as $tag) {
                    $tag = trim($tag);
                    if ($tag === '') {
                        continue;
                    }

                    $tag = Tag::firstOrCreate(['name' => $tag]);
                    $tagIds[] = $tag->id;
                }
            }
            $ejaculation->tags()->sync($tagIds);

            return $ejaculation;
        });

        if (!empty($ejaculation->link)) {
            event(new LinkDiscovered($ejaculation->link));
        }

        return new EjaculationResource($ejaculation);
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
