<?php

namespace Tests\Feature\Api\V1;

use App\Ejaculation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserCheckinTest extends TestCase
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

        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'link' => '',
            'note' => 'world biggest boob',
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $ejaculation->id,
                'checked_in_at' => '2020-07-01T00:00:00+09:00',
                'tags' => [],
                'link' => '',
                'note' => 'world biggest boob',
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

        Ejaculation::factory(11)->create([
            'user_id' => $user->id,
        ]);
        $oldest = $user->ejaculations()->orderBy('ejaculated_date')->first();

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 11);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $oldest->id,
            ]
        ], true);
    }

    public function testProtected()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->protected()->create();

        $response = $this->getJson('/api/v1/users/' . $target->name . '/checkins');

        $response->assertStatus(403);
    }

    public function testMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $target->delete();

        $response = $this->getJson('/api/v1/users/' . $target->name . '/checkins');

        $response->assertStatus(404);
    }

    public function testExposeMyPrivateCheckins()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'is_private' => false,
        ]);
        Ejaculation::factory()->create([
            'user_id' => $user->id,
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertJsonCount(2);
    }

    public function testHidePrivateCheckins()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $public = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => false,
        ]);
        Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/checkins');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $public->id,
            ]
        ], true);
    }

    public function testHasLink()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $hasLink = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'link' => 'http://example.com',
        ]);
        Ejaculation::factory()->create([
            'user_id' => $target->id,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/checkins?has_link=true');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $hasLink->id,
            ]
        ], true);
    }
}
