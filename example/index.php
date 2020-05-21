<?php

use Cellard\Throttle\Throttle;

$throttle = Throttle::event('sms')->subject('+70001234567');

if ($throttle->allow()) {

    // Do what you need

    $throttle->hit();

} else {

    $throttle->error();
    $throttle->next();

}

// Or

try {

    $throttle->try();

    // Do what you need

} catch (\Cellard\Throttle\ThrottlingException $exception) {
    $exception->getMessage();
    $exception->getNext();
}