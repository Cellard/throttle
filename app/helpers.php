<?php

if (! function_exists('throttle')) {
    /**
     * Get throttle service.
     * @param $driver
     * @return \Cellard\Throttle\ThrottleService
     */
    function throttle($driver)
    {
        return \Cellard\Throttle\Throttle::event($driver);
    }
}