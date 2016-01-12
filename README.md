# SHQ_PHPUnit_Profiler

A PHPUnit listener to profile the execution of test suites and tests inside them.

This listener can show the time needed by each test and each test suite to complete and the memory used by each one of them.

## Installation

Use Composer to install this listener:

    $ composer require serendipity_hq/phpunit_profiler
     
To [configure the listener](https://phpunit.de/manual/current/en/appendixes.configuration.html#appendixes.configuration.test-listeners) you have to pass an array of options:

    <listeners>
        <listener class="SerendipityHQ\Library\PHPUnit_Profiler\Profiler">
            <arguments>
                <array>
                    <element key="time"><boolean>true</boolean></element>
                    <element key="profileTimeWithStopwatch"><boolean>true</boolean></element>
                    <element key="profileMemoryUsage"><boolean>true</boolean></element>
                    <element key="profileMemoryDetailedUsage"><boolean>true</boolean></element>
                </array>
            </arguments>
        </listener>
    </listeners>

The listener will output the profiling information.

NOTE: As this is a [listener](https://phpunit.de/manual/current/en/extending-phpunit.html#extending-phpunit.PHPUnit_Framework_TestListener) and not a `ResultsPrinter`, it doesn't take care of the use of `--verbose` or `--debug` options.