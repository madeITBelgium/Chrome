<?php

namespace MadeITBelgium\Chrome\Concerns;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use MadeITBelgium\Chrome\Browser;
use ReflectionFunction;
use Throwable;

trait ProvidesBrowser
{
    /**
     * All of the active browser instances.
     *
     * @var array
     */
    protected static $browsers = [];

    /**
     * The callbacks that should be run on class tear down.
     *
     * @var array
     */
    protected static $afterClassCallbacks = [];

    /**
     * Register an "after class" tear down callback.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function afterClass(Closure $callback)
    {
        static::$afterClassCallbacks[] = $callback;
    }

    /**
     * Create a new browser instance.
     *
     * @param \Closure $callback
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return \MadeITBelgium\Chrome\Browser|void
     */
    public function browse(Closure $callback)
    {
        $browsers = $this->createBrowsersFor($callback);

        try {
            $callback(...$browsers->all());
        } catch (Exception $e) {
            $this->captureFailuresFor($browsers);

            throw $e;
        } catch (Throwable $e) {
            $this->captureFailuresFor($browsers);

            throw $e;
        } finally {
            $this->storeConsoleLogsFor($browsers);

            static::$browsers = $this->closeAllButPrimary($browsers);
        }
    }

    /**
     * Create the browser instances needed for the given callback.
     *
     * @param \Closure $callback
     *
     * @return array
     */
    protected function createBrowsersFor(Closure $callback)
    {
        if (count(static::$browsers) === 0) {
            static::$browsers = collect([$this->newBrowser($this->createWebDriver())]);
        }

        $additional = $this->browsersNeededFor($callback) - 1;

        for ($i = 0; $i < $additional; $i++) {
            static::$browsers->push($this->newBrowser($this->createWebDriver()));
        }

        return static::$browsers;
    }

    /**
     * Create a new Browser instance.
     *
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $driver
     *
     * @return \MadeITBelgium\Chrome\Browser
     */
    protected function newBrowser($driver)
    {
        return new Browser($driver);
    }

    /**
     * Get the number of browsers needed for a given callback.
     *
     * @param \Closure $callback
     *
     * @return int
     */
    protected function browsersNeededFor(Closure $callback)
    {
        return (new ReflectionFunction($callback))->getNumberOfParameters();
    }

    /**
     * Capture failure screenshots for each browser.
     *
     * @param \Illuminate\Support\Collection $browsers
     *
     * @return void
     */
    protected function captureFailuresFor($browsers)
    {
        $browsers->each(function ($browser, $key) {
            $name = str_replace('\\', '_', get_class($this)).'_'.$this->getFileName();

            $browser->screenshot('failure-'.$name.'-'.$key);
        });
    }

    /**
     * Store the console output for the given browsers.
     *
     * @param \Illuminate\Support\Collection $browsers
     *
     * @return void
     */
    protected function storeConsoleLogsFor($browsers)
    {
        $browsers->each(function ($browser, $key) {
            $name = str_replace('\\', '_', get_class($this)).'_'.$this->getFileName();

            $browser->storeConsoleLog($name.'-'.$key);
        });
    }

    /**
     * Close all of the browsers except the primary (first) one.
     *
     * @param \Illuminate\Support\Collection $browsers
     *
     * @return \Illuminate\Support\Collection
     */
    protected function closeAllButPrimary($browsers)
    {
        $browsers->slice(1)->each->quit();

        return $browsers->take(1);
    }

    /**
     * Close all of the active browsers.
     *
     * @return void
     */
    public static function closeAll()
    {
        Collection::make(static::$browsers)->each->quit();

        static::$browsers = collect();
    }

    /**
     * Create the remote web driver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function createWebDriver()
    {
        return retry(5, function () {
            return $this->driver();
        }, 50);
    }

    private function getFileName()
    {
        return date('Y_m_d-His').rand();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    abstract protected function driver();
}
