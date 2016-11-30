<?php

namespace Bolt\Extension\levin\phpfunctions\Tests;

use Bolt\Tests\BoltUnitTest;
use Bolt\Extension\levin\phpfunctions\Extension;

/**
 * Ensure that the ExtensionName extension loads correctly.
 *
 */
class phpfunctionsTest extends BoltUnitTest
{
    public function testExtensionLoads()
    {
        $app = $this->getApp();
        $extension = new Extension($app);
        $app['extensions']->register( $extension );
        $name = $extension->getName();
        $this->assertSame($name, 'colourpicker');
        $this->assertSame($extension, $app["extensions.$name"]);
    }
}
