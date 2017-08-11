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

    // optional, defaults to 90
    'retention_days' => 3
]
```
