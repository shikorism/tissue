<?php

namespace Tests\Feature\Api\V1;

use App\Ejaculation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Carbon::setTestNow('2020-07-21 19:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testBasicProfile()
    {
        $user = User::factory()->create()->fresh(); // 不思議なことにリロードしないとnot null列の結果がおかしい
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'name' => $user->name,
            'display_name' => $user->display_name,
            'is_protected' => false,
            'private_likes' => false,
            'bio' => '',
            'url' => '',
        ], true);
    }

    public function testBioAndUrl()
    {
        $user = User::factory()->create([
            'bio' => 'happy f*cking',
            'url' => 'http://example.com',
        ]);
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'name' => $user->name,
            'display_name' => $user->display_name,
            'is_protected' => false,
            'private_likes' => false,
            'bio' => 'happy f*cking',
            'url' => 'http://example.com',
        ], true);
    }

    public function testCheckinSummary()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo')
        ]);
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 3, 0, 0, 0, 'Asia/Tokyo')
        ]);
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 7, 0, 0, 0, 'Asia/Tokyo')
        ]);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'checkin_summary' => [
                'current_session_elapsed' => 1278000,
                'total_checkins' => 3,
                'total_times' => 518400,
                'average_interval' => 172800,
                'longest_interval' => 345600,
                'shortest_interval' => 172800
            ]
        ], true);
    }
}
