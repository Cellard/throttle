<?php


namespace Cellard\Throttle;


class ExampleThrottleService extends ThrottleService
{
    protected function rules()
    {
        return [
            '1:60', // one hit per minute
            '3:300', // 3 hits per 5 minutes
        ];
    }

    protected function messages()
    {
        return [
            '1:60' => 'Only one example per minute is allowed',
            '3:300' => 'No more than three examples in five minutes are allowed'
        ];
    }
}