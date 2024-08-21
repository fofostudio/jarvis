<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('girls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_id');
            $table->string('username');
            $table->string('password');
            $table->foreignId('platform_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('girls');
    }
};
