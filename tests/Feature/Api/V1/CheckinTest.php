<?php

namespace Tests\Feature\Api\V1;

use App\Ejaculation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testGet()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'link' => '',
            'note' => 'world biggest boob',
        ]);

        $response = $this->getJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $ejaculation->id,
            'checked_in_at' => '2020-07-01T00:00:00+09:00',
            'tags' => [],
            'link' => '',
            'note' => 'world biggest boob',
            'is_private' => false,
            'is_too_sensitive' => false,
            'discard_elapsed_time' => false,
            'source' => 'web',
        ], true);
    }

    public function testGetProtected()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->state('protected')->create();
        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $targetUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->getJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(403);
    }

    public function testGetMyPrivate()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(200);
    }

    public function testGetOthersPrivate()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $targetUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(403);
    }

    public function testGetMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $targetUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $ejaculation->delete();

        $response = $this->getJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(404);
    }

    public function testPatchMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $ejaculation->delete();

        $response = $this->patchJson('/api/v1/checkins/' . $ejaculation->id, [
            'note' => 'edited',
        ]);

        $response->assertStatus(404);
    }

    public function testPatchOthers()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $targetUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->patchJson('/api/v1/checkins/' . $ejaculation->id, [
            'note' => 'edited',
        ]);

        $response->assertStatus(403);
    }

    public function testDelete()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->deleteJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ejaculations', ['id' => $ejaculation->id]);
    }

    public function testDeleteMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);
        $ejaculation->delete();

        $response = $this->deleteJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('ejaculations', ['id' => $ejaculation->id]);
    }

    public function testDeleteOthers()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $ejaculation = factory(Ejaculation::class)->create([
            'user_id' => $targetUser->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 0, 0, 0, 'Asia/Tokyo'),
        ]);

        $response = $this->deleteJson('/api/v1/checkins/' . $ejaculation->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('ejaculations', ['id' => $ejaculation->id]);
    }
}
