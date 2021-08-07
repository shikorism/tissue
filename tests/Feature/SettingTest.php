<?php

namespace Tests\Feature;

use App\Ejaculation;
use App\Like;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testDestroyUser()
    {
        $user = factory(User::class)->create();
        $ejaculation = factory(Ejaculation::class)->create(['user_id' => $user->id]);

        $anotherUser = factory(User::class)->create();
        $anotherEjaculation = factory(Ejaculation::class)->create(['user_id' => $anotherUser->id]);

        $like = factory(Like::class)->create([
            'user_id' => $user->id,
            'ejaculation_id' => $anotherEjaculation->id,
        ]);
        $anotherLike = factory(Like::class)->create([
            'user_id' => $anotherUser->id,
            'ejaculation_id' => $ejaculation->id,
        ]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/setting/deactivate', ['password' => 'secret']);

        $response->assertStatus(200)
            ->assertViewIs('setting.deactivated');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('ejaculations', ['id' => $ejaculation->id]);
        $this->assertDatabaseMissing('likes', ['id' => $like->id]);
        $this->assertDatabaseMissing('likes', ['id' => $anotherLike->id]);
        $this->assertDatabaseHas('deactivated_users', ['name' => $user->name]);
    }
}
