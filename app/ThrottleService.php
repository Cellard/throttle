<?php


namespace Cellard\Throttle;

use Cellard\Throttle\Exceptions\ThrottlingException;
use Illuminate\Support\Carbon;

abstract class ThrottleService
{
    /**
     * Value to filter records in database
     * @var string
     */
    protected $action;
    protected $driver;
    protected $subject;

    protected $lastRule;

    public function __construct($driver)
    {
        $this->driver = $driver;
        $this->action = $driver;
    }

    /**
     * Set scope
     * @param string $subject
     * @return static
     */
    public function subject($subject)
    {
        $this->action = $this->driver . '.' . $subject;

        return $this;
    }

    /**
     * Throttle rules
     *
     * [<br>
     *    '1:60', // one hit per minute<br>
     *    '3:300', // 3 hits per 5 minutes<br>
     * ]
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Custom error messages
     *
     * ['1:60' => 'You may send just one sms per minute']
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Check one rule
     * @param $rule
     * @return bool
     */
    protected function checkRule($rule)
    {
        $this->lastRule = $rule;

        // 1, 60
        list($limit, $interval) = explode(':', $rule);

        return $this->builder($interval)->count() < $limit ? true : false;
    }

    /**
     * @param integer $interval seconds
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function builder($interval)
    {
        return Throttle::after(Carbon::parse("-{$interval} seconds"))
            ->where('action', $this->action);
    }

    protected function getMessage($rule)
    {
        $messages = $this->messages();

        if (!($message = @$messages[$rule])) {
            list($hits, $interval) = explode(':', $rule);

            $message = trans("Only allowed :hits event(s) in :interval seconds", ['hits' => $hits, 'interval' => $interval]);
        }

        return $message;
    }

    /**
     * Check if action is allowed
     * @return boolean
     */
    public function allow()
    {
        foreach ($this->rules() as $rule) {
            if (!$this->checkRule($rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get last error message
     * @return \Illuminate\Contracts\Translation\Translator|string
     */
    public function error()
    {
        return $this->getMessage($this->lastRule);
    }

    /**
     * Register new action
     */
    public function hit()
    {
        $throttle = new Throttle();
        $throttle->action = $this->action;
        $throttle->save();
    }

    /**
     * Next run allowed
     * @return Carbon
     */
    public function next()
    {
        if ($this->lastRule) {
            list($hits, $interval) = explode(':', $this->lastRule);

            /** @var Throttle $first */
            $first = $this->builder($interval)->orderBy('created_at')->first();

            // eg
            // rule has interval 300 seconds
            // event was 240 seconds ago
            // so next run allowed in 60 seconds

            $diff = $interval - Carbon::now()->diffAsCarbonInterval($first->created_at)->seconds;

            return Carbon::parse("{$diff} seconds");
        } else {
            return Carbon::now();
        }
    }

    /**
     * Check if action is allowed in try-catch style and register the event
     * @throws ThrottlingException
     */
    public function try()
    {
        if (!$this->allow()) {
            throw new ThrottlingException($this->error(), $this->next());
        } else {
            $this->hit();
        }
    }
}