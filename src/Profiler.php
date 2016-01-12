<?php

namespace SerendipityHQ\Library\PHPUnit_Profiler;

use Symfony\Component\Stopwatch\Stopwatch;

class Profiler extends \PHPUnit_Framework_BaseTestListener
{
    private $stopwatch;

    public function __construct()
    {
        $this->stopwatch = new Stopwatch();
    }

    protected function getStopwatch()
    {
        return $this->stopwatch;
    }
}
