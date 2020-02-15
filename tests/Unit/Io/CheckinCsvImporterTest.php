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
}
