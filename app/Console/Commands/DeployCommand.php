<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the deploy script';
    /**
     * The console command help text.
     *
     * @var string|null
     */
    protected $help = 'This command allows you to run the deploy script';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $process = new Process(['/bin/bash', app()->basePath('deploy.sh')], app()->basePath());
        $process->run(function ($type, $buffer) {
            $this->info($buffer);
        });
        return 0;
    }
}
