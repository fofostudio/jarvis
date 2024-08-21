<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakLogsTable extends Migration
{
    public function up()
    {
        Schema::create('break_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('start_time');
            $table->dateTime('expected_end_time');
            $table->dateTime('actual_end_time')->nullable();
            $table->integer('overtime')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('break_logs');
    }
}
