[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/phpunit_profiler/v/stable)](https://packagist.org/packages/serendipity_hq/phpunit_profiler)
[![Build Status](https://travis-ci.org/SerendipityHQ/SHQ_PHPUnit_Profiler.svg?branch=master)](https://travis-ci.org/SerendipityHQ/SHQ_PHPUnit_Profiler)
[![Total Downloads](https://poser.pugx.org/serendipity_hq/phpunit_profiler/downloads)](https://packagist.org/packages/serendipity_hq/phpunit_profiler)
[![License](https://poser.pugx.org/serendipity_hq/phpunit_profiler/license)](https://packagist.org/packages/serendipity_hq/phpunit_profiler)
[![Code Climate](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler/badges/gpa.svg)](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler)
[![Test Coverage](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler/badges/coverage.svg)](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler/coverage)
[![Issue Count](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler/badges/issue_count.svg)](https://codeclimate.com/github/SerendipityHQ/SHQ_PHPUnit_Profiler)
[![StyleCI](https://styleci.io/repos/49488856/shield)](https://styleci.io/repos/49488856)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0ad683e4-b29b-4d8b-b968-cfb61e6117e3/mini.png)](https://insight.sensiolabs.com/projects/0ad683e4-b29b-4d8b-b968-cfb61e6117e3)
[![Dependency Status](https://www.versioneye.com/user/projects/56ae2a7d7e03c7003db69697/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56ae2a7d7e03c7003db69697)

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
