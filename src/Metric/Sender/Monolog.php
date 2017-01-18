<?php
namespace Cmp\Monitoring\Metric\Sender;

use Cmp\Monitoring\Metric\SenderInterface;
use Monolog\Logger;
use JMS\Serializer\Serializer;
use Cmp\Monitoring\Metric\AbstractMetric;

class Monolog implements SenderInterface
{
    /**
     * Monolog logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * JMS serializer
     *
     * @var Serializer
     */
    protected $serializer;

    /**
     * Logger level to use
     *
     * @var string
     */
    protected $level;

    /**
     * @param Logger     $logger     Logger to user
     * @param Serializer $serializer JMS Serializer
     * @param int        $level      Logging level
     */
    public function __construct(Logger $logger, Serializer $serializer, $level = Logger::INFO)
    {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->level = $level;
    }

    /**
     * Logs the metric
     *
     * @param AbstractMetric $metric
     */
    public function send(AbstractMetric $metric)
    {
        $this->logger->log($this->level, $this->serializer->serialize($metric, 'json'), array('metric'));
    }
}
