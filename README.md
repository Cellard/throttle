# Laravel Throttle Service

May throttle any event you need. E.g. sending sms or requests from exact IP address.

## Installation

    composer require Cellard/throttle

Register Service Provider in `config/app.php` file.

```php
'providers' => [
    /**
     * Third Party Service Providers...
     */
    Cellard\Throttle\ThrottleServiceProvider::class
],
```

Publish the package config file and migrations to your application. Run these commands inside your terminal.

    php artisan vendor:publish --provider="Cellard\Throttle\ThrottleServiceProvider"
    
And also run migrations.

    php artisan migrate
    
## Setup

Set up your throttle service.

```php
class ThrottleSms extends Cellard\Throttle\ThrottleService
{
    public function rules()
    {
        return [
            '1:60', // one sms per minute
            '3:300', // 3 sms per 5 minutes
        ];

        // Or you may use helper
        return [
            $this->everyMinute(),
            $this->everyFiveMinutes(3)
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

### Error messages

By default error messages looks like `Next :event after :interval`

    Next sms after 23 hours 32 minutes 13 seconds

You may define custom error messages.

```php
class ThrottleSms extends Cellard\Throttle\ThrottleService
{
    public function rules()
    {
        return [
            '1:60' => 'You may send only one SMS per minute',
            '3:300' => 'You may send no more than three SMS in five minutes'
        ];
    }
}
```

Placeholders:

- limit — number of events (defined in rule)
- seconds — number of seconds (defined in rule)
- event — name of service (defined in config file)
- interval - `CarbonInterval` object

## Usage

```php
$throttle = Throttle::event('sms')->subject('+70001234567');

if ($throttle->allow()) {

    // Your code to send SMS here


    // Do not forget to register an event
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

    Throttle::event('sms')
        ->subject('+70001234567')
        ->try();

    // Your code to send SMS here

} catch (\Cellard\Throttle\ThrottlingException $exception) {

    // Show error message
    $exception->getMessage();

    // Show the time, next sms is allowed
    $exception->getNext();
}
```

## What is `subject`?

You may check availability without subject.

```php
Throttle::event('sms')->try();
```

It means that service will check limitations without reference to the exact phone number.

Subject means that service will check limitations per phone.

```php
Throttle::event('sms')->subject('+70001234567')->try();
```

## Pick up your room before you go out

Throttle Service stores records in its table, and you may want to clear it.

    php artisan throttle:clean

Will remove obsolete records.

You may schedule it to run once a day or week...
