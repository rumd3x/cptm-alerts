<?php

namespace CptmAlerts\Modules;

class TimeTracker
{

    /**
     * @var float
     */
    private $start;

    public function __construct()
    {
        $this->start = microtime(true);
    }

    /**
     * Get Elapsed time since object creation in float
     *
     * @return float
     */
    public function getElapsed()
    {
        return microtime(true) - $this->start;
    }
}
