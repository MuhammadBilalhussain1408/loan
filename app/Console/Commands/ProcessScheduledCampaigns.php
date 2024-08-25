<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCampaign;
use App\Models\CommunicationCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;


use Stancl\Tenancy\Concerns\HasATenantsOption;
use Stancl\Tenancy\Concerns\TenantAwareCommand;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class ProcessScheduledCampaigns extends Command
{


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'campaigns:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes Scheduled Campaigns';

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
        $campaigns = CommunicationCampaign::where('trigger_type', 'schedule')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('scheduled_date', date("Y-m-d"));
                    $query->orWhere('scheduled_next_run_date', date('Y-m-d'));
                });
            })
            ->where('scheduled_time', date('H:i'))
            ->get();
        foreach ($campaigns as $communicationCampaign) {
            ProcessCampaign::dispatch($communicationCampaign);
            //check if we should move the schedule
            if (!empty($communicationCampaign->schedule_frequency) & !empty($communicationCampaign->schedule_frequency_type)) {
                if ($communicationCampaign->recur_type === 'selected_days') {
                    foreach ($communicationCampaign->selected_days as $day) {
                        $date = Carbon::parse($day);
                        if ($date->greaterThan(Carbon::now())) {
                            $communicationCampaign->scheduled_date = $date->format('Y-m-d');
                            $communicationCampaign->scheduled_next_run_date = $date->format('Y-m-d');
                            break;
                        }
                    }
                } else {
                    $communicationCampaign->scheduled_date = Carbon::now()->add($communicationCampaign->schedule_frequency, $communicationCampaign->schedule_frequency_type)->format("Y-m-d");
                    $communicationCampaign->scheduled_next_run_date = Carbon::now()->add($communicationCampaign->schedule_frequency, $communicationCampaign->schedule_frequency_type)->format("Y-m-d");
                }
                $communicationCampaign->scheduled_last_run_date = Carbon::now()->format("Y-m-d");
                $communicationCampaign->save();
            }

        }
        $this->info("Schedule ran successfully");
        return CommandAlias::SUCCESS;
    }


}
