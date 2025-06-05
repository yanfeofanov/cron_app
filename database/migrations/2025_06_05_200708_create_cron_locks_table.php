<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cron_task_id')->constrained()->cascadeOnDelete();
            $table->string('server_id');
            $table->timestamp('locked_at');
            $table->timestamps();

            $table->index('server_id');
            $table->index('locked_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cron_locks');
    }
}
