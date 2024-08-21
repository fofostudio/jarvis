<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Users Migration
return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->string('identification')->unique();
            $table->string('username')->unique();
            $table->date('birth_date');
            $table->string('phone');
            $table->string('address');
            $table->string('neighborhood');
            $table->date('entry_date');
            $table->enum('role', ['super_admin', 'admin', 'operator'])->default('operator');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
