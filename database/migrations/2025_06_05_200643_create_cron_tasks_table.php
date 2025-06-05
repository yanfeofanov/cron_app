<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->string('schedule');
            $table->boolean('is_running')->default(false);
            $table->timestamp('last_run')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('is_running');
            $table->index('last_run');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_tasks');
    }
}
