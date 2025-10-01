<?php

use App\Country;
use App\Http\Resources\CountryResource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AvailableCountriesTableSeeder extends Seeder
{
    /**
     * List countries
     *
     * @var array
     */
    protected $availableCountries =
    [
        // Data ini sudah benar
        [
            "name" => 'China',
            "country_code" => 'CHN'
        ],
        [
            "name" => 'Indonesia',
            "country_code" => 'IDN'
        ],
        // ... (data negara lainnya tetap sama)
        [
            "name" => 'Spain',
            "country_code" => 'ES'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PERBAIKAN 1: Ganti 'country_code' menjadi 'code' di SELECT
        $datasFromDB = Country::select('name', 'code')->get()->toArray();
        
        foreach ($this->availableCountries as $data) {
            $name = $data['name'];
            $country_code_from_array = $data['country_code']; // Ambil dari array sumber
            
            // Perhatikan bahwa Anda mungkin perlu menyesuaikan array_column jika Anda ingin membandingkan kode
            // Kita akan menggunakan kode 2 huruf/3 huruf dari array $availableCountries
            
            // PERBAIKAN 2: Ganti 'country_code' menjadi 'code' di array_column
            if (array_search($name, array_column($datasFromDB, 'name')) === false && array_search($country_code_from_array, array_column($datasFromDB, 'code')) === false) {
                Country::create(
                    [
                        'name' => $name,
                        // PERBAIKAN 3: Ganti 'country_code' menjadi 'code' saat INSERT
                        'code' => $country_code_from_array 
                    ]
                );
            }
        }
    }
}