<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Events\LinkDiscovered;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class EjaculationController extends Controller
{
    public function create(Request $request)
    {
        $tags = old('tags') ?? $request->input('tags', '');
        if (!empty($tags)) {
            $tags = explode(' ', $tags);
        }

        $errors = $request->session()->get('errors');
        $initialState = [
            'mode' => 'create',
            'fields' => [
                'date' => old('date') ?? $request->input('date', date('Y/m/d')),
                'time' => old('time') ?? $request->input('time', date('H:i')),
                'link' => old('link') ?? $request->input('link', ''),
                'tags' => $tags,
                'note' => old('note') ?? $request->input('note', ''),
                'is_private' => old('is_private') ?? $request->input('is_private', 0) == 1,
                'is_too_sensitive' => old('is_too_sensitive') ?? $request->input('is_too_sensitive', 0) == 1,
                'is_realtime' => old('is_realtime', true),
                'discard_elapsed_time' => old('discard_elapsed_time') ?? $request->input('discard_elapsed_time') == 1,
            ],
            'errors' => isset($errors) ? $errors->getMessages() : null
        ];

        return view('ejaculation.checkin')->with('initialState', $initialState);
    }

    public function store(Request $request)
    {
        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'date' => 'required_without:is_realtime|date_format:Y/m/d',
            'time' => 'required_without:is_realtime|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|string',
        ])->after(function ($validator) use ($request, $inputs) {
            // 日時の重複チェック
            if (!$validator->errors()->hasAny(['date', 'time'])) {
                if (isset($inputs['date']) && isset($inputs['time'])) {
                    $dt = Carbon::createFromFormat('Y/m/d H:i', $inputs['date'] . ' ' . $inputs['time']);
                } else {
                    $dt = now();
                }
                $dt = $dt->startOfMinute();
                if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $dt])->count()) {
                    $validator->errors()->add('datetime', '既にこの日時にチェックインしているため、登録できません。');
                }
            }
            // タグの個数チェック
            if (!$validator->errors()->has('tags') && !empty($inputs['tags'])) {
                $tags = array_filter(explode(' ', $inputs['tags']), function ($v) {
                    return $v !== '';
                });
                if (count($tags) > 40) {
                    $validator->errors()->add('tags', 'タグは最大32個までです。');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->route('checkin')
                ->withErrors($validator)
                ->withInput(array_merge(['is_realtime' => false], $request->input()));
        }

        $ejaculation = DB::transaction(function () use ($request, $inputs) {
            if (isset($inputs['date']) && isset($inputs['time'])) {
                $ejaculatedDate = Carbon::createFromFormat('Y/m/d H:i', $inputs['date'] . ' ' . $inputs['time']);
            } else {
                $ejaculatedDate = now();
            }
            $ejaculatedDate = $ejaculatedDate->startOfMinute();
            $ejaculation = Ejaculation::create([
                'user_id' => Auth::id(),
                'ejaculated_date' => $ejaculatedDate,
                'note' => $inputs['note'] ?? '',
                'link' => $inputs['link'] ?? '',
                'source' => Ejaculation::SOURCE_WEB,
                'is_private' => $request->has('is_private') ?? false,
                'is_too_sensitive' => $request->has('is_too_sensitive') ?? false,
                'discard_elapsed_time' => $request->has('discard_elapsed_time') ?? false,
            ]);

            $tagIds = [];
            if (!empty($inputs['tags'])) {
                $tags = explode(' ', $inputs['tags']);
                foreach ($tags as $tag) {
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

        return redirect()->route('checkin.show', ['id' => $ejaculation->id])->with('status', 'チェックインしました！');
    }

    public function show($id)
    {
        $ejaculation = Ejaculation::where('id', $id)
            ->withLikes()
            ->firstOrFail();
        $user = User::findOrFail($ejaculation->user_id);

        return view('ejaculation.show')->with(compact('user', 'ejaculation'));
    }

    public function edit(Request $request, $id)
    {
        $ejaculation = Ejaculation::findOrFail($id);

        $this->authorize('edit', $ejaculation);

        if (old('tags') === null) {
            $tags = $ejaculation->tags->pluck('name');
        } else {
            $tags = old('tags');
            if (!empty($tags)) {
                $tags = explode(' ', $tags);
            }
        }

        $errors = $request->session()->get('errors');
        $initialState = [
            'mode' => 'update',
            'fields' => [
                'date' => old('date') ?? $ejaculation->ejaculated_date->format('Y/m/d'),
                'time' => old('time') ?? $ejaculation->ejaculated_date->format('H:i'),
                'link' => old('link') ?? $ejaculation->link,
                'tags' => $tags,
                'note' => old('note') ?? $ejaculation->note,
                'is_private' => is_bool(old('is_private')) ? old('is_private') : $ejaculation->is_private,
                'is_too_sensitive' => is_bool(old('is_too_sensitive')) ? old('is_too_sensitive') : $ejaculation->is_too_sensitive,
                'discard_elapsed_time' => is_bool(old('discard_elapsed_time')) ? old('discard_elapsed_time') : $ejaculation->discard_elapsed_time,
            ],
            'errors' => isset($errors) ? $errors->getMessages() : null
        ];

        return view('ejaculation.edit')->with(compact('ejaculation', 'initialState'));
    }

    public function update(Request $request, $id)
    {
        $ejaculation = Ejaculation::findOrFail($id);

        $this->authorize('edit', $ejaculation);

        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'date' => 'required|date_format:Y/m/d',
            'time' => 'required|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|string',
        ])->after(function ($validator) use ($id, $request, $inputs) {
            // 日時の重複チェック
            if (!$validator->errors()->hasAny(['date', 'time'])) {
                $dt = $inputs['date'] . ' ' . $inputs['time'];
                if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $dt])->where('id', '<>', $id)->count()) {
                    $validator->errors()->add('datetime', '既にこの日時にチェックインしているため、登録できません。');
                }
            }
            // タグの個数チェック
            if (!$validator->errors()->has('tags') && !empty($inputs['tags'])) {
                $tags = array_filter(explode(' ', $inputs['tags']), function ($v) {
                    return $v !== '';
                });
                if (count($tags) > 40) {
                    $validator->errors()->add('tags', 'タグは最大32個までです。');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->route('checkin.edit', ['id' => $id])->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($ejaculation, $request, $inputs) {
            $ejaculation->fill([
                'ejaculated_date' => Carbon::createFromFormat('Y/m/d H:i', $inputs['date'] . ' ' . $inputs['time']),
                'note' => $inputs['note'] ?? '',
                'link' => $inputs['link'] ?? '',
                'is_private' => $request->has('is_private') ?? false,
                'is_too_sensitive' => $request->has('is_too_sensitive') ?? false,
                'discard_elapsed_time' => $request->has('discard_elapsed_time') ?? false,
            ])->save();

            $tagIds = [];
            if (!empty($inputs['tags'])) {
                $tags = explode(' ', $inputs['tags']);
                foreach ($tags as $tag) {
                    if ($tag === '') {
                        continue;
                    }

                    $tag = Tag::firstOrCreate(['name' => $tag]);
                    $tagIds[] = $tag->id;
                }
            }
            $ejaculation->tags()->sync($tagIds);
        });

        if (!empty($ejaculation->link)) {
            event(new LinkDiscovered($ejaculation->link));
        }

        return redirect()->route('checkin.show', ['id' => $ejaculation->id])->with('status', 'チェックインを修正しました！');
    }

    public function tools()
    {
        return view('ejaculation.tools');
    }

    public function report()
    {

    }
}
