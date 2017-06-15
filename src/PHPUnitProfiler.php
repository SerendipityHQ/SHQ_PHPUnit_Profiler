<?php

/**
 * @author      Adamo Crespi <hello@aerendir.me>
 * @copyright   Copyright (C) 2016.
 * @license     MIT
 */
namespace SerendipityHQ\Library\PHPUnit_Profiler;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * A PHPUnit listener to profile tests execution time and memory consumed.
 */
class PHPUnitProfiler extends BaseTestListener
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
    /** @var  float The amount of memory used by the last test suite */
    private $memoryUsedBeforeTestsuite = 0;
    /** @var  float The amount of memory used by the last test */
    private $memoryUsedBeforeTest = 0;

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

        if ($this->profileWithStopwatch) {
            $this->initStopwatch();
        }
    }

    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        $this->memoryUsedBeforeTestsuite = memory_get_usage();

        printf("\n\nStart testsuite '%s'", $suite->getName());

        // Output current memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage) {
                printf(' (Memory Currently used: %s)', $this->getCurrentMemoryUsed());
            }
        }

        printf("\n");

        // Start Stopwatch for the current test
        if ($this->profileWithStopwatch) {
            $this->getStopwatch()->start($suite->getName());
        }
    }

    /**
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
        printf("\nEnded testsuite '%s\n", $suite->getName());

        // Stop Stopwatch for the current test
        if ($this->profileWithStopwatch) {
            $event = $this->getStopwatch()->stop($suite->getName());
        }

        // Output detailed memory profiling
        if ($this->profileMemoryUsage) {
            printf('Memory: ');

            if ($this->profileMemoryUsage) {
                printf(' Currently used: %s; ', $this->getCurrentMemoryUsed());
            }

            if ($this->profileMemoryDetailedUsage) {
                printf(' Increased by this test suite: %s; ', $this->calculateIncreaseInMemoryUseSinceLastTestsuite());
            }

            printf("\n");
        }

        // Output time profiling
        if ($this->profileTime) {
            printf('Time took: ');

            // Output time profiling with PHPUnit
            if ($this->profileTimeWithPhpunit) {
                printf(' PHPUnit: N/A for a test suite; ');
            }

            // Output time profiling with Stopwatch
            if ($this->profileTimeWithStopwatch && isset($event)) {
                printf(' Stopwatch: %s ms;', $event->getDuration());
            }

            printf("\n");

            // Freeup memory
            if (isset($event)) {
                $event = null;
            }
        }
    }

    /**
     * Called when a test in a test class is started.
     *
     * @param Test $test
     */
    public function startTest(Test $test)
    {
        $this->memoryUsedBeforeTest = memory_get_usage();

        printf("\n\nStart test '%s'", $test->getName());

        // Start Stopwatch for the current test
        if ($this->profileWithStopwatch) {
            $this->getStopwatch()->start($test->getName());
        }

        // Output current memory profiling
        if ($this->profileMemoryUsage) {
            if ($this->profileMemoryCurrentUsage) {
                printf(' (Memory Currently used: %s)', $this->getCurrentMemoryUsed());
            }
        }

        printf("\n");
    }

    /**
     * Called when a test in a test class ends.
     *
     * @param Test $test
     * @param float                   $time
     */
    public function endTest(Test $test, $time)
    {
        printf("Ended test '%s'\n", $test->getName());

        // Stop Stopwatch for the current test
        if ($this->profileWithStopwatch) {
            $event = $this->getStopwatch()->stop($test->getName());
        }

        // Output detailed memory profiling
        if ($this->profileMemoryUsage) {
            printf('Memory: ');

            if ($this->profileMemoryUsage) {
                printf(' Currently used: %s; ', $this->getCurrentMemoryUsed());
            }

            if ($this->profileMemoryDetailedUsage) {
                printf(' Increased by this test: %s; ', $this->calculateIncreaseInMemoryUseSinceLastTest());
            }

            printf("\n");

            // Freeup memory
            if (isset($event)) {
                $event = null;
            }
        }

        // Output time profiling
        if ($this->profileTime) {
            printf('Time took: ');

            // Output time profiling with PHPUnit
            if ($this->profileTimeWithPhpunit) {
                printf(' PHPUnit: %s ms; ', round($time * 1000, 2));
            }

            // Output time profiling with Stopwatch
            if ($this->profileTimeWithStopwatch && isset($event)) {
                printf(' Stopwatch: %s ms;', $event->getDuration());
            }

            printf("\n");

            // Freeup memory
            if (isset($event)) {
                $event = null;
            }
        }

        $this->memoryUsedBeforeTest = memory_get_usage();
    }

    protected function calculateIncreaseInMemoryUseSinceLastTest()
    {
        return $this->formatMemory(memory_get_usage() - $this->memoryUsedBeforeTest);
    }

    protected function calculateIncreaseInMemoryUseSinceLastTestsuite()
    {
        return $this->formatMemory(memory_get_usage() - $this->memoryUsedBeforeTestsuite);
    }

    /**
     * Format an integer in bytes.
     *
     * @see http://php.net/manual/en/function.memory-get-usage.php#96280
     *
     * @param $size
     *
     * @return string
     */
    protected function formatMemory($size)
    {
        $isNegative = false;
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        if (0 > $size) {
            // This is a negative value
            $isNegative = true;
        }

        $return = ($isNegative) ? '-' : '';

        return $return
            .round(
                abs($size) / pow(1024, ($i = floor(log(abs($size), 1024)))), 2
            )
            .' '
            .$unit[$i];
    }

    /**
     * Get the current memory usage in a formatted string.
     *
     * @return string
     */
    protected function getCurrentMemoryUsed()
    {
        return $this->formatMemory(memory_get_usage());
    }

    /**
     * Get the Stopwatch instance.
     *
     * @return Stopwatch
     */
    protected function getStopwatch()
    {
        return $this->stopwatch;
    }

    /**
     * Stes the options of the profiler.
     *
     * @param array $options
     */
    protected function initOptions(array $options)
    {
        // Options for time profiling
        if (isset($options['profileTime']) && is_bool($options['profileTime'])) {
            $this->profileTime = $options['profileTime'];
        }

        if (isset($options['profileTimeWithPhpunit']) && is_bool($options['profileTimeWithPhpunit'])) {
            $this->profileTimeWithPhpunit = $options['profileTimeWithPhpunit'];
        }

        if (isset($options['profileTimeWithStopwatch']) && is_bool($options['profileTimeWithStopwatch'])) {
            $this->profileTimeWithStopwatch = $options['profileTimeWithStopwatch'];
            $this->profileWithStopwatch = true;
        }

        // Option for memoery usage profiling
        if (isset($options['profileMemoryUsage']) && is_bool($options['profileMemoryUsage'])) {
            $this->profileMemoryUsage = $options['profileMemoryUsage'];
        }

        if (isset($options['profileMemoryCurrentUsage']) && is_bool($options['profileMemoryCurrentUsage'])) {
            $this->profileMemoryCurrentUsage = $options['profileMemoryCurrentUsage'];
        }

        if (isset($options['profileMemoryDetailedUsage']) && is_bool($options['profileMemoryDetailedUsage'])) {
            $this->profileMemoryDetailedUsage = $options['profileMemoryDetailedUsage'];
            $this->profileWithStopwatch = true;
        }
    }

    /**
     * Initialize the Stopwatch instance and start the global event.
     *
     * @return \Symfony\Component\Stopwatch\StopwatchEvent
     */
    protected function initStopwatch()
    {
        $this->stopwatch = new Stopwatch();
    }
}
