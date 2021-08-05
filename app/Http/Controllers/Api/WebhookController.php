<?php

namespace App\Http\Controllers\Api;

use App\CheckinWebhook;
use App\Ejaculation;
use App\Events\LinkDiscovered;
use App\Http\Controllers\Controller;
use App\Http\Resources\EjaculationResource;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebhookController extends Controller
{
    public function checkin(CheckinWebhook $webhook, Request $request)
    {
        if (!$webhook->isAvailable()) {
            return response()->json([
                'status' => 404,
                'error' => [
                    'message' => 'The webhook is unavailable'
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'checked_in_at' => 'nullable|date|after_or_equal:2000-01-01 00:00:00|before_or_equal:2099-12-31 23:59:59',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|array|max:40',
            'tags.*' => ['string', 'not_regex:/[\s\r\n]/u', 'max:255'],
            'is_private' => 'nullable|boolean',
            'is_too_sensitive' => 'nullable|boolean',
            'discard_elapsed_time' => 'nullable|boolean',
        ], [
            'tags.*.not_regex' => 'The :attribute cannot contain spaces, tabs and newlines.'
        ]);

        try {
            $inputs = $validator->validate();
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'error' => [
                    'message' => 'Validation failed',
                    'violations' => $validator->errors()->all(),
                ]
            ], 422);
        }

        $ejaculatedDate = empty($inputs['checked_in_at']) ? now() : new Carbon($inputs['checked_in_at']);
        $ejaculatedDate = $ejaculatedDate->setTimezone(date_default_timezone_get())->startOfMinute();
        if (Ejaculation::where(['user_id' => $webhook->user_id, 'ejaculated_date' => $ejaculatedDate])->count()) {
            return response()->json([
                'status' => 422,
                'error' => [
                    'message' => 'Checkin already exists in this time',
                ]
            ], 422);
        }

        $ejaculation = DB::transaction(function () use ($inputs, $webhook, $ejaculatedDate) {
            $ejaculation = Ejaculation::create([
                'user_id' => $webhook->user_id,
                'ejaculated_date' => $ejaculatedDate,
                'note' => $inputs['note'] ?? '',
                'link' => $inputs['link'] ?? '',
                'source' => Ejaculation::SOURCE_WEBHOOK,
                'is_private' => (bool)($inputs['is_private'] ?? false),
                'is_too_sensitive' => (bool)($inputs['is_too_sensitive'] ?? false),
                'discard_elapsed_time' => (bool)($inputs['discard_elapsed_time'] ?? false),
                'checkin_webhook_id' => $webhook->id
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

        return response()->json([
            'status' => 200,
            'checkin' => new EjaculationResource($ejaculation)
        ]);
    }
}
