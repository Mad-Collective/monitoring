<?php
namespace Cmp\Monitoring\Metric\Sender;

use Cmp\Monitoring\Metric\SenderInterface;
use Cmp\Monitoring\Metric\AbstractMetric;
use Cmp\Monitoring\Metric\Type\Counter;
use Cmp\Monitoring\Metric\Type\Gauge;
use Cmp\Monitoring\Metric\Type\Histogram;
use Cmp\Monitoring\Metric\Type\Set;
use Cmp\Monitoring\Metric\Type\Timer;

class DataDog implements SenderInterface
{
    const DATADOG_DEFAULT_SERVER = '127.0.0.1';
    const DATADOG_DEFAULT_PORT = 8125;

    /**
     * Socket for sending messages
     *
     * @var string
     */
    protected $socket;

    /**
     * DataDog Agent server ip
     *
     * @var string
     */
    protected $server;

    /**
     * DataDog Agent port
     *
     * @var int
     */
    protected $port;

    /**
     * @param Socket $socket Socket for sending messages
     * @param string $server DataDog agent host
     * @param int    $port   Port where DataDog agent is listening to
     */
    public function __construct(Socket $socket, $server = self::DATADOG_DEFAULT_SERVER, $port = self::DATADOG_DEFAULT_PORT)
    {
        $this->socket = $socket;
        $this->server = $server;
        $this->port = $port;
    }

    /**
     * Send a metric to DataDog agent
     *
     * @param AbstractMetric $metric
     */
    public function send(AbstractMetric $metric)
    {
        $message = $this->getMetricMessage($metric);
        $this->flush($message);
    }

    /**
     * Builds the metric udp message
     *
     * @param AbstractMetric $metric
     *
     * @return string
     */
    protected function getMetricMessage(AbstractMetric $metric)
    {
        $message = "{$metric->getName()}:{$this->getMetricValue($metric)}|{$this->getMetricIndicator($metric)}";

        if ($metric->getSampleRate() < 1 && $metric->getSampleRate() > 0) {
            $message .= '|@'.sprintf("%.1f", $metric->getSampleRate());
        }

        return $message . $this->getFormattedTags($metric->getTags());
    }

    /**
     * Gets the value for datadog
     *
     * @param AbstractMetric $metric
     *
     * @return null|string
     */
    protected function getMetricValue(AbstractMetric $metric)
    {
        $value = $metric->getValue();
        $type  = gettype($value);
        switch ($type) {
            case 'boolean': return ($value) ? 'true' : 'false';

            // Numeric values
            case 'integer':
            case 'double':  return strval($value);

            case 'NULL':    return 'null';

            // A string cannot be empty
            case 'string':  if (!empty($value)) {
                return strval($value);
            }

            default:
        }

        throw new \InvalidArgumentException('Metric value is not a valid DataDog metric type: '.$type);
    }

    /**
     * Gets the tags formatted ready to use on the udp message
     *
     * @param array $tags
     *
     * @return null|string
     */
    protected function getFormattedTags(array $tags)
    {
        $add =array();

        foreach ($tags as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            $add[] = "#$key:$value";
        }

        return !empty($add) ? '|'.implode(',', $add) : null;
    }

    /**
     * Returns the metric indicator depending on the metric type
     *
     * @param AbstractMetric $metric
     *
     * @return string
     */
    protected function getMetricIndicator(AbstractMetric $metric)
    {
        switch ($metric->getType()) {
            case Set::TYPE:       return 's';
            case Gauge::TYPE:     return 'g';
            case Timer::TYPE:     return 'ms';
            case Counter::TYPE:   return 'c';
            case Histogram::TYPE: return 'h';

            default:
                throw new \InvalidArgumentException('Metric type is not a valid DataDog metric: '.$metric->getType());
        }
    }

    /**
     * Send the message over UDP
     *
     * @param $message
     */
    protected function flush($message)
    {
        $this->socket->create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $this->socket->setNonBlocking();
        $this->socket->sendMessage($message, $this->server, $this->port);
        $this->socket->close();
    }
}