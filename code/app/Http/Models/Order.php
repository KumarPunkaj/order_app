<?php

namespace app\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const UNASSIGNED_ORDER_STATUS = 'UNASSIGN';
    const ASSIGNED_ORDER_STATUS = 'TAKEN';

    protected $table = 'orders';

    //Add a virtual colum distance value
    protected $distanceValue =  null;

    public function distanceModel()
    {
        return $this->hasOne('App\Http\Models\Distance', 'id', 'distance_id');
    }

    /**
     * @return null|int
     */
    public function getDistanceValue()
    {
        return $this->distanceValue ? $this->distanceValue : $this->distanceModel->distance;
    }

    /**
     * @param int $value
     *
     * @return self
     */
    public function setDistanceValue($value)
    {
        $this->distanceValue = (int) $value;

        return $this;
    }
}
