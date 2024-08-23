<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WorkPlanGeneratorService;
use App\Models\Group;

class GenerateWorkPlans extends Command
{
    protected $signature = 'workplans:generate {week? : The week number} {year? : The year}';
    protected $description = 'Generate work plans for all groups';

    private $workPlanGenerator;

    public function __construct(WorkPlanGeneratorService $workPlanGenerator)
    {
        parent::__construct();
        $this->workPlanGenerator = $workPlanGenerator;
    }

    public function handle()
    {
        $week = $this->argument('week') ?? now()->weekOfYear;
        $year = $this->argument('year') ?? now()->year;

        $groups = Group::all();

        foreach ($groups as $group) {
            $this->workPlanGenerator->generateForGroup($group, $week, $year);
            $this->info("Generated work plans for group {$group->name}");
        }

        $this->info('All work plans generated successfully.');
    }
}
