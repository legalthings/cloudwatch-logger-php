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
    'stream_name' => 'stream_name'
];

$logger = new CloudWatchLogger($config);

$logger->log(['hello' => 'world']);
/*
  outputs within the group 'group_name' and instance 'stream_name' on CloudWatch:

   {
      "hello": "world"
   }
*/
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
    'stream_name' => 'stream_name',

    // optional
    'options' => [
        // defaults to infinite
        'retention_days' => 7,

        // retry logging when receiving error (invalid token sequence exception), defaults to 5
        'error_max_retry' => 3,

        // delay to wait for before retrying logging in microseconds, defaults to 100000 microseconds (0.1 seconds)
        'error_retry_delay' => 0
    ]
]
```
