<?php

namespace App\Http\Services;

use App\Http\Models\Distance;
use App\Http\Models\Order as OrderModel;
use Illuminate\Http\JsonResponse;
use App\Validators\DistanceValidator;
use Illuminate\Database\Eloquent\Model;

class Order
{
    /**
     * @var null|string
     */
    public $error = null;

    /**
     * @var int
     */
    public $errorCode;

    /**
     * @var DistanceValidator
     */
    protected $distanceValidator;


    public function __construct(DistanceValidator $distanceValidator)
    {
        $this->distanceValidator = $distanceValidator;
    }

    /**
     * @param Request $requestData
     *
     * @return Order|false
     */
    public function createOrder($requestData)
    {
        $initialLatitude = $requestData->origin[0];
        $initialLongitude = $requestData->origin[1];
        $finalLatitude = $requestData->destination[0];
        $finalLongitude = $requestData->destination[1];

        $validateDistanceParameter = $this->distanceValidator
            ->validate($initialLatitude,
                $initialLongitude,
                $finalLatitude,
                $finalLongitude
            );

        if (!$validateDistanceParameter) {
            $this->error = $this->distanceValidator->getError();
            $this->errorCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

            return false;
        }

        $distance = $this->getDistance($initialLatitude, $initialLongitude, $finalLatitude,
                    $finalLongitude);

        if(!$distance instanceof \App\Http\Models\Distance) {
            $this->error = $distance;
            $this->errorCode = JsonResponse::HTTP_BAD_REQUEST;

            return false;
        }

        //Create new record
        $order = new OrderModel();
        $order->status = OrderModel::UNASSIGNED_ORDER_STATUS;
        $order->distance_id = $distance->id;
        $order->setDistanceValue($distance->distance);
        $order->save();

        return $order;
    }

    public function getDistance(
        $initialLatitude,
        $initialLongitude,
        $finalLatitude,
        $finalLongitude
    ) {
        $model = new Distance;

        return $model->getOrSetDistance($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude);
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getList($page, $limit)
    {
        $skip = ($page -1) * $limit;

        return (new OrderModel())->with('distanceModel')->skip($skip)->take($limit)->orderBy('id', 'asc')->get();

        return $orders;
    }
}
