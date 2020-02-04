<?php

namespace App\Io;

use App\Ejaculation;
use App\Exceptions\CsvImportException;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class CheckinCsvImporter
{
    /** @var string CSV filename */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function execute()
    {
        // Guess charset
        $head = file_get_contents($this->filename, false, null, 0, 1024);
        $charset = mb_detect_encoding($head, ['ASCII', 'UTF-8', 'SJIS-win'], true);
        if (array_search($charset, ['UTF-8', 'SJIS-win'], true) === false) {
            throw new CsvImportException(['文字コード判定に失敗しました。UTF-8 (BOM無し) または Shift_JIS をお使いください。']);
        }

        // Open CSV
        $csv = Reader::createFromPath($this->filename, 'r');
        $csv->setHeaderOffset(0);
        if ($charset === 'SJIS-win') {
            $csv->addStreamFilter('convert.mbstring.encoding.SJIS-win:UTF-8');
        }

        // Import
        DB::transaction(function () use ($csv) {
            $errors = [];

            if (!in_array('日時', $csv->getHeader(), true)) {
                $errors[] = '日時列は必須です。';
                throw new CsvImportException($errors);
            }

            foreach ($csv->getRecords() as $offset => $record) {
                $ejaculation = new Ejaculation();

                $checkinAt = $record['日時'] ?? null;
                if (empty($checkinAt)) {
                    $errors[] = "{$offset} 行 : 日時列は必須です。";
                    continue;
                }
                if (preg_match('/\A20\d{2}[-/](1[0-2]|0?\d)[-/](0?\d|[1-2]\d|3[01]) (0?\d|1\d|2[0-4]):(0?\d|[1-5]\d)(:(0?\d|[1-5]\d))?\z/', $checkinAt) !== 1) {
                    $errors[] = "{$offset} 行 : 日時列の書式が正しくありません。";
                    continue;
                }
                $checkinAt = str_replace('/', '-', $checkinAt);
                try {
                    $ejaculation->ejaculated_date = Carbon::createFromFormat('!Y-m-d H:i+', $checkinAt);
                } catch (\InvalidArgumentException $e) {
                    $errors[] = "{$offset} 行 : 日時列に不正な値が入力されています。";
                    continue;
                }

                if (!empty($record['ノート'])) {
                    $note = $record['ノート'];

                    if (mb_strlen($note) > 500) {
                        $errors[] = "{$offset} 行 : ノート列は500文字以内にしてください。";
                        continue;
                    }

                    $ejaculation->note = $note;
                }

                if (!empty($record['オカズリンク'])) {
                    $link = $record['オカズリンク'];

                    if (mb_strlen($link) > 2000) {
                        $errors[] = "{$offset} 行 : オカズリンク列は500文字以内にしてください。";
                        continue;
                    }

                    // TODO: URL Validation

                    $ejaculation->link = $link;
                }

                $tagIds = [];
                for ($i = 1; $i <= 32; $i++) {
                    $column = 'タグ' . $i;
                    if (empty($record[$column])) {
                        break;
                    } else {
                        $tag = trim($record[$column]);

                        if (empty($tag)) {
                            break;
                        }
                        if (mb_strlen($tag) > 255) {
                            $errors[] = "{$offset} 行 : {$column}列は255文字以内にしてください。";
                            continue 2;
                        }

                        $tag = Tag::firstOrCreate(['name' => $tag]);
                        $tagIds[] = $tag->id;
                    }
                }
                $ejaculation->tags()->sync($tagIds);

                $ejaculation->save();
            }

            if (!empty($errors)) {
                throw new CsvImportException($errors);
            }
        });
    }
}
