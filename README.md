# Pluggit Monitoring

[![Build Status](https://travis-ci.org/CMProductions/monitoring.svg?branch=master)](https://travis-ci.org/CMProductions/monitoring)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CMProductions/monitoring/badges/quality-score.png?b=master&s=6001a11048667bd084b7b535321c5fcf4f026e6d)](https://scrutinizer-ci.com/g/CMProductions/monitoring/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/CMProductions/monitoring/badges/coverage.png?b=master&s=c9d2a96bb748b69f39dc3491fdeb5f5b38ea062a)](https://scrutinizer-ci.com/g/CMProductions/monitoring/?branch=master)

Monitoring is an application monitor and event service, which gives you the ability to monitor and health check from the code.

## Installation

Require the library as usual:

``` bash
composer require "pluggit/monitoring"
```

## Types of monitoring data

### Metrics
Metric are statistic values taken at a given point, so it can be graphed and aggregated to be studied.

#### Supported Metrics
- **Counter**: Tracks how many times something happened like the number of database requests or page views.
- **Gauge**: Measure the value of a particular thing at a particular time, like the amount of fuel in a car’s gas tank or the number of users connected to a system.
- **Histogram**: Track the statistical distribution of a set of values, like the duration of a number of database queries or the size of files uploaded by users.
- **Sets**: Are used to count the number of unique elements in a group. If you want to track the number of unique visitor to your site, sets are a great way to do that.
- **Timers**: Timers are essentially a special case of histograms, but specifically sending time measures.

### Events
Events are useful for give context information over the metrics, for example tag a new release, a server migration or a library update.

## How to use the Monitoring libraries
Two new entities have been defined to model real world usage to code.
### The metric entity
The metric entity contains all the information a metric must have.

- **Name**: The metric name.
- **Type**: The metric type.
- **Value**: The metric value. Different type depending on the metric type
- **Tags**: Associated tags with the metric, it has to be a keyed array. Use them to segregate the data. 
- **Sample rate**: An integer greater than 0 and less or equal than 1. Use it to sample the metric, .I. ex: to send only a 10% of the metrics set a sample rate of 0.1.

### The event entity
The event entity describes an occurred event at one specific moment.
 
- **Title**: Short event title.
- **Text**: Long description of the event if necessary.
- **Host**: The host where the event occurred.
- **Timestamp**: The exact time when the event occurred.
- **Tags**: Associated tags with the metric, it has to be a keyed array.
- **Type**: Indicates the type of event: one of info, success, warning or error.

### Creating the entities
There are two factories created to facilitate the creation of the entities, both allow the creation of the entities and provide default data and tags for them in one step

- **MetricFactory**: Allows the creation of metrics with default tags
- **EventFactory**: Allow the creation of Event entities with default host and tags

*NOTE:* Both factories allow to add more default tags after they are instantiated using the method:
```php
public function addDefaultTags(array $tags);
```

### Send the metrics/events to back end storage
Once you have one of this entities you can send them to different back ends (i.ex: log files, email, DataDog, statsd server, etc.). The Senders are the classes that know of to send a Metric or an Event to a back end storage.

They have to comply with the _Metric/SenderInterface_ interface to send metrics:

```php
public function send(AbstractMetric $metric);
```

Or the _Event/SenderInterface_ interface to send events

```php
public function send(Event $event);
```

### The monitor
To ease the creation and the sending of the metrics and events a new class Monitor has been created, for most common use cases this is the only class you will have to interact with from the code.

#### Configuration of the monitor
the monitor requires both the metric and the event factory as dependencies, it also requires a Monolog logger that will register the posible exceptions that are found. The monitor will prevent all exceptions from being raised, so the normal execution flow of the code using the monitor won't be broken by any exception in the monitoring libraries
After the monitor is created, it has to be populated with the metric and events senders using this methods:
```php
public function pushMetricSender(MetricSender $sender);
public function pushEventSender(EventSender $sender);
```

**NOTE**: If no senders are pushed the monitor, it will work normally but will not send the metric to any backend, so it is a nice way to disable the monitor temporary.

#### Accesing the factories
You can access the factories inside the monitor (for example to add more default tags) with those two accessors:
```php
public function getMetricFactory();
public function getEventFactory();
```

#### One-step methods
The monitor can create metrics and events and send them to all backend with one step methods:

- **counter**: This method will create and send a counter metric with the given count.
```php
public function counter($metric, $count = 1, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->counter('wh.queries_executed', 38);
```

- **increment**: This method will increment a counter metric in +1 and send it.
```php
public function increment($metric, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->increment('wh.page_views', ['page' => 'site/home']);
```

- **decrement**: This method will decrement a counter metric in -1 and send it.
```php
public function decrement($metric, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->decrement('wh.remaining_slots');
```

- **gauge**: This method will create and send a gauge metric with the given level.
```php
public function gauge($metric, $level, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->gauge('wh.redis.memory_used', 736827);
```

- **set**: It will create a set counter with the given unique value and  send it.
```php
public function set($metric, $uniqueValue, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->set('wh.user', 314646); 
```

- **histogram**: This method will create and send a histogram metric with the given duration in milliseconds.
```php
public function histogram($metric, $duration, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->histogram('wh.user_creation', 325, ['controller' => 'api.user']); 
```

- **start**: This method will create a timer and begin to count the milliseconds, this method will not send the metric yet.
```php
public function start($metric, array $tags = [], $sampleRate = AbstractMetric::DEFAULT_SAMPLE_RATE)

$monitor->start('wh.user_creation', ['controller' => 'site.registration']);
```

- **end**: This method has to be called after a timer has been started, and it will calculate the time spend between the starting time and the end time and then send the metric. You can also pass new tags that will be merged to those specified when starting the timer.
```php
public function end($metric, array $tags = [])

$monitor->end('wh.user_creation', ['result' => 'ok']);
```

- **event**: This method will create and send an event, if the timestamp is not given the current time will be used
```php
public function event($title, $text, $type = Event::INFO, array $tags = [], $timestamp = null)

$monitor->event('release.1.55.1', 'features: WQR-615, WQR-634, WQR-645', Event::Success, ['deployer' => 'Sam'], strtotime('-10 minutes'));
```

#### Sending custom metrics or events with the monitor
The monitor class can also send custom metrics and events with the methods:
```php
public function sendMetric(AbstractMetric $metric);
public function sendEvent(Event $event);
```

### Typical setup
For using the monitor the typical steps are:
* Create a monitor with the provided Metric and Event factories
* Create and push the metric senders and events sender to the monitor
* Start sending metrics and events with the monitor

This code is a simplified demonstration of the setup process without the object dependencies.
```php
// Build the metric factory with your choosen default tags
$metricFactory = new MetricFactory(['environment' => 'dev', 'mode' => 'production']);

// Build the event factory with the host name and your choosen default tags
$eventFactory = new EventFactory('my_docker_hostname', ['environment' => 'dev', 'mode' => 'production', 'domain' => 'my_domain']);

// Build a Monolog Logger and push handlers
$logger = new Monolog\Logger('monitoring');
$logger->pushHandler(/* build handler for logging messages */);

// Build the monitor
$monitor = new Monitor($metricFactory, $eventFactory, $logger);

// Populate the monitor with the desired metric and event senders
$monitor
 ->pushMetricSender(new MonologMetricSender(/* object dependencies */))
 ->pushMetricSender(new DataDogMetricSender(/* object dependencies */))
 ->pushEventSender(new MonologMetricSender(/* object dependencies */))
 ->pushEventSender(new MonologMetricSender(/* object dependencies */));

// Use the monitor to send metrics and events
$monitor->increment('wh.page_views');
```

## Available back end senders

### Psr-3 logger
With the psr-3 compatible sender you can send events and metrics to _PS-3 Logger Handlers_. The typical case scenario will be log to files but you can setup very powerful handlers by plug in, for example, Monolog (let you log to databases, send mails or send the metrics and events to online platforms like Splunk. Of course, you can also build custom ones.)

```


Example of setup
```php
// Build the logger 
$logger = new \Monolog\Logger('monitoring');
$logger->pushHandler(new \Monolog\Handler\StreamHandler('/tmp/monitoring.log'));
// Push as many monolog handlers as you want

// Build the serializer
$serializer = \JMS\Serializer\SerializerBuilder::create()->build();

// Create the metric and/or event sender
$eventSender = new \Cmp\Infrastructure\Application\Monitoring\Monolog\Event\Sender($logger, $serializer);
$metricSender = new \Cmp\Infrastructure\Application\Monitoring\Monolog\Metric\Sender($logger, $serializer);

// Push them to the monitor
$monitor->pushMetricSender($metricSender);
$monitor->pushEventSender($eventSender);
```

The senders accept a second parameters _level_ which is the level that the Logger is going to use to create the messages, so you could have different senders with different loggers depending on the level

```php
$logger = new \Monolog\Logger('monitoring');
$logger->pushHandler(new \Monolog\AbstractHandler\StreamHandler('/tmp/monitoring.log'));
$logger->pushHandler(new \Monolog\AbstractHandler\StreamHandler('/tmp/monitoring_errors.log'), Logger::ERROR);

// This event sender will use error as level for generating the logger message, so only the second monolog handler will treat the events
$eventSender = new \Cmp\Infrastructure\Application\Monitoring\Monolog\Event\Sender($logger, serializer, Logger::ERROR);

// This event sender will log all events because both monolog handlers accept debug level and above
$eventSender = new \Cmp\Infrastructure\Application\Monitoring\Monolog\Event\Sender($logger, serializer, Logger::DEBUG);
```


### DataDog
With DataDog senders you will be able to send metrics to DataDog using the integrated stastd server on the datadog agent and send events using the HTTP API.
> **Do you want to know more about DataDog?**
>
> Check out the configuration quick guide [here][1]

#### Metric sender
There are 2 implementation of the metric sender available
- _**Sender**_: The default implementation, will send an UDP message to the datadog statsd server for every metric it receives
- _**BufferedSender**_: This sender will buffer the metrics until the specified size is reached and flush multiple metrics in one UDP message. 
 
_**Note** on the buffered sender_: Be carefull when configuring the maximum size in bytes for the UDP message, it is dependant on the machine and the network protocol, the safest value is the default one, if the message is too big the package will fail to be send and the metrics will be lost

Example configuring the default sender
```php
// Datadog configuration
$datadogAgent = ['ip' => '127.0.0.1', 'port' => 8125];

// Build the sender
$socket = \Cmp\Infrastructure\Application\Monitoring\DataDog\Metric\Socket();
$metricSender = new \Cmp\Infrastructure\Application\Monitoring\DataDog\Metric\Sender($socket, datadogAgent['ip'], datadogAgent['port']);

// Push to the monitor
$monitor->pushMetricSender($metricSender);
```

#### Event sender
You will need to get the api key and generate an application key from the datadog web panel.

When configuring the sender you will need to create a new instance of the API Events included on the libraries, which will require a transport for performing the HTTP requests.
Right now there are two available implementation on the libraries but you can build your own ones:
- **CURL**: This is a native CURL implementation of the API transport
- **Guzzle**: It will require to install guzzle libraries as recommended on the ```composer.json``` file 

Example of setup for the metric sender with native CURL transport
```php
// Build the API
$apiKey = 'YOUR API KEY';
$appKey = 'YOUR APPLICATION KEY';
$apiTransport = \Cmp\Infrastructure\Application\Monitoring\DataDog\Api\Transport\Curl(); 
$api = \Cmp\Infrastructure\Application\Monitoring\DataDog\Api\Api($apiTransport, $apiKey, $appKey);

// Create the events api SDK
$eventsApi = new \Cmp\Infrastructure\Application\Monitoring\DataDog\Api\Method\Events($api); 

// Build the sender
$eventSender = new \Cmp\Infrastructure\Application\Monitoring\DataDog\Event\Sender($eventsApi);

// Push it to the monitor
$monitor->pushEventSender($eventSender);
```

**IMPORTANT NOTE**: This sender will perform synchronous HTTP requests, so it's not recommended using it on user applications. (see _Message_ back end sender  to see how can be used asynchronous)

### Message
These senders are not usual senders because they don't directly perform operation to metrics or events. 

This back ends senders make use of the Message system libraries included on the CMP/base libraries to be able to send metrics and events to a messaging system, so they can be processed by other senders in an asynchronous way

Imagine we want to send an event to DataDog using the datadog event sender every time a user upgrades his account, if we do it directly with the native datadog sender we are going to block the user with a synchronous call to DataDog's HTTP API, so we can use this back end sender to make the request asynchronous.

Example of the Message event sender using RabbitMQ as messaging system
```php
//Setup the Rabbit producer
$rabbitProducer = \Cmp\Infrastructure\Application\Notification\RabbitMQ\Message\Producer($rabbitConfig);

// Build the sender with the producer and providing a destination exchange
$messageEventSender = new \Cmp\Infrastructure\Application\Monitoring\Message\Event\Sender($rabbitProducer, 'monitoring.events');

// Push it to the monitor
$monitor->pushEventSender($eventSender);
```

Now there should be another daemon task that will read the event messages and send them to other senders
```php
// Build the rabbit consumer
$rabbitConsumer = new Cmp\Infrastructure\Application\Notification\RabbitMQ\Message\Consumer($rabbitConfig);

// The consumer requires a monolog logger to log errors
$logger = new \Monolog\Logger('monitoring');
$logger->pushHandler(new \Monolog\AbstractHandler\StreamHandler('/tmp/events_errors.log'));

// Build the consumer with the correct origin to read the messages
$messageEventConsumer = new \Cmp\Infrastructure\Application\Monitoring\Message\Event\Consumer($rabbitConsumer, $logger, 'monitoring.events';

// Push as many event senders as you want
$messageEventConsumer->pushSender($datadogEventSender);
$messageEventConsumer->pushSender($monologEventSender);

// Start listening to messages in an infinite loop
$messageEventConsumer->start();
```

**NOTE ON ASYNCHRONOUS METRICS**: The metrics don't have a timestamp, so if there is a delay while consuming the messages the metrics will be delayed. The worst case could be if the consumer crashes and then is restarted later, it start consuming queued message and generate a lot of metrics with wrong timestamps

[1]: doc/datadog.md