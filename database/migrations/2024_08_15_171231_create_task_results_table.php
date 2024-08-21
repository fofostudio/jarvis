<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskResultsTable extends Migration
{
    public function up()
    {
        Schema::create('task_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->string('platform_name');
            $table->text('result');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_results');
    }
}
