<?php

namespace MadeITBelgium\Chrome;

use Exception;
use MadeITBelgium\Chrome\Chrome\SupportsChrome;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

class ChromeBrowser
{
    use Concerns\ProvidesBrowser,
        SupportsChrome;

    private $url;
    
    /**
     * Register the base URL with Dusk.
     *
     * @return void
     */
    protected function setUp($url)
    {
        $this->url = $url;
        
        Browser::$baseUrl = $this->url;

        Browser::$storeScreenshotsAt = base_path('storate/app/chrome/screenshots');

        Browser::$storeConsoleLogAt = base_path('storage/app/chrome/console');
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless'
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
