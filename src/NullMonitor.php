<?php
namespace Cmp\Monitoring;

use Cmp\Monitoring\Event\EventFactory;
use Cmp\Monitoring\Metric\AbstractMetric;
use Cmp\Monitoring\Metric\MetricFactory;
use Psr\Log\NullLogger;

class NullMonitor extends Monitor
{
    /**
     * NullMonitor constructor.
     */
    public function __construct()
    {
        parent::__construct(new MetricFactory(), new EventFactory('null'), new NullLogger());
    }

    /**
     * @param AbstractMetric $metric
     */
    public function sendMetric(AbstractMetric $metric)
    {
        //nothing_to_do
    }
}
