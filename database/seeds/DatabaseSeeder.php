<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan UserTableSeeder dipanggil di sini!
        $this->call([
            AvailableCountriesTableSeeder::class,
            UserTableSeeder::class // <--- TAMBAHKAN BARIS INI
        ]);
    }
}