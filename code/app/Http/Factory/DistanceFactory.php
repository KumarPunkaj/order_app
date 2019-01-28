<?php

namespace App\Http\Factory;

class DistanceFactory
{
    /**
     * Create distance model with injecting distance helper dependency using method injection
     *
     * @return App\Http\Models\Distance
     */
    public static function create(\App\Helpers\DistanceHelper $distanceHelper)
    {
        $model = new \App\Http\Models\Distance();
        $model->setDistanceHelper($distanceHelper);

        return $model;
    }
}
