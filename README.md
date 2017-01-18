# Pluggit Monitoring

Monitoring is a monitor factory system, which gives you ability to log information.

## Instalation

Add this repo to your composer.json

````json
"repositories": {
  "pluggit/monitoring": {
    "type": "vcs",
    "url": "git@github.com:CMProductions/monitoring.git"
  }
}
````

Then require it as usual:

``` bash
composer require "pluggit/monitoring"
```

To create factory, set default channel name as first parameter and Formatter as second one.
```php
<?php
use Cmp\Monitoring\Event\EventFactory;
use Cmp\Monitoring\Metric\MetricFactory;
use Cmp\Monitoring\Monitor;
use Psr\Log\LoggerInterface;

/**
* @var LoggerInterface $logger
**/
$logger;
$metricFactory = new MetricFactory($app['monitoring.default_tags']);
$eventFactory = new EventFactory('host_name' [
                                'env' => $app['environment'],
                                'mode' => $app['mode']
                            ]);

$logger = new Monitor($metricFactory, $eventFactory, $logger);
```
## Handlers
### Adding senders
Sender should implement ``Cmp\Monitoring\Metric\SenderInterface``
The library provides two types of senders, DataDox and Monolog
To add sender, use ``pushMetricSender()`` method
```php=
$monitor->pushMetricSender($sender);
```

## Usage
```php
<?php
$tags = ['app' => 'fun application'];
$monitor->increment('tag.tag', $tags);
```
