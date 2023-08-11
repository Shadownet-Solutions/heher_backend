<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->string('status')->nullable();
            $table->string('religion')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('children')->nullable();
            $table->string('smoke')->nullable();
            $table->string('drink')->nullable();
            $table->string('education')->nullable();
            $table->string('address')->nullable();
            $table->string('cordinates')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->boolean('premium')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        
    }

    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
