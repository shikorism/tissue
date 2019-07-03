<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateFixture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:fixture:update {resolver : Some Resolver Name }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update specific fixtures';

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
        $resolver_base_path = __DIR__ . '/../../../tests/Unit/MetadataResolver/';
        $test_file_path = $resolver_base_path . $this->argument('resolver') . 'ResolverTest.php';

        if (file_exists($test_file_path)) {
            $this->info($this->argument('resolver') . 'ResolverTest.php is found.');

            $test_file = file_get_contents($test_file_path);
            $test_file_without_comment = '';
            // コメントを削除する
            $tokens = token_get_all($test_file);
            foreach ($tokens as $token) {
                if (is_string($token)) {
                    $test_file_without_comment .= $token;
                } else {
                    list($id, $text) = $token;
                    if (token_name($id) !== 'T_COMMENT') {
                        $test_file_without_comment .= $text;
                    }
                }
            }
            preg_match_all('~file_get_contents\(__DIR__ . \'/(.+)\'\);~', $test_file_without_comment, $fixtures);
            preg_match_all('~\$this->assertSame\(\'(.+)\', \(string\) \$this->handler->getLastRequest\(\)->getUri\(\)\);~m', $test_file_without_comment, $urls);
            $update_list = array_combine($fixtures[1], $urls[1]);

            $progress = $this->output->createProgressBar(count($update_list));
            $progress->setFormat('Updating %path% from %url%' . PHP_EOL . '%current%/%max% [%bar%] %percent:3s%%');

            foreach ($update_list as $path => $url) {
                sleep(1);
                $progress->setMessage($path, 'path');
                $progress->setMessage($url, 'url');
                file_put_contents($resolver_base_path . $path, file_get_contents($url));
                $progress->advance();
            }

            $progress->finish();
            $this->output->newLine();
            $this->info('Update Complete!');
        } else {
            $this->error($this->argument('resolver') . 'ResolverTest.php is not found.');
        }
    }
}
