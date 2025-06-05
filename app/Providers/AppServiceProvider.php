<?php

namespace App\Providers;



use App\Service\CronTaskService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CronTaskService::class, function () {
            return new CronTaskService();
        });
    }


}
