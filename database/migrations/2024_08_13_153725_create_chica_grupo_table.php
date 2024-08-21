<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pivot table for users and grupos
        Schema::create('group_operator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->enum('shift', ['morning', 'afternoon', 'night']);
            $table->unique(['group_id', 'shift']);
            $table->timestamps();
        });
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->enum('shift', ['morning', 'afternoon', 'night']);
            $table->date('date');
            $table->integer('points');
            $table->integer('goal');
            $table->timestamps();
        });

    }

    public function down()
    {

    }
};
