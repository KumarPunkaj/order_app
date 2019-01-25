<?php

namespace App\Helpers;

class DistanceHelper
{
    /**
     * @var \App\Library\Distance\GoogleDistanceMatrix
     */
    protected $googleDistanceMatrix;

    /** @param \App\Helpers\GoogleMap $googleMapHelper */
    public function __construct(
        \App\Library\Distance\GoogleDistanceMatrix $googleDistanceMatrix
    ) {
        $this->googleDistanceMatrix = $googleDistanceMatrix;
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
        return $this->googleDistanceMatrix->getDistance($origin, $destination);
    }
}
