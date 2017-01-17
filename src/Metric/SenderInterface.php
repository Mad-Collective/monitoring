<?php

namespace Cmp\Monitoring\Metric;

/**
 * Represents an object that can send metric to a back end
 * 
 * Interface SenderInterface
 * @package Cmp\Monitoring\Metric
 */
interface SenderInterface
{
    /**
     * Sends a metric to a back end
     * 
     * @param AbstractMetric $metric
     */
    public function send(AbstractMetric $metric);
}