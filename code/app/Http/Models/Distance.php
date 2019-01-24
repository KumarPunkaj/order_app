<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    /** @var \App\Helpers\Helper */
    protected $helper;

    protected $table = 'distance';

    /** @param \App\Helpers\Helper $helper */
    public function __construct()
    {
        $this->helper = \App::make('App\Helpers\Helper');
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'origin' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (count($value) !== 2 || empty($value[0]) || empty($value[1])) {
                        $fail('INVALID_PARAMETERS');
                    }
                },
            ],
            'destination' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (count($value) !== 2 || empty($value[0]) || empty($value[1])) {
                        $fail('INVALID_PARAMETERS');
                    }
                },
            ],
        ];
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

            $distanceBetween = $this->helper->getDistance($origin, $destination);

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
