<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AllProtectUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:user:protect-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set is_protected flag for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('すべてのユーザーのチェックインを非公開にします。');
        $this->warn('【警告】この操作を元に戻すことはできません！ 実行前に必ずデータベースのバックアップを作成してください！！！');

        if ($this->confirm('本当に実行しますか？')) {
            User::query()->update(['is_protected' => true]);
            $this->info('完了しました');
        }
    }
}
