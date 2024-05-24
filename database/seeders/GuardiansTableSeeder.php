<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class GuardiansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('guardians')->insert([
                'parent_name' => $faker->name,
                'mobile' => $faker->phoneNumber,
                'secondary_mobile' => $faker->phoneNumber,
                'city' => $faker->city,
                'town' => $faker->city,
                'address' => $faker->address,
            ]);
        }
    
    }
}
