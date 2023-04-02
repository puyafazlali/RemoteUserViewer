<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace PuyaFazlali\RemoteUserViewer\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Sets up the environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        \Brain\Monkey\setUp();
    }

    /**
     * Tears down the environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Monkey\tearDown();
    }
}
