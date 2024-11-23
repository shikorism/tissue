<x-mail::message>
{{ $moderation->targetUser->display_name }} 様

いつも Tissue をご利用いただき、ありがとうございます。

@switch ($moderation->action)
@case (App\ModerationAction::SuspendCheckin)
投稿された一部のチェックインを「非公開」に変更いたしましたので、ご案内申し上げます。

**対象**  
[{{ route('checkin.show', ['id' => $moderation->ejaculation_id]) }}]({{ route('checkin.show', ['id' => $moderation->ejaculation_id]) }})
@break
@case (App\ModerationAction::SuspendUser)
アカウントのチェックイン公開設定を「非公開」に変更いたしましたので、ご案内申し上げます。
@break
@endswitch

@if (!empty($moderation->comment))
{!! str_replace(["\r\n", "\r", "\n"], "  \n", $moderation->comment) !!}
@endif

公開チェックインには記載できるコンテンツに一定の制限があります。投稿ガイドラインの再度の確認をお願いします。

なお、問題が解消されていない状態での再公開や、同様の投稿が繰り返された場合にはアカウントの永久停止を含めた対応が行われる可能性があります。

以上、よろしくお願いいたします。
</x-mail::message>
