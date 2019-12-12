<?php

namespace App\Http\Controllers;

use App\Ejaculation;
use App\Events\LinkDiscovered;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class EjaculationController extends Controller
{
    public function create(Request $request)
    {
        $defaults = [
            'date' => $request->input('date', date('Y/m/d')),
            'time' => $request->input('time', date('H:i')),
            'link' => $request->input('link', ''),
            'tags' => $request->input('tags', ''),
            'note' => $request->input('note', ''),
            'is_private' => $request->input('is_private', 0) == 1,
            'is_too_sensitive' => $request->input('is_too_sensitive', 0) == 1
        ];

        return view('ejaculation.checkin')->with('defaults', $defaults);
    }

    public function store(Request $request)
    {
        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'date' => 'required|date_format:Y/m/d',
            'time' => 'required|date_format:H:i',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|string',
        ])->after(function ($validator) use ($request, $inputs) {
            // 日時の重複チェック
            if (!$validator->errors()->hasAny(['date', 'time'])) {
                $dt = $inputs['date'] . ' ' . $inputs['time'];
                if (Ejaculation::where(['user_id' => Auth::id(), 'ejaculated_date' => $dt])->count()) {
                    $validator->errors()->add('datetime', '既にこの日時にチェックインしているため、登録できません。');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->route('checkin')->withErrors($validator)->withInput();
        }

        $ejaculation = Ejaculation::create([
            'user_id' => Auth::id(),
            'ejaculated_date' => Carbon::createFromFormat('Y/m/d H:i', $inputs['date'] . ' ' . $inputs['time']),
            'note' => $inputs['note'] ?? '',
            'link' => $inputs['link'] ?? '',
            'is_private' => $request->has('is_private') ?? false,
            'is_too_sensitive' => $request->has('is_too_sensitive') ?? false
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

        // 1つ前のチェックインからの経過時間を求める
        $previousEjaculation = Ejaculation::select('ejaculated_date')
            ->where('user_id', $ejaculation->user_id)
            ->where('ejaculated_date', '<', $ejaculation->ejaculated_date)
            ->orderByDesc('ejaculated_date')
            ->first();
        if (!empty($previousEjaculation)) {
            $ejaculatedSpan = $ejaculation->ejaculated_date
                ->diff($previousEjaculation->ejaculated_date)
                ->format('%a日 %h時間 %i分');
        } else {
            $ejaculatedSpan = null;
        }

        return view('ejaculation.show')->with(compact('user', 'ejaculation', 'ejaculatedSpan'));
    }

    public function edit($id)
    {
        $ejaculation = Ejaculation::findOrFail($id);

        $this->authorize('edit', $ejaculation);

        return view('ejaculation.edit')->with(compact('ejaculation'));
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
        });

        if ($validator->fails()) {
            return redirect()->route('checkin.edit', ['id' => $id])->withErrors($validator)->withInput();
        }

        $ejaculation->fill([
            'ejaculated_date' => Carbon::createFromFormat('Y/m/d H:i', $inputs['date'] . ' ' . $inputs['time']),
            'note' => $inputs['note'] ?? '',
            'link' => $inputs['link'] ?? '',
            'is_private' => $request->has('is_private') ?? false,
            'is_too_sensitive' => $request->has('is_too_sensitive') ?? false
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

        if (!empty($ejaculation->link)) {
            event(new LinkDiscovered($ejaculation->link));
        }

        return redirect()->route('checkin.show', ['id' => $ejaculation->id])->with('status', 'チェックインを修正しました！');
    }

    public function destroy($id)
    {
        $ejaculation = Ejaculation::findOrFail($id);

        $this->authorize('edit', $ejaculation);

        $user = User::findOrFail($ejaculation->user_id);
        $ejaculation->tags()->detach();
        $ejaculation->delete();

        return redirect()->route('user.profile', ['name' => $user->name])->with('status', '削除しました。');
    }
}
