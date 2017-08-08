Legal Things - CloudWatch Logger PHP
==================

This library provides you with a simplified interface to log data to AWS CloudWatch.


## Requirements

- [PHP](http://www.php.net) >= 5.5.0

_Required PHP extensions are marked by composer_


## Installation

The library can be installed using composer.

    composer require legalthings/cloudwatch-logger-php

## Output

![output](https://user-images.githubusercontent.com/5793511/29072119-c16b1ce2-7c46-11e7-87e1-bde855aa279e.png)


## Usage

```php
use LegalThings/CloudWatchLogger;

$config = [
    'aws' => [
        'version' => 'latest',
        'region' => 'eu-west-1',
        'credentials' => [
            'key' => 'my_key',
            'secret' => 'my_secret'
        ]
    ],
    'group_name' => 'group_name',
    'instance_name' => 'instance_name',
    'channel_name' => 'channel_name'
];

$logger = new CloudWatchLogger($config);

$logger->info('test_info', ['hello' => 'world']);
/*
  outputs within the group 'group_name' and instance 'instance_name' on CloudWatch:
  
  [2017-08-08 13:23:44] channel_name.INFO: my_notice
   {
      "hello": "world"
   }
*/

$logger->notice('test_notice', ['foo' => 'bar']);
$logger->warn('test_warn', ['flag' => true]);
$logger->error('test_error', ['line' => 111]);
$logger->debug('test_debug', ['debugging' => false]);
```


## Configuration

```php
[
    // required
    'aws' => [
        // required
        'version' => 'latest',

        // required
        'region' => 'eu-west-1',

        // optional, credentials may be omitted if using aws environment variables or roles
        'credentials' => [
            'key' => 'my_key',
            'secret' => 'my_secret'
        ]
    ],

    // required
    'group_name' => 'group_name',

    // required
    'instance_name' => 'instance_name',

    // optional
    'channel_name' => 'channel_name',

    // optional, defaults to 90
    'retention_days' => 3,

    // optional, defaults to 10000 and may not be greater than 10000
    'batch_size' => 5000,

    // optional
    'tags' => [
        'application' => 'php-test-app-1'
    ],

    // optional, defaults to '[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n'
    'format' => '[%datetime%] %message% %context%\n'
]
```
