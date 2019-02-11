<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class PromoteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tissue:user:promote {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote user to admin';

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
        $user = User::where('name', $this->argument('username'))->first();
        if ($user === null) {
            $this->error('No user with such username');
            return 1;
        }

        if ($user->is_admin) {
            $this->info('@' . $user->name . ' is already an administrator.');
            return 0;
        }

        $user->is_admin = true;
        if ($user->save()) {
            $this->info('@' . $user->name . ' is an administrator now.');
        } else {
            $this->error('Something happened.');
        }
    }
}
