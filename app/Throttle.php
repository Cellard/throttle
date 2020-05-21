<?php

namespace Cellard\Throttle;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Throttle Facade and Model
 * @package Cellard\Throttle
 *
 * @property int $id
 * @property string $action
 * @property Carbon $created_at
 *
 * @method static static|Builder after($datetime)
 * @method static static|Builder before($datetime)
 */
class Throttle extends Model
{

    protected $table = 'throttle';
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

    public function scopeAfter(Builder $query, Carbon $datetime)
    {
        $query
            ->whereDate('created_at', '>=', $datetime);
    }
    public function scopeBefore(Builder $query, Carbon $datetime)
    {
        $query
            ->whereDate('created_at', '<', $datetime);
    }
}