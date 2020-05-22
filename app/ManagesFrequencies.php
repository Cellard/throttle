<?php


namespace Cellard\Throttle;


trait ManagesFrequencies
{
    /**
     * Limit number of events in number of minutes
     *
     * @param int $numberOfEvents
     * @param int $numberOfMinutes
     * @return string
     */
    public function everyMinute($numberOfEvents = 1, $numberOfMinutes = 1)
    {
        return $numberOfEvents . ':' . ($numberOfMinutes * 60);
    }

    /**
     * Limit number of events in five minutes
     *
     * @param int $numberOfEvents
     * @return string
     */
    public function everyFiveMinutes($numberOfEvents = 1)
    {
        return $this->everyMinute($numberOfEvents, 5);
    }

    /**
     * Limit number of events in ten minutes
     *
     * @param int $numberOfEvents
     * @return string
     */
    public function everyTenMinutes($numberOfEvents = 1)
    {
        return $this->everyMinute($numberOfEvents, 10);
    }

    /**
     * Limit number of events in fifteen minutes
     *
     * @param int $numberOfEvents
     * @return string
     */
    public function everyFifteenMinutes($numberOfEvents = 1)
    {
        return $this->everyMinute($numberOfEvents, 15);
    }

    /**
     * Limit number of events in thirty minutes
     *
     * @param int $numberOfEvents
     * @return string
     */
    public function everyThirtyMinutes($numberOfEvents = 1)
    {
        return $this->everyMinute($numberOfEvents, 30);
    }

    /**
     * Limit number of events in number of hours
     *
     * @param int $numberOfEvents
     * @param int $numberOfHours
     * @return string
     */
    public function hourly($numberOfEvents = 1, $numberOfHours = 1)
    {
        return $this->everyMinute($numberOfEvents, $numberOfHours * 60);
    }

    /**
     * Limit number of events in number of days
     *
     * @param int $numberOfEvents
     * @param int $numberOfDays
     * @return string
     */
    public function daily($numberOfEvents = 1, $numberOfDays = 1)
    {
        return $this->hourly($numberOfEvents, $numberOfDays * 24);
    }

    /**
     * Two events per day
     *
     * @return string
     */
    public function twiceDaily()
    {
        return $this->hourly(2, 24);
    }

    /**
     * Limit number of events in number of weeks
     *
     * @param int $numberOfEvents
     * @param int $numberOfWeeks
     * @return string
     */
    public function weekly($numberOfEvents = 1, $numberOfWeeks = 1)
    {
        return $this->daily($numberOfEvents, $numberOfWeeks * 7);
    }

    /**
     * Limit number of events in number of months
     *
     * @param int $numberOfEvents
     * @param int $numberOfMonths
     * @return string
     */
    public function monthly($numberOfEvents = 1, $numberOfMonths = 1)
    {
        return $this->daily($numberOfEvents, $numberOfMonths * 30);
    }

    /**
     * Two events per month
     *
     * @return string
     */
    public function twiceMonthly()
    {
        return $this->daily(2, 30);
    }
}