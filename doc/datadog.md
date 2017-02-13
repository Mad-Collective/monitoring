
# DataDog

DataDog (DD) is a cloud monitoring service

## How it works
You can report metrics and events to datadog using an api key associated to the account, then you can create graphs and views with this data

### Metrics
Metrics are reported passing UDP messages to the DataDogsD server (based on [StatsD](https://github.com/etsy/statsd)). 
This server is part of the DataDog agent, which is a daemon that run on a server, receives the metrics, aggregates them and sends them to DataDog servers every X seconds (10 by default)

#### How to install the Agent
The easier way to install the Datadog agent is:
- Log into DataDog [web admin panel](https://app.datadoghq.com/account/login)
- Go to the [agent's configuration page](https://app.datadoghq.com/account/settings#agent)
- Follow the instructions for your server's OS

To check that the agent is currently up and running with the proper API key for your account, you can check that the server's host name is on the list at the [infrastucture web page](https://app.datadoghq.com/infrastructure

#### Configuration of the agent
If you need to change the port or the api key of the agent you can edit the file at:

```
vi /etc/dd-agent/datadog.conf

api_key: 90170ee4b01fedefe9ccf********** 
dogstatsd_port : 8125
```

#### Test the agent
You can send metrics by command line to the agent with this command to see if it works
```
echo "foo:1|c" | nc -u -w0 127.0.0.1 8125
```
In the [metrics explorer](https://app.datadoghq.com/metric/explorer) you should see a new counter metric with name foo.

### Events
Events are send with DataDogs' HTTP API. You will need the api key associated with your account and create an application key. You will be able to get this information from [the api configuration page](https://app.datadoghq.com/account/settings#api)
