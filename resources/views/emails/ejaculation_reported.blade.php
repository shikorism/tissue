<x-mail::message>
{{ $report->reporter->name }} さんがチェックイン #{{ $report->ejaculation_id }} を報告しました。

- 報告者: [{{ $report->reporter->name }}]({{ route('user.profile', ['name' => $report->reporter->name]) }})
- チェックイン投稿者: [{{ $report->targetUser->name }}]({{ route('user.profile', ['name' => $report->targetUser->name]) }})
- チェックイン: [{{ route('checkin.show', ['id' => $report->ejaculation_id]) }}]({{ route('checkin.show', ['id' => $report->ejaculation_id]) }})
- 報告理由: {{ $report->violatedRule?->summary ?? 'その他' }}

詳細は管理画面から確認してください。

[{{ route('admin.reports.show', ['report' => $report]) }}]({{ route('admin.reports.show', ['report' => $report]) }})
</x-mail::message>
