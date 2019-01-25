<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    /** @var \App\Helpers\DistanceHelper */
    protected $distanceHelper;

    protected $table = 'distance';

    /**
     * @param \App\Helpers\DistanceHelper $distanceHelper
     *
     * @return self
     */
    public function setDistanceHelper(\App\Helpers\DistanceHelper $distanceHelper)
    {
        $this->distanceHelper = $distanceHelper;

        return $this;
    }

    /**
     * @param string $initialLatitude
     * @param string $initialLongitude
     * @param string $finalLatitude
     * @param string $finalLongitude
     *
     * @return self
     */
    public function getOrSetDistance($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude)
    {
        //Seaching distance first to reduce google API calls
        $distance = self::where([
            ['initial_latitude', '=', $initialLatitude],
            ['initial_longitude', '=', $initialLongitude],
            ['final_latitude', '=', $finalLatitude],
            ['final_longitude', '=', $finalLongitude],
        ])->first();

        //If model is not available, create a new one
        if (null === $distance) {
            $origin = $initialLatitude . "," . $initialLongitude;
            $destination = $finalLatitude . "," . $finalLongitude;

            $distanceBetween = $this->distanceHelper->getDistance($origin, $destination);

            if (!is_int($distanceBetween)) {
                return $distanceBetween;
            }

            //inserting data in distance table
            $distance = new Distance;
            $distance->initial_latitude = $initialLatitude;
            $distance->initial_longitude = $initialLongitude;
            $distance->final_latitude = $finalLatitude;
            $distance->final_longitude = $finalLongitude;
            $distance->distance = $distanceBetween;
            $distance->save();
        }

        return $distance;
    }
}
