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
                '--log-path='.storage_path('logs/chromedriver-errors.log'),
            ]);

            return RemoteWebDriver::create(
                'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                    ChromeOptions::CAPABILITY, $options
                )
            );
        } else {
            $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_3 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) CriOS/67.0.3396.87 Mobile/15E216 Safari/604.1';
            $capabilities = DesiredCapabilities::chrome();
            $options = (new ChromeOptions())->addArguments([
                '--disable-gpu',
                '--headless',
                '--log-path='.storage_path('logs/chromedriver-errors.log'),
            ]);
            $options->setExperimentalOption('mobileEmulation', ['userAgent' => $ua]);

            return RemoteWebDriver::create(
                'http://localhost:9515', $options->toCapabilities()
            );
        }
    }
}
