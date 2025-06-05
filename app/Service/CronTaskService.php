<?php

namespace App\Service;

use App\Models\CronLock;
use App\Models\CronTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CronTaskService
{
    private string $serverId;
    /**
    * Конструктор
    **/
    public function __construct()
    {
        $this->serverId = gethostname() . '-' . Str::random(4);
    }
    /**
     * Фкнцкия создания новой задачи CRON
     **/
    public function createTask(string $command, string $schedule): CronTask
    {
        $this->validateSchedule($schedule);

        return DB::transaction(function () use ($command, $schedule) {
            return CronTask::create([
                'command' => trim($command),
                'schedule' => $schedule,
                'is_running' => false
            ]);
        });
    }
    /**
     * Функция получения следующей доступной задачи CRON
     **/
    public function fetchNextTask(): ?CronTask
    {
        return DB::transaction(function () {
            $task = CronTask::where('is_running', false)
                ->where(function ($query) {
                    $query->whereNull('last_run')
                        ->orWhere('last_run', '<=', now()->subMinutes(1));
                })
                ->lockForUpdate()
                ->first();

            if ($task) {
                $this->acquireLock($task);
                $task->update(['is_running' => true]);
            }

            return $task;
        });
    }

    /**
     * Функция поментки здачи, то что она выполнена
     **/
    public function completeTask(CronTask $task, bool $success = true): void
    {
        DB::transaction(function () use ($task, $success) {
            $task->update([
                'is_running' => false,
                'last_run' => $success ? now() : $task->last_run
            ]);

            $task->locks()->delete();
        });
    }
    /**
     * Функция очистки блокированных задач, по таймауту(истекшие)
     **/
    public function cleanupExpiredLocks(int $timeoutMinutes = 5): int
    {
        return CronLock::where('locked_at', '<=', now()->subMinutes($timeoutMinutes))
            ->delete();
    }

    private function acquireLock(CronTask $task): \Illuminate\Database\Eloquent\Model
    {
        $this->cleanupExpiredLocks();

        return $task->locks()->create([
            'server_id' => $this->serverId,
            'locked_at' => now()
        ]);
    }

    private function validateSchedule(string $schedule): void
    {
        if (!preg_match('/^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])|\*\/([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])|\*\/([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])|\*\/([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])|\*\/([1-9]|1[0-2])) (\*|([0-6])|\*\/([0-6]))$/', $schedule)) {
            throw new InvalidArgumentException("Invalid cron schedule format: {$schedule}");
        }
    }
}
