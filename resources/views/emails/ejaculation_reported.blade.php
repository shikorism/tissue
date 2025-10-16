<x-mail::message>
{{ $report->reporter->name }} さんがチェックイン #{{ $report->ejaculation_id }} を報告しました。

- 報告者: [{{ $report->reporter->name }}]({{ url('/user/' . $report->reporter->name) }})
- チェックイン投稿者: [{{ $report->targetUser->name }}]({{ url('/user' . $report->targetUser->name) }})
- チェックイン: [{{ url('/checkin/' . $report->ejaculation_id) }}]({{ url('/checkin/' . $report->ejaculation_id) }})
- 報告理由: {{ $report->violatedRule?->summary ?? 'その他' }}

詳細は管理画面から確認してください。

[{{ route('admin.reports.show', ['report' => $report]) }}]({{ route('admin.reports.show', ['report' => $report]) }})
</x-mail::message>
