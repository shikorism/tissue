<?php
declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Collection;
use App\CollectionItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CollectionItemTest extends TestCase
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

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
            'note' => 'big pi',
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id . '/items');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $collectionItem->id,
                'collection_id' => $collection->id,
                'link' => 'http://example.com',
                'note' => 'big pi',
            ]
        ], true);
    }

    public function testGetPagination()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        CollectionItem::factory(11)
            ->sequence(fn ($sequence) => ['link' => 'http://example.com/#' . $sequence->index])
            ->create([
                'collection_id' => $collection->id,
            ]);
        $first = $collection->items()->orderBy('id')->first();

        $response = $this->getJson('/api/v1/collections/' . $collection->id . '/items?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 11);
        $response->assertJsonCount(1);
        $response->assertJson([
            [
                'id' => $first->id,
            ]
        ], true);
    }

    public function testGetProtected()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->state('protected')->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => true,
        ]);
        CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id . '/items');

        $response->assertStatus(404);
    }

    public function testGetMyPrivate()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => true,
        ]);
        CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id . '/items');

        $response->assertStatus(200);
    }

    public function testGetOthersPrivate()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => true,
        ]);
        CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->getJson('/api/v1/collections/' . $collection->id . '/items');

        $response->assertStatus(404);
    }

    public function testGetMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/collections/0/items');

        $response->assertStatus(404);
    }

    public function testPost()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->postJson('/api/v1/collections/' . $collection->id . '/items', [
            'link' => 'http://example.com',
            'note' => 'big pi',
            'tags' => ['foo', 'bar'],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'link' => 'http://example.com',
            'note' => 'big pi',
            'tags' => ['foo', 'bar'],
        ]);

        $collectionItemId = $response->json('id');
        $collectionItem = $collection->items()->find($collectionItemId);
        $this->assertSame('http://example.com', $collectionItem->link);
        $this->assertSame('big pi', $collectionItem->note);
        $this->assertCount(2, $collectionItem->tags);
    }

    public function testPostConflictLink()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->postJson('/api/v1/collections/' . $collection->id . '/items', [
            'link' => 'http://example.com',
            'note' => 'big pi',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('status', 422)
            ->assertJsonPath('error.message', 'そのlinkはすでに使われています。')
            ->assertJsonCount(1, 'error.violations')
            ->assertJsonPath('error.violations.0.field', 'link')
            ->assertJsonPath('error.violations.0.message', 'そのlinkはすでに使われています。');
    }

    public function testPostMissingCollection()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/v1/collections/0/items', [
            'link' => 'http://example.com',
            'note' => 'big pi',
        ]);

        $response->assertStatus(404);
    }

    public function testPatch()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->patchJson('/api/v1/collections/' . $collection->id . '/items/' . $collectionItem->id, [
            'note' => 'updated',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'note' => 'updated',
        ]);

        $collectionItem->refresh();
        $this->assertSame('updated', $collectionItem->note);
    }

    public function testPatchMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->patchJson('/api/v1/collections/' . $collection->id . '/items/0', [
            'note' => 'updated',
        ]);

        $response->assertStatus(404);
    }

    public function testPatchOthers()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->patchJson('/api/v1/collections/' . $collection->id . '/items/' . $collectionItem->id, [
            'note' => 'updated',
        ]);

        $response->assertStatus(403);
    }

    public function testDelete()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id . '/items/' . $collectionItem->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('collection_items', ['id' => $collectionItem->id]);
    }

    public function testDeleteMissing()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $collection = Collection::factory()->create([
            'user_id' => $user->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id . '/items/0');

        $response->assertStatus(404);
    }

    public function testDeleteOthers()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $targetUser = factory(User::class)->create();
        $collection = Collection::factory()->create([
            'user_id' => $targetUser->id,
            'title' => 'test collection',
            'is_private' => false,
        ]);
        $collectionItem = CollectionItem::factory()->create([
            'collection_id' => $collection->id,
            'link' => 'http://example.com',
        ]);

        $response = $this->deleteJson('/api/v1/collections/' . $collection->id . '/items/' . $collectionItem->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('collection_items', ['id' => $collectionItem->id]);
    }
}
