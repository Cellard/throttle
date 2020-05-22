<?php

namespace Cellard\Throttle\Console\Commands;

use Cellard\Throttle\Throttle;
use Cellard\Throttle\ThrottleRule;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

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
        foreach (config("throttle.events") as $event => $class) {
            $throttler = Throttle::event($event);

            /** @var ThrottleRule $rule */
            $rule = $throttler->getRules()->first();

            Throttle::before(Carbon::parse("-{$rule->seconds} seconds"))
                ->where('action', $event)
                ->delete();

            $this->info($event . '... clean');
        }
    }
}