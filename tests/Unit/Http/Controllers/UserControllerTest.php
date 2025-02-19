<?php

namespace Tests\Unit\Http\Controllers;

use App\Ejaculation;
use App\Http\Controllers\UserController;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Carbon::setTestNow('2020-07-21 19:00:00');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow();
    }

    public function testProfileShikontributions()
    {
        $user = User::factory()->create()->fresh();

        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 19, 19, 0, 'Asia/Tokyo'),
            'link' => '',
            'note' => 'world biggest boob',
        ]);

        foreach (['2020-07-21 00:00:00', '2020-07-21 09:00:00', '2020-07-21 19:00:00'] as $time) {
            Carbon::setTestNow($time);
            $response = $this->get('/user/' . $user->name);
            // シコ草における日付を表すタイムスタンプがチェックイン・プロフィール閲覧の時刻によらずUTCで00:00に揃えられることを確認する
            // UNIX時間 1593561600 = 2020-07-01 00:00:00 UTC
            $response->assertSee('<script id="count-by-day" type="application/json">[{"t":1593561600,"count":1}]</script>', false);
        }
    }

    public function testStatsYearlyShikontributions()
    {
        $user = User::factory()->create()->fresh();

        $ejaculation = Ejaculation::factory()->create([
            'user_id' => $user->id,
            'ejaculated_date' => Carbon::create(2020, 7, 1, 19, 19, 0, 'Asia/Tokyo'),
            'link' => '',
            'note' => 'world biggest boob',
        ]);

        foreach (['2020-07-21 00:00:00', '2020-07-21 09:00:00', '2020-07-21 19:00:00'] as $time) {
            Carbon::setTestNow($time);
            $response = $this->get('/user/' . $user->name . '/stats/2020');
            // シコ草における日付を表すタイムスタンプがチェックイン・プロフィール閲覧の時刻によらずUTCで00:00に揃えられることを確認する
            // UNIX時間 1593561600 = 2020-07-01 00:00:00 UTC
            $response->assertSee('"dailySum":[{"t":1593561600,"count":1}]', false);
        }
    }
}
