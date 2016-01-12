<?php

namespace SerendipityHQ\Library\PHPUnit_Profiler;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * A PHPUnit listener to profile tests execution time and memory consumed.
 *
 * @package SerendipityHQ\Library\PHPUnit_Profiler
 */
class Profiler extends \PHPUnit_Framework_BaseTestListener
{
    /*
     * Public options
     */
    /** @var bool Toggle on or off the time profiling */
    private $profileTime = false;
    /** @var bool Toggle on or off the time profiling with PHPUnit builtin $time */
    private $profileTimeWithPhpunit = true;
    /** @var bool Toggle on or off the time profiling with Stopwatch */
    private $profileTimeWithStopwatch = false;
    /** @var bool Toggle on or off the memory usage profiling */
    private $profileMemoryUsage = false;
    /** @var bool Toggle on or off the memory usage profiling with PHP */
    private $profileMemoryCurrentUsage = true;
    /** @var bool Toggle on or off the memory usage with Stopwatch */
    private $profileMemoryDetailedUsage = false;

    /*
     *  Private options
     */
    /** @var bool Defines if Stopwatch has to be started */
    private $profileWithStopwatch = false;

    /** @var  Stopwatch */
    private $stopwatch;

    /**
     * Profiler constructor.
     *
     * @param array $options The array containing the options of the profiler
     */
    public function __construct($options = [])
    {
        $this->initOptions($options);

        if ($this->profileWithStopwatch)
            $this->initStopwatch();
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        printf("\n\nStart testsuite '%s'", $suite->getName());

        // Output memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage)
                printf(" (Memory Currently used: %s)", $this->getCurrentMemoryUsed());
        }

        printf("\n");

        // Start Stopwatch for the current test
        if ($this->profileWithStopwatch)
            $this->getStopwatch()->start($suite->getName());
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        printf("\nEnded testsuite '%s", $suite->getName());

        // Output memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage)
                printf(" (Memory Currently used: %s)", $this->getCurrentMemoryUsed());
        }

        printf("\n");

        // Start Stopwatch for the current test
        if ($this->profileWithStopwatch)
            $event = $this->getStopwatch()->stop($suite->getName());

        // Output time profiling
        if ($this->profileTime) {
            printf('Time took: ');

            // Output time profiling with PHPUnit
            if ($this->profileTimeWithPhpunit)
                printf(" PHPUnit: N/A for a test suite; ");

            // Output time profiling with Stopwatch
            if ($this->profileTimeWithStopwatch && isset($event))
                printf(" Stopwatch: %s ms;", $event->getDuration());

            printf("\n");

            // Freeup memory
            if (isset($event))
                $event = null;
        }
    }

    /**
     * Called when a test in a test class is started
     *
     * @param \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        printf("\n\nStart test '%s'", $test->getName());

        // Start Stopwatch for the current test
        if ($this->profileWithStopwatch)
            $this->getStopwatch()->start($test->getName());

        // Output memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage)
                printf(" (Memory Currently used: %s)", $this->getCurrentMemoryUsed());
        }

        printf("\n");
    }

    /**
     * Called when a test in a test class ends
     *
     * @param \PHPUnit_Framework_Test $test
     * @param float $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        printf("Ended test '%s'", $test->getName());

        // Output memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage)
                printf(" (Memory Currently used: %s)", $this->getCurrentMemoryUsed());
        }

        printf("\n");

        // Stop Stopwatch for the current test
        if ($this->profileWithStopwatch)
            $event = $this->getStopwatch()->stop($test->getName());

        // Output time profiling
        if ($this->profileTime) {
            printf('Time took: ');

            // Output time profiling with PHPUnit
            if ($this->profileTimeWithPhpunit)
                printf(" PHPUnit: %s ms; ", round($time * 1000, 2));

            // Output time profiling with Stopwatch
            if ($this->profileTimeWithStopwatch && isset($event))
                printf(" Stopwatch: %s ms;", $event->getDuration());

            printf("\n");

            // Freeup memory
            if (isset($event))
                $event = null;
        }
    }

    /**
     * Get the current memory usage in a formatted string.
     *
     * Format method taken from http://php.net/manual/en/function.memory-get-usage.php#96280
     * @return string
     */
    protected function getCurrentMemoryUsed()
    {
        $size = memory_get_usage();

        $unit = ['b','kb','mb','gb','tb','pb'];

        return @round(
            $size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * Get the Stopwatch instance
     *
     * @return Stopwatch
     */
    protected function getStopwatch()
    {
        return $this->stopwatch;
    }

    /**
     * Stes the options of the profiler
     *
     * @param array $options
     */
    protected function initOptions(array $options)
    {
        // Options for time profiling
        if (isset($options['profileTime']) && is_bool($options['profileTime']))
            $this->profileTime = $options['profileTime'];

        if (isset($options['profileTimeWithPhpunit']) && is_bool($options['profileTimeWithPhpunit']))
            $this->profileTimeWithPhpunit = $options['profileTimeWithPhpunit'];

        if (isset($options['profileTimeWithStopwatch']) && is_bool($options['profileTimeWithStopwatch'])) {
            $this->profileTimeWithStopwatch = $options['profileTimeWithStopwatch'];
            $this->profileWithStopwatch = true;
        }

        // Option for memoery usage profiling
        if (isset($options['profileMemoryUsage']) && is_bool($options['profileMemoryUsage']))
            $this->profileMemoryUsage = $options['profileMemoryUsage'];

        if (isset($options['profileMemoryCurrentUsage']) && is_bool($options['profileMemoryCurrentUsage']))
            $this->profileMemoryCurrentUsage = $options['profileMemoryCurrentUsage'];

        if (isset($options['profileMemoryDetailedUsage']) && is_bool($options['profileMemoryDetailedUsage'])) {
            $this->profileMemoryDetailedUsage = $options['profileMemoryDetailedUsage'];
            $this->profileMemoryCurrentUsage = true;
            $this->profileWithStopwatch = true;
        }
    }

    /**
     * Initialize the Stopwatch instance and start the global event
     *
     * @return \Symfony\Component\Stopwatch\StopwatchEvent
     */
    protected function initStopwatch()
    {
        $this->stopwatch = new Stopwatch();
    }
}
