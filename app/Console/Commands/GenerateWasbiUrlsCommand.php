<?php

namespace App\Console\Commands;

use App\Jobs\GenerateExpiringWasabiUrls;
use Illuminate\Console\Command;

class GenerateWasbiUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wasbi:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $obj = new GenerateExpiringWasabiUrls();
        $obj->handle();
    }
}
