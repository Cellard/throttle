# Laravel Throttle Service

May throttle any event you need. E.g. sending sms or requests from exact IP address.

## Installation

    composer require Cellard/throttle

Publish the package config file and migrations to your application. Run these commands inside your terminal.

    php artisan vendor:publish --provider="Cellard\Throttle\ThrottleServiceProvider"
    
And also run migrations.

    php artisan migrate
    
## Setup

Set up your throttle service.

```php
class ThrottleSms extends Cellard\Throttle\ThrottleService
{
    protected function rules()
    {
        return [
            '1:60', // one sms per minute
            '3:300', // 3 sms per 5 minutes
        ];
    }

    protected function messages()
    {
        return [
            '1:60' => 'You may send only one SMS per minute',
            '3:300' => 'You may send no more than three SMS in five minutes'
        ];
    }
}
```

Register your service in `config/throttle.php`.

```php
return [
    'events' => [
        'sms' => ThrottleSms::class
    ]
];
```

## Usage

```php
$throttle = Throttle::event('sms')->subject('+70001234567');

if ($throttle->allow()) {

    // Send sms

    $throttle->hit();

} else {

    // Show error message
    $throttle->error();

    // Show the time, next sms is allowed
    $throttle->next();

}
```

Or in try-catch style

```php
try {

    Throttle::event('ip')
        ->subject($_SERVER['REMOTE_ADDR'])
        ->try();

    // handle the request from user

} catch (\Cellard\Throttle\ThrottlingException $exception) {

    // Show error message
    $exception->getMessage();

    // Show the time, next request is allowed
    $exception->getNext();
}
```

## What is `subject`?

You may check availability without subject.

```php
Throttle::event('sms')->try();
```

It means that your service has global limitations without reference to the exact phone number.

Subject means, that your service has limitations per phone.

```php
Throttle::event('sms')->subject('+70001234567')->try();
```

