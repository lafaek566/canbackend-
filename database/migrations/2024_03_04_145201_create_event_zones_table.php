<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('zone_name')->nullable(); // Ditambahkan dari kode terakhir Anda
            
            // PERBAIKAN FOREIGN KEY: bigInteger dan unsigned untuk kompatibilitas
            $table->bigInteger('country_id')->unsigned(); 
            
            $table->decimal('lat', 10, 8); // Ditambahkan dari kode Anda
            $table->decimal('lng', 11, 8); // Ditambahkan dari kode Anda
            $table->boolean('is_active')->default(true); // Ditambahkan dari kode Anda
            
            $table->timestamps();
            
            // Definisi Foreign Key
            $table->foreign('country_id')
                  ->references('id')->on('countries')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_zones');
    }
}