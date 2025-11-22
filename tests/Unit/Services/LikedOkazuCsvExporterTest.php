<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Ejaculation;
use App\Services\LikedOkazuCsvExporter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\Csv\Reader;
use Tests\TestCase;

class LikedOkazuCsvExporterTest extends TestCase
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
        $ownEjaculation1 = Ejaculation::factory()->create(['user_id' => $user->id, 'is_private' => false]);
        $ownEjaculation2 = Ejaculation::factory()->create(['user_id' => $user->id, 'is_private' => true]);

        $anotherUser = User::factory()->create();
        $anotherUserEjaculation1 = Ejaculation::factory()->create(['user_id' => $anotherUser->id, 'is_private' => false]);
        $anotherUserEjaculation2 = Ejaculation::factory()->create(['user_id' => $anotherUser->id, 'is_private' => true]);

        $protectedUser = User::factory()->protected()->create();
        $protectedUserEjaculation = Ejaculation::factory()->create(['user_id' => $protectedUser->id]);

        $user->likes()->createMany([
            ['ejaculation_id' => $ownEjaculation1->id],
            ['ejaculation_id' => $ownEjaculation2->id],
            ['ejaculation_id' => $anotherUserEjaculation1->id],
            ['ejaculation_id' => $anotherUserEjaculation2->id],
            ['ejaculation_id' => $protectedUserEjaculation->id],
        ]);

        $exporter = new LikedOkazuCsvExporter($user, $this->tmpFile, 'UTF-8');
        $exporter->execute();

        $csv = Reader::createFromPath($this->tmpFile, 'r');
        $csv->setHeaderOffset(0);

        $records = iterator_to_array($csv->getRecords(), false);
        $this->assertCount(3, $records);

        // only includes public checkin or user's own checkin
        $this->assertStringEndsWith(route('checkin.show', ['id' => $ownEjaculation1->id]), $records[0]['チェックインURL']);
        $this->assertStringEndsWith(route('checkin.show', ['id' => $ownEjaculation2->id]), $records[1]['チェックインURL']);
        $this->assertStringEndsWith(route('checkin.show', ['id' => $anotherUserEjaculation1->id]), $records[2]['チェックインURL']);
    }
}
