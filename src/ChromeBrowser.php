<?php

namespace MadeITBelgium\Chrome;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use MadeITBelgium\Chrome\Chrome\SupportsChrome;

class ChromeBrowser
{
    use Concerns\ProvidesBrowser,
        SupportsChrome;

    public $url;

    public $mobile = false;

    /**
     * Register the base URL.
     *
     * @return void
     */
    public function setUp($url, $mobile = false)
    {
        $this->url = $url;

        $this->mobile = $mobile;

        Browser::$baseUrl = $this->url;

        Browser::$storeScreenshotsAt = base_path('storage/app/chrome/screenshots');

        Browser::$storeConsoleLogAt = base_path('storage/app/chrome/console');
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public function driver()
    {
        if (!$this->mobile) {
            $options = (new ChromeOptions())->addArguments([
                '--disable-gpu',
                '--headless',
                '--verbose',
                '--log-path='.storage_path('logs/chromedriver-errors.log'),
            ]);

            return RemoteWebDriver::create(
                'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY, $options
                )
            );
        } else {
            $ua = 'Mozilla/5.0 (iPhone; CPU OS 11_0 like Mac OS X) AppleWebKit/604.1.25 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1';
            $capabilities = DesiredCapabilities::chrome();
            $options = (new ChromeOptions())->addArguments([
                '--disable-gpu',
                '--headless',
                '--verbose',
                '--log-path='.storage_path('logs/chromedriver-errors.log'),
            ]);
            $options->setExperimentalOption('mobileEmulation', ['userAgent' => $ua]);

            return RemoteWebDriver::create(
                'http://localhost:9515', $options->toCapabilities()
            );
        }
    }
}
