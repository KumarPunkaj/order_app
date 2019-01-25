<?php

namespace App\Http\Factory;

class DistanceFactory
{
    /**
     * @return App\Http\Models\Distance
     */
    public static function create(\App\Helpers\DistanceHelper $distanceHelper)
    {
        $model = new \App\Http\Models\Distance();
        $model->setDistanceHelper($distanceHelper);

        return $model;
    }
}
