<?php

namespace Cmp\Monitoring\Metric\Sender;

use Cmp\Monitoring\Metric\SenderInterface;
use Cmp\Monitoring\Metric\AbstractMetric;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class PsrLogger implements SenderInterface
{
    /**
     * PSR-3 logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Logger level to use
     *
     * @var string
     */
    protected $level;

    /**
     * @param LoggerInterface $logger Logger to user
     * @param string          $level  Logging level
     */
    public function __construct(LoggerInterface $logger, $level = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->level  = $level;
    }

    /**
     * Logs the metric
     *
     * @param AbstractMetric $metric
     */
    public function send(AbstractMetric $metric)
    {
        $data = $this->getData($metric);
        $tags = array_merge($data, $metric->getTags());
        $this->logger->log($this->level, $this->getMessage($data), $tags);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getMessage(array $data)
    {
        return "Metric: ".implode(' | ', $data);
    }

    /**
     * @param AbstractMetric $metric
     *
     * @return array
     */
    private function getData(AbstractMetric $metric)
    {
        return array(
            'type'        => $metric->getType(),
            'name'        => $metric->getName(),
            'value'       => $metric->getValue(),
            'sample_rate' => $metric->getSampleRate()
        );
    }
}
