<?php

namespace Tests\Feature\Api\V1\Timelines;

use App\Ejaculation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PublicTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testDefaultQuery()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // オカズ付きの公開チェックインを作成
        $publicWithLink = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'link' => 'http://example.com',
            'note' => 'public with link',
            'is_private' => false,
        ]);

        // オカズなしの公開チェックインを作成（結果に表示されないはず）
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 2, 0, 0, 0, 'Asia/Tokyo'),
            'link' => '',
            'note' => 'public without link',
            'is_private' => false,
        ]);

        // オカズ付きの非公開チェックインを作成（結果に表示されないはず）
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 3, 0, 0, 0, 'Asia/Tokyo'),
            'link' => 'http://example.com',
            'note' => 'private with link',
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/timelines/public');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $publicWithLink->id,
                'checked_in_at' => '2020-07-01T00:00:00+09:00',
                'tags' => [],
                'link' => 'http://example.com',
                'note' => 'public with link',
                'is_private' => false,
                'is_too_sensitive' => false,
                'discard_elapsed_time' => false,
                'source' => 'web',
            ]
        ], true);
    }

    public function testPagination()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // オカズ付きの公開チェックインを11件作成
        for ($i = 0; $i < 11; $i++) {
            Ejaculation::factory()->create([
                'user_id' => $user->id,
                'ejaculated_date' => Carbon::create(2020, 7, $i + 1, 0, 0, 0, 'Asia/Tokyo'),
                'link' => 'http://example.com',
                'is_private' => false,
            ]);
        }

        // 最も古いチェックインを取得（per_page=10の場合、2ページ目に表示されるはず）
        $oldest = Ejaculation::orderBy('ejaculated_date')->first();

        $response = $this->getJson('/api/v1/timelines/public?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 11);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $oldest->id,
            ]
        ], true);
    }

    public function testProtectedUserCheckins()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // オカズ付きの公開チェックインを持つ公開ユーザーを作成
        $publicUser = User::factory()->create(['is_protected' => false]);
        $publicCheckin = Ejaculation::factory()->create([
            'user_id' => $publicUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'link' => 'http://example.com',
            'is_private' => false,
        ]);

        // オカズ付きの公開チェックインを持つ非公開ユーザーを作成
        $protectedUser = User::factory()->protected()->create();
        Ejaculation::factory()->create([
            'user_id' => $protectedUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 2, 0, 0, 0, 'Asia/Tokyo'),
            'link' => 'http://example.com',
            'is_private' => false,
        ]);

        $response = $this->getJson('/api/v1/timelines/public');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $publicCheckin->id,
            ]
        ], true);
    }

    public function testFutureCheckins()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // オカズ付きの過去のチェックインを作成
        $pastCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::now()->subDay(),
            'link' => 'http://example.com',
            'is_private' => false,
        ]);

        // オカズ付きの未来のチェックインを作成 (未来のチェックインは表示されないはず)
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::now()->addDay(),
            'link' => 'http://example.com',
            'is_private' => false,
        ]);

        $response = $this->getJson('/api/v1/timelines/public');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $pastCheckin->id,
            ]
        ], true);
    }

    public function testPerPageValidation()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/timelines/public?per_page=5');
        $response->assertStatus(422);

        $response = $this->getJson('/api/v1/timelines/public?per_page=101');
        $response->assertStatus(422);

        $response = $this->getJson('/api/v1/timelines/public?per_page=10');
        $response->assertStatus(200);
    }
}
