<?php

namespace App\Http\Controllers\Api;

use App\CheckinWebhook;
use App\Ejaculation;
use App\Events\LinkDiscovered;
use App\Http\Controllers\Controller;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'date' => 'nullable|date_format:Y/m/d',
            'time' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|array',
            'tags.*' => ['string', 'not_regex:/\s/u'],
            'is_private' => 'nullable|boolean',
            'is_too_sensitive' => 'nullable|boolean',
        ], [
            'tags.*.not_regex' => 'The :attribute cannot contain spaces.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'error' => [
                    'message' => 'Validation failed',
                    'violations' => $validator->errors()->all(),
                ]
            ]);
        }

        $ejaculatedDate = now()->startOfMinute();
        if (!empty($inputs['date'])) {
            $ejaculatedDate = $ejaculatedDate->setDateFrom(Carbon::createFromFormat('Y/m/d', $inputs['date']));
        }
        if (!empty($inputs['time'])) {
            $ejaculatedDate = $ejaculatedDate->setTimeFrom(Carbon::createFromFormat('H:i', $inputs['time']));
        }
        if (Ejaculation::where(['user_id' => $webhook->user_id, 'ejaculated_date' => $ejaculatedDate])->count()) {
            return response()->json([
                'status' => 422,
                'error' => [
                    'message' => 'Checkin already exists in this time',
                ]
            ]);
        }

        $ejaculation = Ejaculation::create([
            'user_id' => $webhook->user_id,
            'ejaculated_date' => $ejaculatedDate,
            'note' => $inputs['note'] ?? '',
            'link' => $inputs['link'] ?? '',
            'source' => Ejaculation::SOURCE_WEBHOOK,
            'is_private' => $request->has('is_private') ?? false,
            'is_too_sensitive' => $request->has('is_too_sensitive') ?? false
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

        if (!empty($ejaculation->link)) {
            event(new LinkDiscovered($ejaculation->link));
        }

        return response()->json([
            'status' => 200,
            'checkin' => $ejaculation
        ]);
    }
}
