<?php

namespace App\Validators;

class DistanceValidator
{
    /**
     * @var string
     */
    protected $error;

    public function getError()
    {
        return $this->error;
    }

    /**
     * Validate Input Parameters.
     *
     * @param float $initialLatitude
     * @param float $initialLongitude
     * @param float $finalLatitude
     * @param float $finalLongitude
     *
     * @return array
     */
    public function validate($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude)
    {
        if ($initialLatitude == $finalLatitude && $initialLongitude == $finalLongitude) {
            $this->error = 'REQUESTED_ORIGIN_DESTINATION_SAME';
        } elseif (!$initialLatitude || !$initialLongitude || !$finalLatitude || !$finalLongitude) {
            $this->error = 'REQUEST_PARAMETER_MISSING';
        } elseif ( $initialLatitude < -90 || $initialLatitude > 90 || $finalLatitude < -90 || $finalLatitude > 90 ) {
            $this->error = 'LATITUDE_OUT_OF_RANGE';
        } elseif ( $initialLongitude < -180 || $initialLongitude > 180 || $finalLongitude < -180 || $finalLongitude > 180 ) {
            $this->error = 'LONGITUDE_OUT_OF_RANGE';
        } elseif ( !is_numeric($initialLatitude) || !is_numeric($finalLatitude) || !is_numeric($initialLongitude) || !is_numeric($finalLongitude) ) {
            $this->error = 'INVALID_PARAMETERS';
        }

        return $this->error ? false : true;
    }
}
