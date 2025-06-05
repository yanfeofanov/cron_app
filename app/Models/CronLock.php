<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель блокировки задачи
 *
 * @property int $id
 * @property int $cron_task_id
 * @property string $server_id
 * @property DateTime $locked_at
 */

class CronLock extends Model
{
    protected $fillable = [
        'cron_task_id',
        'server_id',
        'locked_at'
    ];

    protected $casts = [
        'locked_at' => 'datetime'
    ];

    // Делаем функцию проверки связи один к одному
    public function task()
    {
        return $this->belongsTo(CronTask::class);
    }
}
