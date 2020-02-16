<?php

namespace Tests\Unit\Io;

use App\Exceptions\CsvImportException;
use App\Io\CheckinCsvImporter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CheckinCsvImporterTest extends TestCase
{
    use RefreshDatabase;

    public function testIncompatibleCharsetEUCJP()
    {
        $user = factory(User::class)->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('文字コード判定に失敗しました。UTF-8 (BOM無し) または Shift_JIS をお使いください。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/incompatible-charset.eucjp.csv');
        $importer->execute();
    }

    /**
     * @dataProvider provideMissingTime
     */
    public function testMissingTime($filename)
    {
        $user = factory(User::class)->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('日時列は必須です。');

        $importer = new CheckinCsvImporter($user, $filename);
        $importer->execute();
    }

    public function provideMissingTime()
    {
        return [
            'UTF8' => [__DIR__ . '/../../fixture/Csv/missing-time.utf8.csv'],
            'SJIS' => [__DIR__ . '/../../fixture/Csv/missing-time.sjis.csv'],
        ];
    }

    /**
     * @dataProvider provideDate
     */
    public function testDate($expectedDate, $filename)
    {
        $user = factory(User::class)->create();

        $importer = new CheckinCsvImporter($user, $filename);
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertEquals($expectedDate, $ejaculation->ejaculated_date);
    }

    public function provideDate()
    {
        $date = Carbon::create(2020, 1, 23, 6, 1, 0, 'Asia/Tokyo');

        return [
            'Zero, Second, UTF8' => [$date, __DIR__ . '/../../fixture/Csv/date.utf8.csv'],
            'NoZero, Second, UTF8' => [$date, __DIR__ . '/../../fixture/Csv/date-nozero.utf8.csv'],
            'Zero, NoSecond, UTF8' => [$date, __DIR__ . '/../../fixture/Csv/date-nosecond.utf8.csv'],
            'NoZero, NoSecond, UTF8' => [$date, __DIR__ . '/../../fixture/Csv/date-nozero-nosecond.utf8.csv'],
        ];
    }

    public function testInvalidDate()
    {
        $user = factory(User::class)->create();

        try {
            $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/invalid-date.utf8.csv');
            $importer->execute();
        } catch (CsvImportException $e) {
            $this->assertSame('2 行 : 日時 は 2000/01/01 00:00 〜 2099/12/31 23:59 の間のみ対応しています。', $e->getErrors()[0]);
            $this->assertSame('3 行 : 日時 は 2000/01/01 00:00 〜 2099/12/31 23:59 の間のみ対応しています。', $e->getErrors()[1]);
            $this->assertSame('4 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[2]);
            $this->assertSame('5 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[3]);
            $this->assertSame('6 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[4]);
            $this->assertSame('7 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[5]);
            $this->assertSame('8 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[6]);
            $this->assertSame('9 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[7]);
            $this->assertSame('10 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[8]);
            $this->assertSame('11 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[9]);
            $this->assertSame('12 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[10]);
            $this->assertSame('13 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[11]);
            $this->assertSame('14 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[12]);
            $this->assertSame('15 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[13]);
            $this->assertSame('16 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[14]);
            $this->assertSame('17 行 : 日時 の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[15]);

            return;
        }

        $this->fail('期待する例外が発生していません');
    }
}
