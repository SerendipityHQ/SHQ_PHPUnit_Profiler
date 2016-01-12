<?php

namespace SerendipityHQ\Library\PHPUnit_Profiler;

use Symfony\Component\Stopwatch\Stopwatch;

class Profiler extends \PHPUnit_Framework_BaseTestListener
{
    // Public options
    private $profileTime = false;

    public function __construct($options = [])
    {
        $this->initOptions($options);
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
        // Start the Stopwatch
        printf("\n\nStart test '%s'\n", $test->getName());
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        printf("Ended test '%s'\n", $test->getName());

        if ($this->profileTime) {
            printf('[Time took] ');
            printf("PHPUnit: %s ms ", round($time * 1000, 2));
            printf("\n");
        }
    }

    protected function initOptions(array $options)
    {
        if (isset($options['profileTime']) && is_bool($options['profileTime']))
            $this->profileTime = $options['profileTime'];
        }
    }
}
