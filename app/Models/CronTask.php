<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Модель задачи для CRON
 *
 * @property int $id
 * @property string $command
 * @property string $schedule
 * @property bool is_running
 * @property Date last_run
 */
class CronTask extends Model
{
    protected $fillable = [
        'command',
        'schedule',
        'is_running',
        'last_run'
    ];

    protected $casts = [
        'is_running' => 'boolean',
        'last_run' => 'datetime'
    ];

    // Делаем функцию проверки связи один ко многим

    public function locks()
    {
        return $this->hasMany(CronLock::class);
    }
}
