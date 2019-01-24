<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const UNASSIGNED_ORDER_STATUS = 'UNASSIGN';
    const ASSIGNED_ORDER_STATUS = 'TAKEN';

    protected $table = 'orders';

    public function distanceModel()
    {
        return $this->hasOne('App\Http\Models\Distance', 'id', 'distance_id');
    }

    /**
     * @return null|int
     */
    public function getDistanceValue()
    {
        return $this->distance_value ? $this->distance_value : $this->distanceModel->distance;
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function takeOrder($orderId)
    {
        $affectedRows = self::where([
            ['id', '=', $orderId],
            ['status', '=', self::UNASSIGNED_ORDER_STATUS],
        ])
        ->update(['orders.status' => self::ASSIGNED_ORDER_STATUS]);

        return $affectedRows > 0 ? true : false;
    }
}
