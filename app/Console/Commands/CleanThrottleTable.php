<?php

namespace Cellard\Throttle\Console\Commands;

use Carbon\Carbon;
use Cellard\Throttle\Throttle;
use Illuminate\Console\Command;

class CleanThrottleTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'throttle:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean throttle table removing obsolete records';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (config("throttle.events") as $event) {
            $throttler = Throttle::event($event);
            $interval = 0;
            foreach ($throttler->rules() as $rule) {
                list($limit, $int) = explode(':', $rule);
                $interval = max($interval, $int);
            }

            Throttle::before(Carbon::parse("-{$interval} seconds"))
                ->where('action', $event)
                ->delete();
        }
    }
}