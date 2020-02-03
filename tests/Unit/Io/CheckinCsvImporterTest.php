<?php

namespace Tests\Unit\Io;

use App\Exceptions\CsvImportException;
use App\Io\CheckinCsvImporter;
use Tests\TestCase;

class CheckinCsvImporterTest extends TestCase
{
    public function testIncompatibleCharsetEUCJP()
    {
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('文字コード判定に失敗しました。UTF-8 (BOM無し) または Shift_JIS をお使いください。');

        $importer = new CheckinCsvImporter(__DIR__ . '/../../fixture/Csv/incompatible-charset.eucjp.csv');
        $importer->execute();
    }

    public function testMissingTimeUTF8()
    {
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('日時列は必須です。');

        $importer = new CheckinCsvImporter(__DIR__ . '/../../fixture/Csv/missing-time.utf8.csv');
        $importer->execute();
    }

    public function testMissingTimeSJIS()
    {
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('日時列は必須です。');

        $importer = new CheckinCsvImporter(__DIR__ . '/../../fixture/Csv/missing-time.sjis.csv');
        $importer->execute();
    }
}
