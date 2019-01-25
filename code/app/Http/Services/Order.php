<?php

namespace App\Http\Services;

use App\Http\Factory\DistanceFactory;
use App\Http\Models\Distance;
use App\Http\Models\Order as OrderModel;
use App\Validators\DistanceValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use App\Helpers\DistanceHelper;

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


    /**
     * @var DistanceHelper
     */
    protected $distanceHelper;


    public function __construct(DistanceValidator $distanceValidator, DistanceHelper $distanceHelper)
    {
        $this->distanceValidator = $distanceValidator;
        $this->distanceHelper = $distanceHelper;
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
            $this->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

            return false;
        }

        //Create new record
        $order = new OrderModel();
        $order->status = OrderModel::UNASSIGNED_ORDER_STATUS;
        $order->distance_id = $distance->id;
        $order->distance_value = $distance->distance;
        $order->save();

        return $order;
    }

    public function getDistance(
        $initialLatitude,
        $initialLongitude,
        $finalLatitude,
        $finalLongitude
    ) {
        $model = DistanceFactory::create($this->distanceHelper);

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
        $page = (int) $page;
        $limit = (int) $limit;
        $orders = [];

        if ($page > 0 && $limit > 0) {
            $skip = ($page -1) * $limit;
            $orders = (new OrderModel())->skip($skip)->take($limit)->orderBy('id', 'asc')->get();
        }

        return $orders;
    }
}
