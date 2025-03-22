<?php
declare(strict_types=1);

namespace App\Services;

use App\Ejaculation;
use App\Exceptions\CsvImportException;
use App\Rules\CsvDateTime;
use App\Rules\FuzzyBoolean;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Throwable;

class CheckinCsvImporter
{
    /** @var int 取り込み件数の上限 */
    private const IMPORT_LIMIT = 5000;

    /** @var User Target user */
    private $user;
    /** @var string CSV filename */
    private $filename;

    public function __construct(User $user, string $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
    }

    /**
     * インポート処理を実行します。
     * @return int 取り込んだ件数
     */
    public function execute(): int
    {
        // Guess charset
        $charset = $this->guessCharset($this->filename);

        // Open CSV
        $csv = Reader::createFromPath($this->filename, 'r');
        $csv->setHeaderOffset(0);
        if ($charset === 'SJIS-win') {
            $csv->appendStreamFilterOnRead('convert.mbstring.encoding.SJIS-win:UTF-8');
        }

        // Import
        return DB::transaction(function () use ($csv) {
            $alreadyImportedCount = $this->user->ejaculations()->where('ejaculations.source', Ejaculation::SOURCE_CSV)->count();
            $errors = [];

            if (!in_array('日時', $csv->getHeader(), true)) {
                $errors[] = '日時列は必須です。';
            }

            if (!empty($errors)) {
                throw new CsvImportException(...$errors);
            }

            $imported = 0;
            foreach ($csv->getRecords() as $offset => $record) {
                $line = $offset + 1;
                if (self::IMPORT_LIMIT <= $alreadyImportedCount + $imported) {
                    $limit = self::IMPORT_LIMIT;
                    $errors[] = "{$line} 行 : インポート機能で取り込めるデータは{$limit}件までに制限されています。これ以上取り込みできません。";
                    throw new CsvImportException(...$errors);
                }

                $ejaculation = new Ejaculation(['user_id' => $this->user->id]);

                $validator = Validator::make($record, [
                    '日時' => ['required', new CsvDateTime()],
                    'ノート' => 'nullable|string|max:500',
                    'オカズリンク' => 'nullable|url|max:2000',
                    '非公開' => ['nullable', new FuzzyBoolean()],
                    'センシティブ' => ['nullable', new FuzzyBoolean()],
                    '経過時間リセット' => ['nullable', new FuzzyBoolean()],
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->all() as $message) {
                        $errors[] = "{$line} 行 : {$message}";
                    }
                    continue;
                }

                $ejaculation->ejaculated_date = Carbon::createFromFormat('!Y/m/d H:i+', $record['日時']);
                $ejaculation->note = str_replace(["\r\n", "\r"], "\n", $record['ノート'] ?? '');
                $ejaculation->link = $record['オカズリンク'] ?? '';
                $ejaculation->source = Ejaculation::SOURCE_CSV;
                if (isset($record['非公開'])) {
                    $ejaculation->is_private = FuzzyBoolean::isTruthy($record['非公開']);
                }
                if (isset($record['センシティブ'])) {
                    $ejaculation->is_too_sensitive = FuzzyBoolean::isTruthy($record['センシティブ']);
                }
                if (isset($record['経過時間リセット'])) {
                    $ejaculation->discard_elapsed_time = FuzzyBoolean::isTruthy($record['経過時間リセット']);
                }

                try {
                    $tags = $this->parseTags($line, $record);
                } catch (CsvImportException $e) {
                    $errors = array_merge($errors, $e->getErrors());
                    continue;
                }

                DB::beginTransaction();
                try {
                    $ejaculation->save();
                    if (!empty($tags)) {
                        $ejaculation->tags()->sync(collect($tags)->pluck('id'));
                    }
                    DB::commit();
                    $imported++;
                } catch (QueryException $e) {
                    DB::rollBack();
                    if ($e->errorInfo[0] === '23505') {
                        $errors[] = "{$line} 行 : すでにこの日時のチェックインデータが存在します。";
                        continue;
                    }
                    throw $e;
                } catch (Throwable $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            if (!empty($errors)) {
                throw new CsvImportException(...$errors);
            }

            return $imported;
        });
    }

    /**
     * 指定されたファイルを読み込み、文字コードの判定を行います。
     * @param string $filename CSVファイル名
     * @param int $samplingLength ファイルの先頭から何バイトを判定に使用するかを指定
     * @return string 検出した文字コード (UTF-8, SJIS-win, ...)
     * @throws CsvImportException ファイルの読み込みに失敗した、文字コードを判定できなかった、または非対応文字コードを検出した場合にスロー
     */
    private function guessCharset(string $filename, int $samplingLength = 1024): string
    {
        $fp = fopen($filename, 'rb');
        if (!$fp) {
            throw new CsvImportException('CSVファイルの読み込み中にエラーが発生しました。');
        }

        try {
            $head = fread($fp, $samplingLength);
            if ($head === false) {
                throw new CsvImportException('CSVファイルの読み込み中にエラーが発生しました。');
            }

            for ($addition = 0; $addition < 4; $addition++) {
                $charset = mb_detect_encoding($head, ['ASCII', 'UTF-8', 'SJIS-win'], true);
                if ($charset) {
                    if (array_search($charset, ['UTF-8', 'SJIS-win'], true) === false) {
                        throw new CsvImportException('文字コード判定に失敗しました。UTF-8 (BOM無し) または Shift_JIS をお使いください。');
                    } else {
                        return $charset;
                    }
                }

                // 1バイト追加で読み込んだら、文字境界に到達して上手く判定できるかもしれない
                if (feof($fp)) {
                    break;
                }
                $next = fread($fp, 1);
                if ($next === false) {
                    throw new CsvImportException('CSVファイルの読み込み中にエラーが発生しました。');
                }
                $head .= $next;
            }

            throw new CsvImportException('文字コード判定に失敗しました。UTF-8 (BOM無し) または Shift_JIS をお使いください。');
        } finally {
            fclose($fp);
        }
    }

    /**
     * タグ列をパースします。
     * @param int $line 現在の行番号 (1 origin)
     * @param array $record 対象行のデータ
     * @return Tag[]
     * @throws CsvImportException バリデーションエラーが発生した場合にスロー
     */
    private function parseTags(int $line, array $record): array
    {
        $tags = [];
        foreach (array_keys($record) as $column) {
            if (preg_match('/\Aタグ\d{1,2}\z/u', $column) !== 1) {
                continue;
            }

            $tag = trim($record[$column] ?? '');
            if (empty($tag)) {
                continue;
            }
            if (mb_strlen($tag) > 255) {
                throw new CsvImportException("{$line} 行 : {$column}は255文字以内にしてください。");
            }
            if (strpos($tag, "\n") !== false) {
                throw new CsvImportException("{$line} 行 : {$column}に改行を含めることはできません。");
            }
            if (strpos($tag, ' ') !== false) {
                throw new CsvImportException("{$line} 行 : {$column}にスペースを含めることはできません。");
            }

            $tags[] = Tag::firstOrCreate(['name' => $tag]);
            if (count($tags) >= 40) {
                break;
            }
        }

        return $tags;
    }
}
