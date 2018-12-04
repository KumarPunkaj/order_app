<?php

namespace app\Helpers;

class GoogleMap
{
    /**
     * Gets the distance from google api.
     *
     * @params string $origin
     * @params string destination
     *
     * @return int
     */
    public function getDistance($origin, $destination)
    {
        return 10;
        $googleApiKey = env('GOOGLE_API_KEY');

        $queryString =  env('GOOGLE_API_URL') . "?units=imperial&origins=" . $origin . "&destinations=" . $destination . "&key=" . $googleApiKey;

        $data = file_get_contents($queryString);

        $data = json_decode($data);

        if ( !$data || $data->status == 'REQUEST_DENIED' || $data->status == 'OVER_QUERY_LIMIT' || $data->status == 'NOT_FOUND' || $data->status == 'ZERO_RESULTS' ) {
            return (isset($data->status)) ? $data->status : 'GOOGLE_API_NULL_RESPONSE';
        }

        $distanceValue = (int)$data->rows[0]->elements[0]->distance->value;

        return $distanceValue;
    }
}
