<?php
declare(strict_types=1);

namespace App\Console\Commands\Deploy;

use Illuminate\Console\Command;

class FreshDeploymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:fresh {--dev}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handles migrations and optimisations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate:fresh', [
            '--force' => true,
        ]);

        $this->call('optimize:clear');
        $this->call('event:clear');
        $this->call('view:clear');
    }
}
