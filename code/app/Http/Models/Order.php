<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const UNASSIGNED_ORDER_STATUS = 'UNASSIGN';
    const ASSIGNED_ORDER_STATUS = 'TAKEN';

    protected $table = 'orders';

    /**
     * @return \App\Http\Models\Distance
     */
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
     * Update order status from UNASSIGNED to TAKEN if order is not already taken
     *
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

    /**
     * Fetches a order model using its primary key
     *
     * @param int $id
     *
     * @return self
     */
    public function getOrderById($id)
    {
        return self::findOrFail($id);
    }
}
