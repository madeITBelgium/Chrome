<?php

use MadeITBelgium\Chrome\Browser;
use MadeITBelgium\Chrome\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    public function test_within_method_triggers_assertion()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent(), function ($browser) {
            $this->assertTrue($browser->component->asserted);

            $browser->within($nested = new TestNestedComponent(), function () use ($nested) {
                $this->assertTrue($nested->asserted);
            });
        });
    }

    public function test_resolver_prefix()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent(), function ($browser) {
            $this->assertEquals('body #component-root', $browser->resolver->prefix);

            $browser->within($nested = new TestNestedComponent(), function ($browser) {
                $this->assertEquals('body #component-root #nested-root', $browser->resolver->prefix);

                $browser->with('prefix', function ($browser) {
                    $this->assertEquals('body #component-root #nested-root prefix', $browser->resolver->prefix);
                });
            });
        });
    }

    public function test_component_macros()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent(), function ($browser) {
            $browser->doSomething();
            $this->assertTrue($browser->component->macroed);

            $browser->within($nested = new TestNestedComponent(), function ($browser) use ($nested) {
                $browser->doSomething();
                $this->assertTrue($nested->macroed);
            });
        });
    }

    public function test_component_elements()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent(), function ($browser) {
            $this->assertEquals([
                '@component-alias'  => '#component-alias',
                '@overridden-alias' => '#not-overridden',
            ], $browser->resolver->elements);

            $browser->within($nested = new TestNestedComponent(), function ($browser) {
                $this->assertEquals([
                    '@nested-alias'     => '#nested-alias',
                    '@overridden-alias' => '#overridden',
                    '@component-alias'  => '#component-alias',
                ], $browser->resolver->elements);
            });
        });
    }

    public function test_root_selector_can_be_chrome_hook()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent();
        $component->selector = '@chrome-hook-root';

        $browser->within($component, function ($browser) {
            $this->assertEquals('body [chrome="chrome-hook-root"]', $browser->resolver->prefix);
        });
    }

    public function test_root_selector_can_be_element_alias()
    {
        $driver = Mockery::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent();
        $component->selector = '@component-alias';

        $browser->within($component, function ($browser) {
            $this->assertEquals('body #component-alias', $browser->resolver->prefix);
        });
    }
}

class TestComponent extends Component
{
    public $selector = '#component-root';
    public $asserted = false;
    public $macroed = false;

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function selector()
    {
        return $this->selector;
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public function elements()
    {
        return [
            '@component-alias'  => '#component-alias',
            '@overridden-alias' => '#not-overridden',
        ];
    }
}

class TestNestedComponent extends Component
{
    public $asserted = false;
    public $macroed = false;

    public function selector()
    {
        return '#nested-root';
    }

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public function elements()
    {
        return [
            '@nested-alias'     => '#nested-alias',
            '@overridden-alias' => '#overridden',
        ];
    }
}
