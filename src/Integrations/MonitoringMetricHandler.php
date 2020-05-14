<?php

namespace Cmp\Monitoring\Integrations;

use Cmp\Monitoring\Monitor;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MonitoringMetricHandler extends AbstractProcessingHandler
{
    /**
     * @var Monitor
     */
    private $monitor;

    /**
     * @var bool
     */
    private $metric;

    /**
     * MonitoringMetricHandler constructor.
     *
     * @param Monitor  $monitor
     * @param bool     $metric
     * @param bool|int $level
     * @param bool     $bubble
     */
    public function __construct(Monitor $monitor, $metric, $level = Logger::DEBUG, $bubble = true)
    {
        $this->monitor = $monitor;
        $this->metric  = $metric;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     *
     * @return void
     */
    protected function write(array $record): void
    {
        $this->monitor->increment($this->metric, [
            'channel' => $record['channel'],
            'level'   => $record['level_name'],
        ]);
    }
}
