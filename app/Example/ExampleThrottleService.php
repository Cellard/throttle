<?php


namespace Cellard\Throttle\Example;


use Cellard\Throttle\ThrottleService;

class ExampleThrottleService extends ThrottleService
{
    public function rules()
    {
        return [
            $this->everyMinute() => 'Only one example per minute is allowed',
            $this->everyFiveMinutes(3) => 'No more than three examples in five minutes are allowed',
        ];
    }
}