<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TwitterOAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:twitter:oauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sign in with Twitter and get access token/secret';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $twitter = new \Abraham\TwitterOAuth\TwitterOAuth(config('twitter.api_key'), config('twitter.api_secret'));
        $requestToken = $twitter->oauth('oauth/request_token', ['oauth_callback' => 'oob']);
        $url = $twitter->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);

        $this->info('Please login via browser: ' . $url);
        $pin = $this->ask('After login, please paste your PIN-code here and hit return key');

        $twitter = new \Abraham\TwitterOAuth\TwitterOAuth(config('twitter.api_key'), config('twitter.api_secret'), $requestToken['oauth_token'], $requestToken['oauth_token_secret']);
        $accessToken = $twitter->oauth('oauth/access_token', ['oauth_verifier' => $pin]);

        $this->info('Login successful! Please remember this access token/secret.');
        $this->line('TWITTER_API_ACCESS_TOKEN=' . $accessToken['oauth_token']);
        $this->line('TWITTER_API_ACCESS_TOKEN_SECRET=' . $accessToken['oauth_token_secret']);
    }
}
