@extends('layouts.admin')

@section('title', '通報理由')

@section('tab-content')
    <div class="container">
        <h2>通報理由</h2>
        <hr>
        <div class="d-flex mb-3">
            <a href="{{ route('admin.rule.create') }}" class="btn btn-primary">新規作成</a>
        </div>
        @if (!$rules->isEmpty())
            <div class="list-group mt-3">
                @foreach ($rules as $rule)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 mr-2">
                            {{ $rule->summary }}
                        </div>
                        <div class="ml-2">
                            <a href="{{ route('admin.rule.edit', ['rule' => $rule]) }}" class="btn btn-outline-secondary">編集</a>
                            <button class="btn btn-outline-danger" type="button" data-target="#deleteRuleModal" data-id="{{ $rule->id }}">削除</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @component('components.modal', ['id' => 'deleteRuleModal'])
        @slot('title')
            削除確認
        @endslot
        ルールを削除してもよろしいですか？
        @slot('footer')
            <form action="{{ route('admin.rule.destroy', ['rule' => '@']) }}" method="post">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        @endslot
    @endcomponent
@endsection

@push('script')
    @vite('resources/assets/js/admin/rules.ts')
@endpush
