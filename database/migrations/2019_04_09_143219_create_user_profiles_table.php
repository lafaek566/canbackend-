<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique(); // Ditambahkan 'unique()' untuk memastikan satu user hanya punya satu profile
            
            // KOLOM YANG HILANG (PERBAIKAN)
            $table->string('avatar')->nullable(); 
            $table->string('banner')->nullable(); 
            // Kolom dari Seeder Anda yang lain (biography, phone_no, dll.) seharusnya juga ada di sini
            
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('skype')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
            
            // Memperbaiki klausa foreign key:
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}