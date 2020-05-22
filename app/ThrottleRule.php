<?php


namespace Cellard\Throttle;


use Carbon\CarbonInterval;

class ThrottleRule
{
    /**
     * @var integer
     */
    public $limit;
    /**
     * @var integer
     */
    public $seconds;
    /**
     * @var string
     */
    public $message;
    /**
     * @var CarbonInterval
     */
    public $interval;

    public function __construct($limit, $seconds, $message)
    {
        $this->limit = $limit;
        $this->seconds = $seconds;
        $this->message = $message;

        $this->interval = CarbonInterval::fromString("{$seconds}s");
    }
}