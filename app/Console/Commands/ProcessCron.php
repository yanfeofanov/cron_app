<?php

namespace App\Console\Commands;

use App\Models\CronTask;
use App\Service\CronTaskService;
use Illuminate\Console\Command;


class ProcessCron extends Command
{
    protected $signature = 'cron:process';

    public function handle(CronTaskService $service): int
    {

        while ($task = $service->fetchNextTask()) {
            $this->processTask($service, $task);
        }
        return 0;
    }
    protected function processTask(CronTaskService $service, CronTask $task): void
    {
        try {
            $this->call($task->command);
            $service->completeTask($task);
        } catch (\Exception $e) {
            $service->completeTask($task, false);
        }
    }
}
