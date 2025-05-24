<?php

namespace Tests\Feature\Api\V1;

use App\Ejaculation;
use App\Like;
use App\TagFilter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserLikeTest extends TestCase
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
            'note' => 'test ejaculation',
        ]);

        $like = Like::create([
            'user_id' => $user->id,
            'ejaculation_id' => $ejaculation->id,
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/likes');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $ejaculation->id,
                'note' => 'test ejaculation',
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

        $ejaculations = Ejaculation::factory(11)->create([
            'user_id' => $user->id,
        ]);

        // Create likes for all ejaculations
        foreach ($ejaculations as $ejaculation) {
            Like::create([
                'user_id' => $user->id,
                'ejaculation_id' => $ejaculation->id,
            ]);
        }

        $response = $this->getJson('/api/v1/users/' . $user->name . '/likes?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 11);
        $response->assertJsonCount(1);
    }

    public function testMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $target->delete();

        $response = $this->getJson('/api/v1/users/' . $target->name . '/likes');

        $response->assertStatus(404);
    }

    public function testExposeMyPrivateCheckins()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $publicEjaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'is_private' => false,
        ]);
        $privateEjaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'is_private' => true,
        ]);

        Like::create([
            'user_id' => $user->id,
            'ejaculation_id' => $publicEjaculation->id,
        ]);
        Like::create([
            'user_id' => $user->id,
            'ejaculation_id' => $privateEjaculation->id,
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/likes');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertJsonCount(2);
    }

    public function testHidePrivateCheckins()
    {
        $user = User::factory()->create();
        $target = User::factory()->create();
        Passport::actingAs($user);

        $publicEjaculation = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => false,
        ]);
        $privateEjaculation = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => true,
        ]);

        Like::create([
            'user_id' => $target->id,
            'ejaculation_id' => $publicEjaculation->id,
        ]);
        Like::create([
            'user_id' => $target->id,
            'ejaculation_id' => $privateEjaculation->id,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/likes');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $publicEjaculation->id,
            ]
        ], true);
    }

    public function testTagFilterRemovesEjaculations()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $tagName = 'filtered_tag';

        $ejaculationWithTag = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => false,
        ]);
        $ejaculationWithTag->tags()->create(['name' => $tagName]);

        $ejaculationWithoutTag = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => false,
        ]);

        Like::create([
            'user_id' => $target->id,
            'ejaculation_id' => $ejaculationWithTag->id,
        ]);
        Like::create([
            'user_id' => $target->id,
            'ejaculation_id' => $ejaculationWithoutTag->id,
        ]);

        $user->tagFilters()->create([
            'tag_name' => $tagName,
            'mode' => TagFilter::MODE_REMOVE,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/likes');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $ejaculationWithoutTag->id,
            ]
        ], true);
    }

    public function testPrivateLikesReturns403()
    {
        $user = User::factory()->create();
        $target = User::factory()->create([
            'private_likes' => true
        ]);
        Passport::actingAs($user);

        // Create some likes for the target user
        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $target->id,
            'is_private' => false,
        ]);

        Like::create([
            'user_id' => $target->id,
            'ejaculation_id' => $ejaculation->id,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/likes');

        $response->assertStatus(403);
        $response->assertJson([
            'error' => [
                'message' => 'このユーザーのいいねは表示できません'
            ]
        ]);
    }
}
