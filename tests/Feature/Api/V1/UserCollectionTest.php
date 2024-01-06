<?php
declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Collection;
use App\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserCollectionTest extends TestCase
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

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/collections');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $collection->id,
                'title' => 'test collection',
                'is_private' => false,
            ]
        ], true);
    }

    public function testPagination()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Collection::factory(11)
            ->sequence(fn (Sequence $seq) => ['title' => 'collection #' . $seq->index])
            ->create([
                'user_id' => $user->id,
            ]);
        $last = $user->collections()->orderByDesc('id')->first();

        $response = $this->getJson('/api/v1/users/' . $user->name . '/collections?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 11);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $last->id,
            ]
        ], true);
    }

    public function testProtected()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->protected()->create();

        $response = $this->getJson('/api/v1/users/' . $target->name . '/collections');

        $response->assertStatus(403);
    }

    public function testMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $target->delete();

        $response = $this->getJson('/api/v1/users/' . $target->name . '/collections');

        $response->assertStatus(404);
    }

    public function testExposeMyPrivateCollections()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'public collection',
            'is_private' => false,
        ]);
        Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'private collection',
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/users/' . $user->name . '/collections');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertJsonCount(2);
    }

    public function testHidePrivateCollections()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $public = Collection::factory()->create([
            'user_id' => $target->id,
            'title' => 'public collection',
            'is_private' => false,
        ]);
        Collection::factory()->create([
            'user_id' => $target->id,
            'title' => 'private collection',
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name . '/collections');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $public->id,
            ]
        ], true);
    }
}
