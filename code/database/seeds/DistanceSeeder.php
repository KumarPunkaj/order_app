<?php

use Illuminate\Database\Seeder;

class DistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ["initial_latitude" => "28.704060", "initial_longitude" => "77.102493", "final_latitude" => "28.535517", "final_longitude" => "77.391029", "distance" => 46732
            ],
            ["initial_latitude" => "28.704060", "initial_longitude" => "77.102493", "final_latitude" => "28.535517", "final_longitude" => "77.391044", "distance" => 912242
            ],
            ["initial_latitude" => "28.704060", "initial_longitude" => "77.102493", "final_latitude" => "28.535517", "final_longitude" => "77.391028", "distance" => 46731
            ]
        ];
        foreach ($locations as $disData) {
            DB::table('distance')->insert([
                'initial_latitude' => $disData['initial_latitude'],
                'initial_longitude' => $disData['initial_longitude'],
                'final_latitude' => $disData['final_latitude'],
                'final_longitude' => $disData['final_longitude'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'distance' => $disData['distance']
            ]);
        }

        $faker = Faker\Factory::create();
        for($i = 0; $i < 5; $i++) {
            $lat1 = $faker->latitude();
            $lat2 = $faker->latitude();
            $lon1 = $faker->longitude();
            $lon2 = $faker->longitude();
            $distance = $this->distance($lat1, $lon1, $lat2, $lon2);

            DB::table('distance')->insert([
                'initial_latitude' => $lat1,
                'initial_longitude' => $lon1,
                'final_latitude' => $lat2,
                'final_longitude' => $lon2,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'distance' => $distance
            ]);
        }

        $distances = DB::table('distance')->pluck('distance', 'id');

        DB::table('orders')->insert([
            'distance_id' => 1,
            'status' => 'TAKEN',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        foreach ($distances as $disID => $distanceValue) {
            for ($i=0; $i < 5 ; $i++) {
                DB::table('orders')->insert([
                    'distance_id' => $disID,
                    'status' => 'UNASSIGN',
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distanceInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return $distanceInMetre;
    }
}
