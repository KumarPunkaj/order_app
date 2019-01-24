<?php

namespace App\Helpers;

use App\Http\Models\Distance;
use Illuminate\Support\Facades\DB;

class Helper
{
    /**
     * @var App\Helpers\GoogleMap
     */
    protected $googleMapHelper;

    /** @param \App\Helpers\GoogleMap $googleMapHelper */
    public function __construct(
        \App\Helpers\GoogleMap $googleMapHelper
    ) {
        $this->googleMapHelper = $googleMapHelper;
    }

    /**
     * Gets the distance from google api.
     *
     * @param string $origin
     * @param string destination
     *
     * @return int
     */
    public function getDistance($origin, $destination)
    {
        return $this->googleMapHelper->getDistance($origin, $destination);
    }
}
