@extends('layouts.admin')

@section('title', '通報')

@section('tab-content')
    <div class="container">
        <h2>通報 #{{ $report->id }}</h2>
        <hr>
        <dl>
            <dt>通報日時</dt>
            <dd>{{ $report->created_at->format('Y/m/d H:i:s') }}</dd>
            <dt>通報したユーザー</dt>
            <dd><a href="{{ route('user.profile', ['name' => $report->reporter->name]) }}">{{ $report->reporter->display_name }} (&commat;{{ $report->reporter->name }})</a></dd>
            <dt>通報対象ユーザー</dt>
            <dd>
                <a href="{{ route('user.profile', ['name' => $report->targetUser->name]) }}">{{ $report->targetUser->display_name }} (&commat;{{ $report->targetUser->name }})</a>
                <br>
                被通報回数: {{ $strikes }} 回
            </dd>
            <dt>理由</dt>
            <dd>{{ $report->violatedRule?->summary ?? 'その他' }}</dd>
            <dt>コメント</dt>
            <dd>
                @if (empty($report->comment))
                    &mdash;
                @else
                    {{ $report->comment }}
                @endif
            </dd>
        </dl>
        @if ($report->ejaculation !== null)
            <hr>
            <h4>対象のチェックイン</h4>
            <div class="card">
                <div class="card-body">
                    @component('components.ejaculation', ['ejaculation' => $report->ejaculation, 'showActions' => false])
                    @endcomponent
                </div>
            </div>
        @endif
        <hr>
        <h4>モデレーションの実行</h4>
        <form action="{{ route('admin.reports.action', ['report' => $report]) }}" method="post">
            @csrf

            <div class="form-group">
                <label class="d-block"><input type="radio" name="action" value="{{ App\ModerationAction::SuspendCheckin }}">チェックインを強制非表示にする</label>
                <label class="d-block"><input type="radio" name="action" value="{{ App\ModerationAction::SuspendUser }}">ユーザーを強制非表示にする</label>
            </div>

            <div class="form-group">
                <label for="comment">メッセージ</label>
                <textarea class="form-control {{ $errors->has('comment') ? ' is-invalid' : '' }}" id="comment" name="comment" rows="3" maxlength="1000">{{ old('comment') }}</textarea>
                <small class="form-text text-muted">
                    最大 1,000 文字
                </small>

                @if ($errors->has('comment'))
                    <div class="invalid-feedback">{{ $errors->first('comment') }}</div>
                @endif
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox mb-2">
                    <input id="send_email" name="send_email" class="custom-control-input" type="checkbox" value="1" {{ (old('send_email') ?? true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="send_email">ユーザーにメールで通知</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary d-block">実行</button>
        </form>
        <hr>
        <h4>モデレーション履歴</h4>
        <table class="table table-sm">
            <thead>
            <tr>
                <th>日時</th>
                <th>操作者</th>
                <th>操作</th>
                <th>メッセージ</th>
                <th>メール通知</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($report->moderations as $moderation)
                <tr>
                    <td>{{ $moderation->created_at->format('Y/m/d H:i:s') }}</td>
                    <td><a href="{{ route('user.profile', ['name' => $moderation->moderator->name]) }}">&commat;{{ $moderation->moderator->name }}</a></td>
                    <td>
                        @switch($moderation->action)
                            @case(\App\ModerationAction::SuspendCheckin)
                                チェックイン非表示
                                @break
                            @case(\App\ModerationAction::SuspendUser)
                                ユーザー非表示
                                @break
                        @endswitch
                    </td>
                    <td>
                        @if (empty($moderation->comment))
                            -
                        @else
                            {{ nl2br(e($moderation->comment)) }}
                        @endif
                    </td>
                    <td>
                        {{ $moderation->send_email ? '送信済' : '-' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
