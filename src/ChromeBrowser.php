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
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.75 Safari/537.36';
        if ($this->mobile) {
            $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_4_1 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) CriOS/67.0.3396.87 Mobile/15G77 Safari/604.1';
        }

        $capabilities = DesiredCapabilities::chrome();
        $options = (new ChromeOptions())->addArguments([
            '--disable-gpu',
            '--headless',
            '--verbose',
            '--user-agent="'.$ua.'"',
            '--log-path='.storage_path('logs/chromedriver-errors.log'),
        ]);

        if ($this->mobile) {
            $options->setExperimentalOption('mobileEmulation', ['userAgent' => $ua]);
        }

        $capabilities = DesiredCapabilities::chrome()->setCapability(
            ChromeOptions::CAPABILITY, $options
        );

        if ($this->extraCapabilities !== null) {
            foreach ($this->extraCapabilities as $name => $value) {
                $capabilities->setCapability($name, $value);
            }
        }

        return RemoteWebDriver::create(
            'http://localhost:9515', $capabilities
        );
    }
}
