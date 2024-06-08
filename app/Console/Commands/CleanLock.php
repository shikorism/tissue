<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:lock:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete obsolete global lock file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lockDir = storage_path('global_lock');
        if (!is_dir($lockDir)) {
            return Command::SUCCESS;
        }
        $dh = opendir($lockDir);
        if (!$dh) {
            $this->error("Can't open directory: {$lockDir}");

            return Command::FAILURE;
        }
        try {
            while (($file = readdir($dh)) !== false) {
                $path = $lockDir . DIRECTORY_SEPARATOR . $file;
                if (!is_file($path) || str_starts_with($file, '.')) {
                    continue;
                }

                // あまり古くないファイルはスキップ
                $mtime = filemtime($path);
                if ($mtime === false || !Carbon::createFromTimestamp($mtime)->isBefore(Carbon::now()->subDays(7))) {
                    continue;
                }

                // サーバープロセスで使用中だと困るので排他ロックを取ってから削除する
                $fp = fopen($path, 'r');
                if ($fp === false) {
                    $this->error("Error (can't open lock file): {$file}");
                    continue;
                }
                try {
                    if (!flock($fp, LOCK_EX)) {
                        $this->error("Error (can't lock file): {$file}");
                        continue;
                    }

                    if (unlink($path)) {
                        $this->info("Delete: {$file}");
                    } else {
                        $this->error("Error (can't delete lock file): {$file}");
                    }
                } finally {
                    if (!fclose($fp)) {
                        $this->error("Error (can't close lock file): {$file}");
                    }
                }
            }
        } finally {
            closedir($dh);
        }

        return Command::SUCCESS;
    }
}
