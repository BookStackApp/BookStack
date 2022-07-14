<?php

namespace Database\Seeders;
use BookStack\Entities\Models\Counties_model;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use File;
class CountySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Counties_model::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = File::get("counties.json");
        $countries = json_decode($json);
         foreach($countries as $country){
            Counties_model::create([
                'name' => $country->name,
            ]);
         };
    }
}
