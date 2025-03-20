<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class CheckinCsvExporter
{
    /** @var User Target user */
    private $user;
    /** @var string Output filename */
    private $filename;
    /** @var string Output charset */
    private $charset;

    public function __construct(User $user, string $filename, string $charset)
    {
        $this->user = $user;
        $this->filename = $filename;
        $this->charset = $charset;
    }

    public function execute()
    {
        $csv = Writer::createFromPath($this->filename, 'wb');
        $csv->setEndOfLine("\r\n");
        if ($this->charset === 'SJIS-win') {
            $csv->appendStreamFilterOnWrite('convert.mbstring.encoding.UTF-8:SJIS-win');
        }

        $header = ['日時', 'ノート', 'オカズリンク', '非公開', 'センシティブ', '経過時間リセット'];
        for ($i = 1; $i <= 40; $i++) {
            $header[] = "タグ{$i}";
        }
        $csv->insertOne($header);

        DB::transaction(function () use ($csv) {
            // TODO: そんなに読み取り整合性を保つ努力はしていないのと、chunkの件数これでいいか分からない
            $this->user->ejaculations()->with('tags')->orderBy('ejaculated_date')
                ->chunk(1000, function ($ejaculations) use ($csv) {
                    foreach ($ejaculations as $ejaculation) {
                        $record = [
                            $ejaculation->ejaculated_date->format('Y/m/d H:i'),
                            $ejaculation->note,
                            $ejaculation->link,
                            self::formatBoolean($ejaculation->is_private),
                            self::formatBoolean($ejaculation->is_too_sensitive),
                            self::formatBoolean($ejaculation->discard_elapsed_time),
                        ];
                        foreach ($ejaculation->tags->take(40) as $tag) {
                            $record[] = $tag->name;
                        }
                        $csv->insertOne($record);
                    }
                });
        });
    }

    private static function formatBoolean($value): string
    {
        return $value ? 'true' : 'false';
    }
}
