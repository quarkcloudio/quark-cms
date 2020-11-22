<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'quark:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the quark-cms';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('migrate');

        if (\QuarkCMS\QuarkAdmin\Models\Admin::count() == 0) {
            $this->call('db:seed');
        }

        $this->call('key:generate');
        $this->call('quarkadmin:install');
    }
}