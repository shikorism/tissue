<?php

namespace App\Io;

use App\Ejaculation;
use App\Exceptions\CsvImportException;
use App\Rules\CsvDateTime;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class CheckinCsvImporter
{
    /** @var User Target user */
    private $user;
    /** @var string CSV filename */
    private $filename;

    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
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
                $ejaculation = new Ejaculation(['user_id' => $this->user->id]);

                $validator = Validator::make($record, [
                    '日時' => ['required', new CsvDateTime()],
                    'ノート' => 'nullable|string|max:500',
                    'オカズリンク' => 'nullable|url|max:2000',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $message) {
                        $errors[] = "{$offset} 行 : {$message}";
                    }
                    continue;
                }

                $ejaculation->ejaculated_date = Carbon::createFromFormat('!Y/m/d H:i+', $record['日時']);
                $ejaculation->note = $record['ノート'] ?? '';
                $ejaculation->link = $record['オカズリンク'] ?? '';

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
