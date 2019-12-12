<?php

namespace Tests\Feature;

use App\Ejaculation;
use App\Like;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class SettingTest extends TestCase
{
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

        $token = $this->getCsrfToken($user, '/setting/deactivate');
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post('/setting/deactivate', [
                '_token' => $token,
                'password' => 'secret',
            ]);

        $response->assertStatus(200)
            ->assertViewIs('setting.deactivated');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('ejaculations', ['id' => $ejaculation->id]);
        $this->assertDatabaseMissing('likes', ['id' => $like->id]);
        $this->assertDatabaseMissing('likes', ['id' => $anotherLike->id]);
        $this->assertDatabaseHas('deactivated_users', ['name' => $user->name]);
    }

    /**
     * テスト対象を呼び出す前にGETリクエストを行い、CSRFトークンを得る
     * @param Authenticatable $user 認証情報
     * @param string $uri リクエスト先
     * @return string CSRFトークン
     */
    private function getCsrfToken(Authenticatable $user, string $uri): string
    {
        $response = $this->actingAs($user)->get($uri);
        $crawler = new Crawler($response->getContent());

        return $crawler->filter('input[name=_token]')->attr('value');
    }
}
