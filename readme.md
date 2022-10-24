# PHP Laravel Chrome Browser interaction
[![Build Status](https://travis-ci.org/madeITBelgium/Chrome.svg?branch=master)](https://travis-ci.org/madeITBelgium/Chrome)
[![Coverage Status](https://coveralls.io/repos/github/madeITBelgium/Chrome/badge.svg?branch=master)](https://coveralls.io/github/madeITBelgium/Chrome?branch=master)
[![Latest Stable Version](https://poser.pugx.org/madeITBelgium/Chrome/v/stable.svg)](https://packagist.org/packages/madeITBelgium/Chrome)
[![Latest Unstable Version](https://poser.pugx.org/madeITBelgium/Chrome/v/unstable.svg)](https://packagist.org/packages/madeITBelgium/Chrome)
[![Total Downloads](https://poser.pugx.org/madeITBelgium/Chrome/d/total.svg)](https://packagist.org/packages/madeITBelgium/Chrome)
[![License](https://poser.pugx.org/madeITBelgium/Chrome/license.svg)](https://packagist.org/packages/madeITBelgium/Chrome)

With this Laravel package you interact with a Chrome headless webbrowser. This package is based on Laravel Dusk.

# Installation

Require this package in your `composer.json` and update composer.

```php
"madeitbelgium/chrome": "^1.3"
```

# Documentation
## Usage
```php
$chromebrowser = new \MadeITBelgium\Chrome\ChromeBrowser();
$chromebrowser->setUp($url, false); //False = desktop
$chromebrowser->startChromeDriver();

$chromebrowser->browse(function (Browser $browser) {
    $browser->visit('https://www.example.com');
    $browser->screenshot('screenshot');
});


$chromebrowser->closeAll();
$chromebrowser->stopChromeDriver();
```

## Override default settings
To override the default settings you can create your own class that extends the MadeITBelgium\Chrome\ChromeBrowser class. In your own class you need to override the driver function.
```php
<?php

namespace App;

use MadeITBelgium\Chrome\ChromeBrowser as ChromeBrowserParent;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use MadeITBelgium\Chrome\Chrome\SupportsChrome;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class ChromeBrowser extends ChromeBrowserParent
{
    public function driver()
    {
        $driverLocation = 'http://localhost:9515';
        //$driverLocation = 'http://localhost:4444/wd/hub';
        
        $args = [
            '--disable-gpu',
            '--headless',
            '--no-sandbox',
        ];
        
        $options = (new ChromeOptions())->addArguments($args);
         $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_3 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) CriOS/67.0.3396.87 Mobile/15E216 Safari/604.1';
        $options->setExperimentalOption('mobileEmulation', ['userAgent' => $ua]);
        $capabilities = DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities->setCapability('proxy', [
            'proxyType' => 'manual',
            'httpProxy' => 'http://proxyserver:3128',
            'sslProxy' => 'http://proxyserver:3128',
        ]);
        
        return RemoteWebDriver::create($driverLocation, $capabilities);
    }
}
```

```php
$chromebrowser = new \App\ChromeBrowser();
$chromebrowser->setUp($url);
$chromebrowser->startChromeDriver();

$chromebrowser->browse(function (Browser $browser) {
    $browser->visit('https://www.example.com');
});


$chromebrowser->closeAll();
$chromebrowser->stopChromeDriver();
```

Change location
```php
$chromebrowser = new \MadeITBelgium\Chrome\ChromeBrowser();
$chromebrowser->setUp($url, false); //False = desktop
$chromebrowser->startChromeDriver();

$chromebrowser->browse(function (Browser $browser) {
    $devTools = new \Facebook\WebDriver\Chrome\ChromeDevToolsDriver($browser->driver);
    $coordinates = [
        'latitude' => 39.913818,
        'longitude' => 116.363625,
        'accuracy' => 1,
    ];
    $devTools->execute('Emulation.setGeolocationOverride', $coordinates);
    $browser->visit('https://www.example.com');
    $browser->screenshot('screenshot');
});


$chromebrowser->closeAll();
$chromebrowser->stopChromeDriver();
```
The complete documentation can be found at: [http://www.madeit.be/](http://www.madeit.be/)


# Support
Support github or mail: tjebbe.lievens@madeit.be

# Contributing
Please try to follow the psr-2 coding style guide. http://www.php-fig.org/psr/psr-2/

# License
This package is licensed under LGPL. You are free to use it in personal and commercial projects. The code can be forked and modified, but the original copyright author should always be included!
