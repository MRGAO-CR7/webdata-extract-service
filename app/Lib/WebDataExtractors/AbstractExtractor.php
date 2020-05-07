<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class AbstractExtractor
{
    private $host = 'http://localhost:4444/wd/hub'; // this is the default
    public $driver;
    private $enableWaits = true;

    public function __construct()
    {
        $phantomJSHost = Configure::read('PhantomJS.host');
        $phantomJSPort = Configure::read('PhantomJS.port');
        $this->host = "http://$phantomJSHost:$phantomJSPort/wd/hub";

        // start PhantomJS with 5 second timeout
        $capabilities = DesiredCapabilities::phantomjs();

        $this->driver = RemoteWebDriver::create($this->host, $capabilities, 5000);
    }

    /**
     * This function is to queue all $extractions.
     */
    abstract protected function queue($options);

    /**
     * This function is to extract all data.
     */
    abstract protected function extract($options);

    /**
     * The entry point function for running the automation process.
     *
     * @param  array  $options: contains options required for running the extraction process.
     * 
     * @return integer Returns the extractor id.
     */
    public function run($step, $options)
    {
        if ($step == 'queue') {
            $result = $this->queue($options);
        } else {
            $result = $this->extract($options);
        }
        
        $this->driver->quit();
        return $result;
    }

    /**
     * A simple wrapper around the web driver wait functionality.
     * @param  [int]    $time: Time to wait while waiting for an element to load in the DOM.
     * @param  [mixed]  $condition: A callback that returns a boolean if it finds the element we're waiting for.
     * @return [void]
     */
    protected function waitUntil($time, $condition)
    {
        if (!$this->enableWaits) {
            return;
        }

        $this->driver->wait($time)->until($condition);
    }
}
