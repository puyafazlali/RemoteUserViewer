<?php

namespace PuyaFazlali\RemoteUserViewer\Tests\Unit;

use Brain\Monkey\Functions;

class RemoteUserViewerTest extends \PuyaFazlali\RemoteUserViewer\Tests\Unit\TestCase
{

    public function testAddHooksActuallyAddsHooks()
    {
        Functions\when('register_activation_hook')->justReturn(false);
        $class = new \PuyaFazlali\RemoteUserViewer\RemoteUserViewer();
        $class->addHooks();

        self::assertSame(has_action('init', '\PuyaFazlali\RemoteUserViewer\RemoteUserViewer->addEndpoint()'), 10);
        self::assertSame(has_action('template_redirect', '\PuyaFazlali\RemoteUserViewer\RemoteUserViewer->customEndpointRedirect()'), 10);
    }
}
