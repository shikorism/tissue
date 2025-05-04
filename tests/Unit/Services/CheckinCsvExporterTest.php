<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Ejaculation;
use App\Services\CheckinCsvExporter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use League\Csv\Reader;
use Tests\TestCase;

class CheckinCsvExporterTest extends TestCase
{
    use RefreshDatabase;

    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'tissue_csv_export_test_') . '.csv';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    public function testExportCsvWithValidData()
    {
        $user = User::factory()->create();
        $ejaculations = Ejaculation::factory(3)
            ->sequence(
                ['ejaculated_date' => Carbon::now()->subDays(2)],
                ['ejaculated_date' => Carbon::now()->subDays(1)],
                ['ejaculated_date' => Carbon::now()],
            )
            ->create([
                'user_id' => $user->id,
            ]);

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $this->assertCount(3, $records);

        foreach ($records as $index => $record) {
            $index--;
            $this->assertEquals($ejaculations[$index]->ejaculated_date->format('Y/m/d H:i'), $record['日時']);
            $this->assertEquals($ejaculations[$index]->note, $record['ノート']);
            $this->assertEquals($ejaculations[$index]->link, $record['オカズリンク']);
        }
    }

    public function testExportCsvWithTags()
    {
        $user = User::factory()->create();
        $ejaculation = Ejaculation::factory()->create(['user_id' => $user->id]);
        $tags = $ejaculation->tags()->createMany([
            ['name' => 'Tag1'],
            ['name' => 'Tag2'],
        ]);

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $this->assertCount(1, $records);

        $record = $records[1];
        $this->assertEquals('Tag1', $record['タグ1']);
        $this->assertEquals('Tag2', $record['タグ2']);
    }

    public function testExportCsvWithSjisEncoding()
    {
        $user = User::factory()->create();
        Ejaculation::factory()->create(['user_id' => $user->id]);

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'SJIS-win');
        $exporter->execute();

        $content = file_get_contents($this->tmpFile);
        $this->assertNotFalse(mb_detect_encoding($content, 'SJIS-win', true));
    }

    public function testExportCsvWithNoEjaculations()
    {
        $user = User::factory()->create();

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $this->assertCount(0, $records);
    }

    public function testExportCsvWithNoteHasCrlf()
    {
        $user = User::factory()->create();
        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'note' => 'Note with CRLF\r\nNew line',
        ]);

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $this->assertCount(1, $records);

        $record = $records[1];
        $this->assertEquals('Note with CRLF\r\nNew line', $record['ノート']);
    }

    public function testExportCsvWithNoteHasLf()
    {
        $user = User::factory()->create();
        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'note' => 'Note with LF\nNew line',
        ]);

        $exporter = new CheckinCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords());
        $this->assertCount(1, $records);

        $record = $records[1];
        $this->assertEquals('Note with LF\r\nNew line', $record['ノート']);
    }
}
