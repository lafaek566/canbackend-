<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reboot-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reboot linux server';

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
        shell_exec("systemctl reboot");
    }
}
