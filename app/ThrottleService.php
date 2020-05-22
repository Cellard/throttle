<?php


namespace Cellard\Throttle;

use Cellard\Throttle\Exceptions\ThrottlingException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class ThrottleService
{
    use ManagesFrequencies;

    /**
     * Value to filter records in database
     * @var string
     */
    protected $action;
    protected $driver;
    protected $subject;

    /**
     * Last checked rule
     * @var ThrottleRule
     */
    protected $rule;

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
     * Check one rule
     * @param ThrottleRule $rule
     * @return bool
     */
    protected function check($rule)
    {
        $this->rule = $rule;
        return $this->builder($rule->seconds)->count() < $rule->limit ? true : false;
    }

    /**
     * @param integer $interval seconds
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function builder($interval)
    {
        return Throttle::after(Carbon::parse("-{$interval} seconds"))
            ->where('action', 'like', $this->action);
    }

    /**
     * Check if action is allowed
     * @return boolean
     */
    public function allow()
    {
        foreach ($this->getRules() as $rule) {
            if (!$this->check($rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Collection|ThrottleRule[]
     */
    public function getRules()
    {
        $rules = new Collection();
        foreach ($this->rules() as $rule => $message) {

            if (!is_string($rule)) {
                $rule = $message;
                $message = "Next :event after :interval";
            }
            list($limit, $seconds) = explode(':', $rule);

            $rules->push(new ThrottleRule($limit, $seconds, $message));
        }

        // Bigger intervals on top
        return $rules->sort(function (ThrottleRule $one, ThrottleRule $two) {
            return -($one->seconds - $two->seconds);
        });
    }

    /**
     * Get last error message
     * @return \Illuminate\Contracts\Translation\Translator|string|null
     */
    public function error()
    {
        if ($rule = $this->rule) {
            return trans($rule->message, [
                'limit' => $rule->limit,
                'interval' => $this->next()->diffAsCarbonInterval(),
                'event' => $this->driver,
                'seconds' => $rule->seconds
            ]);
        }

        return null;
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
        if ($rule = $this->rule) {

            /** @var Throttle $first */
            $first = $this->builder($rule->seconds)->orderBy('created_at')->first();

            // eg
            // rule has interval 300 seconds
            // event was 240 seconds ago
            // so next run allowed in 60 seconds

            $ago = Carbon::now()->diffAsCarbonInterval($first->created_at)->totalSeconds;
            $diff = (integer)round($rule->seconds - $ago);

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