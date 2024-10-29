<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRuleStoreRequest;
use App\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index()
    {
        $rules = Rule::query()
            ->sorted()
            ->get();

        return view('admin.rule.index')->with(compact('rules'));
    }

    public function create()
    {
        return view('admin.rule.create');
    }

    public function store(AdminRuleStoreRequest $request)
    {
        Rule::create($request->validated());

        return redirect()->route('admin.rule')->with('status', '通報理由を作成しました。');
    }

    public function edit(Rule $rule)
    {
        return view('admin.rule.edit')->with(compact('rule'));
    }

    public function update(AdminRuleStoreRequest $request, Rule $rule)
    {
        $rule->fill($request->validated())->save();

        return redirect()->route('admin.rule.edit', compact('rule'))->with('status', '通報理由を更新しました。');
    }

    public function destroy(Rule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.rule')->with('status', '通報理由を削除しました。');
    }
}
