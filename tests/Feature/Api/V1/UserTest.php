<?php

namespace Tests\Feature\Api\V1;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testMyself()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/users/' . $user->name);

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

    public function testMyselfBioAndUrl()
    {
        $user = User::factory()->create([
            'bio' => 'happy f*cking',
            'url' => 'http://example.com',
        ]);
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/users/' . $user->name);

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

    public function testProtected()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->protected()->create([
            'bio' => 'test test...',
            'url' => 'http://example.com',
        ]);

        $response = $this->getJson('/api/v1/users/' . $target->name);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => $target->name,
            'display_name' => $target->display_name,
            'is_protected' => true,
            'private_likes' => true,
        ], true);
        $response->assertJsonMissing([
            'bio' => 'test test...',
            'url' => 'http://example.com',
            'checkin_summary' => []
        ], true);
    }

    public function testMissing()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $target = User::factory()->create();
        $target->delete();

        $response = $this->getJson('/api/v1/users/' . $target->name);

        $response->assertStatus(404);
    }
}
