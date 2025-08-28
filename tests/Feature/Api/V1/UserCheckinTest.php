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

    public function testSince()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $oldCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 6, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $midCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $newCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 8, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?since=2020-07-01');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['id' => $midCheckin->id]);
        $response->assertJsonFragment(['id' => $newCheckin->id]);
        $response->assertJsonMissing(['id' => $oldCheckin->id]);
    }

    public function testUntil()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $oldCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 6, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $midCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $newCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 8, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?until=2020-07-31');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['id' => $oldCheckin->id]);
        $response->assertJsonFragment(['id' => $midCheckin->id]);
        $response->assertJsonMissing(['id' => $newCheckin->id]);
    }

    public function testSinceUntil()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $oldCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 6, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $midCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $newCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 8, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?since=2020-07-01&until=2020-07-31');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $midCheckin->id]);
        $response->assertJsonMissing(['id' => $oldCheckin->id]);
        $response->assertJsonMissing(['id' => $newCheckin->id]);
    }

    public function testOrder()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $oldCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 6, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $midCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $newCheckin = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 8, 15, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?order=asc');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertJsonCount(3);

        $responseData = $response->json();
        $this->assertEquals($oldCheckin->id, $responseData[0]['id']);
        $this->assertEquals($midCheckin->id, $responseData[1]['id']);
        $this->assertEquals($newCheckin->id, $responseData[2]['id']);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/checkins?order=desc');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertJsonCount(3);

        $responseData = $response->json();
        $this->assertEquals($newCheckin->id, $responseData[0]['id']);
        $this->assertEquals($midCheckin->id, $responseData[1]['id']);
        $this->assertEquals($oldCheckin->id, $responseData[2]['id']);
    }
}
