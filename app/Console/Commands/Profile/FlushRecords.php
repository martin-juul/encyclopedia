<?php

namespace App\Console\Commands\Profile;

use App\Models\Sys\ProfileReport;
use Illuminate\Console\Command;

class FlushRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates the profiles table';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProfileReport::truncate();

        $this->info('Truncated profiles table');
    }
}
