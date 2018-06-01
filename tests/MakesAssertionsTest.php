<?php

use MadeITBelgium\Chrome\Browser;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class MakesAssertionsTest extends TestCase
{
    public function test_assert_title()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getTitle')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertTitle('foo');

        try {
            $browser->assertTitle('Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected title [Foo] does not equal actual title [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_title_contains()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getTitle')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertTitleContains('fo');

        try {
            $browser->assertTitleContains('Fo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected value [Fo] within title [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_url_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.google.com?foo=bar',
            'http://www.google.com:80/test?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertUrlIs('http://www.google.com');
        $browser->assertUrlIs('http://www.google.com:80/test');
        $browser->assertUrlIs('*google*');

        try {
            $browser->assertUrlIs('http://www.google.com');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual URL [http://www.google.com:80/test?foo=bar] does not equal expected URL [http://www.google.com].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            '/foo',
            'foo/bar',
            'foo/1/bar',
            'foo/1/bar/1'
        );
        $browser = new Browser($driver);

        $browser->assertPathIs('/foo');
        $browser->assertPathIs('foo/bar');
        $browser->assertPathIs('foo/*/bar');
        $browser->assertPathIs('foo/*/bar/*');
        $browser->assertPathIs('foo/1/bar/1');

        try {
            $browser->assertPathIs('foo/*/');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual path [foo/1/bar/1] does not equal expected path [foo/*/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_begins_with()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathBeginsWith('/tes');

        try {
            $browser->assertPathBeginsWith('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual path [/test] does not begin with expected path [test].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathIsNot('test');

        try {
            $browser->assertPathIsNot('/test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Path [/test] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIs('b*z');

        try {
            $browser->assertFragmentIs('ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual fragment [baz] does not equal expected fragment [ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_begins_with()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentBeginsWith('ba');

        try {
            $browser->assertFragmentBeginsWith('Ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual fragment [baz] does not begin with expected fragment [Ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIsNot('Baz');

        try {
            $browser->assertFragmentIsNot('baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Fragment [baz] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_route_is()
    {
        require_once __DIR__.'/stubs/route.php';

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            '/test/1'
        );
        $browser = new Browser($driver);

        $browser->assertRouteIs('test', ['id' => 1]);

        try {
            $browser->assertRouteIs('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual path [/test/1] does not equal expected path [/test/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com',
            'http://www.google.com',
            'http://www.google.com/?foo'
        );
        $browser = new Browser($driver);

        try {
            $browser->assertQueryStringHas('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected query string in [http://www.google.com].',
                $e->getMessage()
            );
        }

        $browser->assertQueryStringHas('foo');

        try {
            $browser->assertQueryStringHas('bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected query string parameter [bar] in [http://www.google.com/?foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringHas('foo', 'bar');

        try {
            $browser->assertQueryStringHas('foo', '');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Query string parameter [foo] had value [bar], but expected [].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_missing()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com',
            'http://www.google.com/?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringMissing('foo');
        $browser->assertQueryStringMissing('Foo');

        try {
            $browser->assertQueryStringMissing('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Found unexpected query string parameter [foo] in [http://www.google.com/?foo=bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_present()
    {
        $driver = Mockery::mock(StdClass::class);
        $element = Mockery::mock(StdClass::class);
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('find')->with('foo')->andReturn(
            $element,
            null
        );
        $browser = new Browser($driver, $resolver);

        $browser->assertPresent('foo');

        try {
            $browser->assertPresent('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Element [body foo] is not present.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_enabled()
    {
        $driver = Mockery::mock(StdClass::class);
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('resolveForField->isEnabled')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver, $resolver);

        $browser->assertEnabled('foo');

        try {
            $browser->assertEnabled('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be enabled, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_disabled()
    {
        $driver = Mockery::mock(StdClass::class);
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('resolveForField->isEnabled')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver, $resolver);

        $browser->assertDisabled('foo');

        try {
            $browser->assertDisabled('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be disabled, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_focused()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            true,
            false
        );
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('resolveForField')->with('foo')->andReturn('element');
        $browser = new Browser($driver, $resolver);

        $browser->assertFocused('foo');

        try {
            $browser->assertFocused('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be focused, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_focused()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            false,
            true
        );
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('resolveForField')->with('foo')->andReturn('element');
        $browser = new Browser($driver, $resolver);

        $browser->assertNotFocused('foo');

        try {
            $browser->assertNotFocused('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected element [foo] not to be focused, but it was.',
                $e->getMessage()
            );
        }
    }
}
