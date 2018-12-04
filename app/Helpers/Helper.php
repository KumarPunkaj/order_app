<?php

namespace App\Helpers;

use App\Http\Models\Distance;
use Illuminate\Support\Facades\DB;

class helper
{
    protected $latLangInfoArray;
    protected $distanceMatrix;

    public function __construct(
        \App\Helpers\GoogleMap $googleMapHelper
    ) {
        $this->googleMapHelper = $googleMapHelper;
    }

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
        return $this->googleMapHelper->getDistance($origin, $destination);
    }
}
