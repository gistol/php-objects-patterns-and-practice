<?php

namespace Tests\Chapter10;

use Acme\Chapter10\Decorator\AcceptEncodingDecorator;
use Acme\Chapter10\Decorator\CacheControlDecorator;
use Acme\Chapter10\Decorator\UserAgentHeader;
use PHPUnit\Framework\TestCase;

class DecoratorTest extends TestCase
{
    public function testOneLevelDecorating()
    {
        $userAgent    = new UserAgentHeader('Mozilla');
        $cacheControl = new CacheControlDecorator($userAgent, 'max-age=0');

        $headers      = $cacheControl();

        $this->assertArrayHasKey($userAgent->fieldname(), $headers);
        $this->assertArrayHasKey($cacheControl->fieldname(), $headers);
    }

    public function testTwoLevelsDecorating()
    {
        $userAgent      = new UserAgentHeader('Mozilla');
        $acceptEncoding = new AcceptEncodingDecorator($userAgent, 'deflate');
        $cacheControl   = new CacheControlDecorator($acceptEncoding, 'max-age=0');

        $headers = $cacheControl();

        $this->assertArrayHasKey($userAgent->fieldname(), $headers);
        $this->assertArrayHasKey($cacheControl->fieldname(), $headers);
        $this->assertArrayHasKey($acceptEncoding->fieldname(), $headers);
    }

    public function testReplacing()
    {
        $headers = (new CacheControlDecorator(
            new AcceptEncodingDecorator(
                new CacheControlDecorator(
                    new UserAgentHeader('Mozilla'), 'max-age=10'
                ), 'deflate'
            ), 'max-age=0'
        ))();

        $this->assertContains('max-age=0', $headers);
        $this->assertNotContains('max-age=10', $headers);
    }
}
