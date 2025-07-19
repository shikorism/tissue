<?php
declare(strict_types=1);

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\ActivityPubResolver;
use PHPUnit\Event\Runtime\PHP;
use Tests\TestCase;

class ActivityPubResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testNote()
    {
        $responses = [
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/note_note.json', 0),
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/note_actor.json', 1),
        ];

        $this->createResolverEx(ActivityPubResolver::class, [
            ['responseText' => $responses[0]],
            ['responseText' => $responses[1]],
        ]);

        $metadata = $this->resolver->resolve('https://ertona.net/@shibafu528/114878118995002996');
        $this->assertEquals('#<Object:0x00000528> (@shibafu528@ertona.net)', $metadata->title);
        $this->assertEquals('テスト' . PHP_EOL . 'テストテストテスト' . PHP_EOL . 'テ', $metadata->description);
        $this->assertEmpty($metadata->image);
    }

    public function testNoteWithSummary()
    {
        $responses = [
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/noteWithSummary_note.json', 0),
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/noteWithSummary_actor.json', 1),
        ];

        $this->createResolverEx(ActivityPubResolver::class, [
            ['responseText' => $responses[0]],
            ['responseText' => $responses[1]],
        ]);

        $metadata = $this->resolver->resolve('https://ertona.net/@shibafu528/114878196104183153');
        $this->assertEquals('#<Object:0x00000528> (@shibafu528@ertona.net)', $metadata->title);
        $this->assertEquals('summary | content', $metadata->description);
        $this->assertEmpty($metadata->image);
    }

    public function testNoteWithTagOmission()
    {
        $responses = [
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/noteWithTagOmission_note.json', 0),
            $this->fetchSnapshot(__DIR__ . '/../../fixture/ActivityPub/noteWithTagOmission_actor.json', 1),
        ];

        $this->createResolverEx(ActivityPubResolver::class, [
            ['responseText' => $responses[0]],
            ['responseText' => $responses[1]],
        ]);

        $metadata = $this->resolver->resolve('https://misskey.io/notes/aaddvocya9zx06gh');
        $this->assertEquals(' (@shibafu528@misskey.io)', $metadata->title);
        // TODO: <blockquote> の終了後の改行が入ってないのは既知のバグ
        $this->assertEquals('引用TissueのAPパースのテスト用' . PHP_EOL . '改行', $metadata->description);
        $this->assertEmpty($metadata->image);
    }
}
