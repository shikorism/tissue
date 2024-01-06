<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Ejaculation;
use App\Exceptions\CsvImportException;
use App\Services\CheckinCsvImporter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CheckinCsvImporterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testIncompatibleCharsetEUCJP()
    {
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
        $user = User::factory()->create();

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
        $user = User::factory()->create();

        try {
            $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/invalid-date.utf8.csv');
            $importer->execute();
        } catch (CsvImportException $e) {
            $this->assertSame('2 行 : 日時は 2000/01/01 00:00 〜 2099/12/31 23:59 の間のみ対応しています。', $e->getErrors()[0]);
            $this->assertSame('3 行 : 日時は 2000/01/01 00:00 〜 2099/12/31 23:59 の間のみ対応しています。', $e->getErrors()[1]);
            $this->assertSame('4 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[2]);
            $this->assertSame('5 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[3]);
            $this->assertSame('6 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[4]);
            $this->assertSame('7 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[5]);
            $this->assertSame('8 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[6]);
            $this->assertSame('9 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[7]);
            $this->assertSame('10 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[8]);
            $this->assertSame('11 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[9]);
            $this->assertSame('12 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[10]);
            $this->assertSame('13 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[11]);
            $this->assertSame('14 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[12]);
            $this->assertSame('15 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[13]);
            $this->assertSame('16 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[14]);
            $this->assertSame('17 行 : 日時の形式は "年/月/日 時:分" にしてください。', $e->getErrors()[15]);

            return;
        }

        $this->fail('期待する例外が発生していません');
    }

    public function testNoteUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/note.utf8.csv');
        $importer->execute();
        $ejaculations = $user->ejaculations()->orderBy('ejaculated_date')->get();

        $this->assertCount(3, $ejaculations);
        $this->assertEquals('The quick brown fox jumps over the lazy dog. 素早い茶色の狐はのろまな犬を飛び越える', $ejaculations[0]->note);
        $this->assertEquals("The quick brown fox jumps over the lazy dog.\n素早い茶色の狐はのろまな犬を飛び越える", $ejaculations[1]->note);
        $this->assertEquals('The quick brown fox jumps over the "lazy" dog.', $ejaculations[2]->note);
    }

    /**
     * @dataProvider provideNoteOverLength
     */
    public function testNoteOverLength($filename)
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : ノートには500文字以下の文字列を指定してください。');

        $importer = new CheckinCsvImporter($user, $filename);
        $importer->execute();
    }

    public function provideNoteOverLength()
    {
        return [
            'ASCII Only, UTF8' => [__DIR__ . '/../../fixture/Csv/note-over-length.ascii.utf8.csv'],
            'JP, UTF8' => [__DIR__ . '/../../fixture/Csv/note-over-length.jp.utf8.csv'],
        ];
    }

    public function testLinkUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/link.utf8.csv');
        $importer->execute();
        $ejaculations = $user->ejaculations()->orderBy('ejaculated_date')->get();

        $this->assertCount(1, $ejaculations);
        $this->assertEquals('http://example.com', $ejaculations[0]->link);
    }

    public function testLinkOverLengthUTF8()
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('3 行 : オカズリンクには2000文字以下の文字列を指定してください。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/link-over-length.utf8.csv');
        $importer->execute();
    }

    public function testLinkIsNotUrlUTF8()
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : オカズリンクには正しい形式のURLを指定してください。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/link-not-url.utf8.csv');
        $importer->execute();
    }

    public function testTag1UTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag1.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();
        $tags = $ejaculation->tags()->get();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertCount(1, $tags);
        $this->assertEquals('貧乳', $tags[0]->name);
    }

    public function testTag2UTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag2.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();
        $tags = $ejaculation->tags()->get();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertCount(2, $tags);
        $this->assertEquals('貧乳', $tags[0]->name);
        $this->assertEquals('巨乳', $tags[1]->name);
    }

    public function testTagOverLengthUTF8()
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('3 行 : タグ1は255文字以内にしてください。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-over-length.utf8.csv');
        $importer->execute();
    }

    public function testTagCanAcceptJumpedColumnUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-jumped-column.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();
        $tags = $ejaculation->tags()->get();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertCount(2, $tags);
        $this->assertEquals('貧乳', $tags[0]->name);
        $this->assertEquals('巨乳', $tags[1]->name);
    }

    public function testTagCantAcceptMultilineUTF8()
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : タグ1に改行を含めることはできません。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-multiline.utf8.csv');
        $importer->execute();
    }

    public function testTagCantAcceptWhitespaceUTF8()
    {
        $user = User::factory()->create();
        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : タグ1にスペースを含めることはできません。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-whitespace.utf8.csv');
        $importer->execute();
    }

    public function testTagCanAccept40ColumnsUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-41-column.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();
        $tags = $ejaculation->tags()->get();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertCount(40, $tags);
        $this->assertEquals('り', $tags[39]->name);
    }

    public function testSkipAllNullTagColumns()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/tag-null.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();
        $tags = $ejaculation->tags()->get();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertCount(0, $tags);
    }

    public function testSourceIsCsv()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/date.utf8.csv');
        $importer->execute();
        $ejaculation = $user->ejaculations()->first();

        $this->assertSame(1, $user->ejaculations()->count());
        $this->assertEquals(Ejaculation::SOURCE_CSV, $ejaculation->source);
    }

    public function testIsPrivateUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/private.utf8.csv');
        $importer->execute();

        $ejaculations = $user->ejaculations()->orderBy('ejaculated_date')->get();

        $this->assertSame(9, $ejaculations->count());
        $this->assertTrue($ejaculations[0]->is_private);
        $this->assertTrue($ejaculations[1]->is_private);
        $this->assertTrue($ejaculations[2]->is_private);
        $this->assertTrue($ejaculations[3]->is_private);
        $this->assertFalse($ejaculations[4]->is_private);
        $this->assertFalse($ejaculations[5]->is_private);
        $this->assertFalse($ejaculations[6]->is_private);
        $this->assertFalse($ejaculations[7]->is_private);
        $this->assertFalse($ejaculations[8]->is_private);
    }

    public function testIsTooSensitiveUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/too-sensitive.utf8.csv');
        $importer->execute();

        $ejaculations = $user->ejaculations()->orderBy('ejaculated_date')->get();

        $this->assertSame(9, $ejaculations->count());
        $this->assertTrue($ejaculations[0]->is_too_sensitive);
        $this->assertTrue($ejaculations[1]->is_too_sensitive);
        $this->assertTrue($ejaculations[2]->is_too_sensitive);
        $this->assertTrue($ejaculations[3]->is_too_sensitive);
        $this->assertFalse($ejaculations[4]->is_too_sensitive);
        $this->assertFalse($ejaculations[5]->is_too_sensitive);
        $this->assertFalse($ejaculations[6]->is_too_sensitive);
        $this->assertFalse($ejaculations[7]->is_too_sensitive);
        $this->assertFalse($ejaculations[8]->is_too_sensitive);
    }

    public function testDiscardElapsedTimeUTF8()
    {
        $user = User::factory()->create();

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/discard-elapsed-time.utf8.csv');
        $importer->execute();

        $ejaculations = $user->ejaculations()->orderBy('ejaculated_date')->get();

        $this->assertSame(9, $ejaculations->count());
        $this->assertTrue($ejaculations[0]->discard_elapsed_time);
        $this->assertTrue($ejaculations[1]->discard_elapsed_time);
        $this->assertTrue($ejaculations[2]->discard_elapsed_time);
        $this->assertTrue($ejaculations[3]->discard_elapsed_time);
        $this->assertFalse($ejaculations[4]->discard_elapsed_time);
        $this->assertFalse($ejaculations[5]->discard_elapsed_time);
        $this->assertFalse($ejaculations[6]->discard_elapsed_time);
        $this->assertFalse($ejaculations[7]->discard_elapsed_time);
        $this->assertFalse($ejaculations[8]->discard_elapsed_time);
    }

    public function testDontThrowUniqueKeyViolation()
    {
        $user = User::factory()->create();
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 1, 23, 6, 1, 0, 'Asia/Tokyo')
        ]);

        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : すでにこの日時のチェックインデータが存在します。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/date.utf8.csv');
        $importer->execute();
    }

    public function testRecordLimit()
    {
        $user = User::factory()->create();
        Ejaculation::factory(5000)->create([
            'user_id' => $user->id,
            'source' => Ejaculation::SOURCE_CSV
        ]);

        $this->expectException(CsvImportException::class);
        $this->expectExceptionMessage('2 行 : インポート機能で取り込めるデータは5000件までに制限されています。これ以上取り込みできません。');

        $importer = new CheckinCsvImporter($user, __DIR__ . '/../../fixture/Csv/link.utf8.csv');
        $importer->execute();
    }
}
