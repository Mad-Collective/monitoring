<?php

namespace Cmp\Monitoring\Metric\Sender;

use Cmp\Monitoring\Integrations\DataDogClient;
use Cmp\Monitoring\Metric\SenderInterface;
use Cmp\Monitoring\Metric\AbstractMetric;
use Cmp\Monitoring\Metric\Type\Counter;
use Cmp\Monitoring\Metric\Type\Gauge;
use Cmp\Monitoring\Metric\Type\Histogram;
use Cmp\Monitoring\Metric\Type\Set;
use Cmp\Monitoring\Metric\Type\Timer;

class DataDog extends DataDogClient implements SenderInterface
{
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
            case 'boolean':
                return ($value) ? 'true' : 'false';

            // Numeric values
            case 'integer':
            case 'double':
                return strval($value);

            case 'NULL':
                return 'null';

            // A string cannot be empty
            case 'string':
                if (!empty($value)) {
                    return strval($value);
                }
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
}
