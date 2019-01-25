<?php

namespace App\Library\Distance;

class GoogleDistanceMatrix implements DistanceMatrixInterface
{
    /**
     * {@inheritedDocs}
     */
    public function getDistance($origin, $destination)
    {
        $googleApiKey = env('GOOGLE_API_KEY');

        $queryString =  env('GOOGLE_API_URL') . "?units=imperial&origins=" . $origin . "&destinations=" . $destination . "&key=" . $googleApiKey;

        try {
            $data = file_get_contents($queryString);

            $data = json_decode($data);

            if ( !$data || $data->status == 'REQUEST_DENIED' || $data->status == 'OVER_QUERY_LIMIT' || $data->status == 'NOT_FOUND' || $data->status == 'ZERO_RESULTS' ) {
                return (isset($data->status)) ? $data->status : 'GOOGLE_API_NULL_RESPONSE';
            }

            return (int) $data->rows[0]->elements[0]->distance->value;
        } catch (\Exception $e) {
            return (isset($data->rows[0]->elements[0]->status)) ? $data->rows[0]->elements[0]->status : 'GOOGLE_API_NULL_RESPONSE';
        }
    }
}
