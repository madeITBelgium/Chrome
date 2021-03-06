<?php

namespace MadeITBelgium\Chrome;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use MadeITBelgium\Chrome\Chrome\SupportsChrome;

class ChromeBrowser
{
    use Concerns\ProvidesBrowser;
    use SupportsChrome;

    public $url;

    private $mobile = false;

    private $extraCapabilities = null;

    /**
     * Register the base URL.
     *
     * @return void
     */
    public function setUp($url, $mobile = false, $extraCapabilities = [])
    {
        $this->url = $url;

        $this->mobile = $mobile;

        $this->extraCapabilities = $extraCapabilities;

        $this->extraCapabilities = $extraCapabilities;

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
        $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36';
        if ($this->mobile) {
            $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/84.0.4147.122 Mobile/15E148 Safari/604.1';
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
            ChromeOptions::CAPABILITY,
            $options
        );

        if ($this->extraCapabilities !== null) {
            foreach ($this->extraCapabilities as $name => $value) {
                $capabilities->setCapability($name, $value);
            }
        }

        return RemoteWebDriver::create(
            'http://localhost:9515',
            $capabilities
        );
    }
}
