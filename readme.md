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
"madeitbelgium/chrome": "~1.0"
```

# Documentation
## Usage
```php
$chromebrowser = new \MadeITBelgium\Chrome\ChromeBrowser();
$chromebrowser->setUp($url);

$chromebrowser->browse(function (Browser $browser) {
    $browser->visit('https://www.example.com');
});
```

The complete documentation can be found at: [http://www.madeit.be/](http://www.madeit.be/)

# Support

Support github or mail: tjebbe.lievens@madeit.be

# Contributing

Please try to follow the psr-2 coding style guide. http://www.php-fig.org/psr/psr-2/
# License

This package is licensed under LGPL. You are free to use it in personal and commercial projects. The code can be forked and modified, but the original copyright author should always be included!