<?php


namespace Cellard\Throttle;


use Illuminate\Support\Carbon;
use Throwable;

/**
 * Action is not allowed die to throttle rules
 * @package Cellard\Throttle
 */
class ThrottlingException extends \Exception
{
    protected $next;
    public function __construct($message, Carbon $next = null, Throwable $previous = null)
    {
        parent::__construct($message, null, $previous);
        $this->next = $next;
    }

    /**
     * Next run allowed
     * @return Carbon
     */
    public function getNext(): Carbon
    {
        return $this->next;
    }

}