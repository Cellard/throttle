<?php

namespace Cellard\Throttle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Throttle Facade and Model
 * @package Cellard\Throttle
 *
 * @property int $id
 * @property string $action
 * @property Carbon $created_at
 */
class Throttle extends Model
{
    /**
     * Get Throttle Service instance
     * @param string $driver
     * @return ThrottleService
     */
    public static function event($driver)
    {
        $service = config("throttle.events.{$driver}");
        return new $service();
    }
}