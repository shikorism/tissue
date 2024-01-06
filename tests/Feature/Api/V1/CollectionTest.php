<?php
declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Collection;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testGet()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $collection->id,
            'title' => 'test collection',
            'is_private' => false,
        ], true);
    }

    public function testGetProtected()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $targetUser = User::factory()->protected()->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(404);
    }

    public function testGetMyPrivate()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(200);
    }

    public function testGetOthersPrivate()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $targetUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => true,
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(404);
    }

    public function testGetMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/collections/0');

        $response->assertStatus(404);
    }

    public function testPost()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/v1/collections', [
            'title' => 'new collection',
            'is_private' => false,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'title' => 'new collection',
            'is_private' => false,
        ], true);

        $collectionId = $response->json('id');
        $collection = Collection::find($collectionId);
        $this->assertSame($user->id, $collection->user_id);
        $this->assertSame('new collection', $collection->title);
        $this->assertFalse($collection->is_private);
    }

    public function testPostConflictTitle()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'new collection',
            'is_private' => false,
        ]);

        $response = $this->postJson('/api/v1/collections', [
            'title' => 'new collection',
            'is_private' => false,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 422)
            ->assertJsonPath('error.message', 'そのタイトルはすでに使われています。')
            ->assertJsonCount(1, 'error.violations')
            ->assertJsonPath('error.violations.0.field', 'title')
            ->assertJsonPath('error.violations.0.message', 'そのタイトルはすでに使われています。');
    }

    public function testPatch()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->patchJson('/api/v1/collections/' . $collection->id, [
            'title' => 'updated',
            'is_private' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $collection->id,
            'title' => 'updated',
            'is_private' => false,
        ], true);
    }

    public function testPatchMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->patchJson('/api/v1/collections/0', [
            'title' => 'updated',
            'is_private' => false,
        ]);

        $response->assertStatus(404);
    }

    public function testPatchOthers()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $targetUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->patchJson('/api/v1/collections/' . $collection->id, [
            'title' => 'updated',
            'is_private' => false,
        ]);

        $response->assertStatus(403);
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function testDeleteMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collection->delete();

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(404);
    }

    public function testDeleteOthers()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $targetUser = User::factory()->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('collections', ['id' => $collection->id]);
    }
}
