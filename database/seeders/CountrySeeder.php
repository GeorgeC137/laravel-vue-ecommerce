<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usaStates = [
            'MI' => 'Miami',
            'FL' => 'Florida',
            'CH' => 'Chicago',
            'CA' => 'California',
            'AL' => 'ALaska',
        ];

        $countries = [
            ['code' => 'ke', 'name' => 'Kenya', 'states' => null ],
            ['code' => 'ca', 'name' => 'Canada', 'states' => null ],
            ['code' => 'de', 'name' => 'Denmark', 'states' => null ],
            ['code' => 'us', 'name' => 'USA', 'states' => json_encode($usaStates) ],
            ['code' => 'ch', 'name' => 'China', 'states' => null ],
        ];

        Country::insert($countries);
    }
}
