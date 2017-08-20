<?php

namespace Reinfi\OptimizedServiceManager\Unit\Types;

use PHPUnit\Framework\TestCase;
use Reinfi\OptimizedServiceManager\Service\Service1;
use Reinfi\OptimizedServiceManager\Types\AutoWire;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Types
 */
class AutoWireTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsServiceName()
    {
        $type = new AutoWire(Service1::class);

        $this->assertEquals(
            Service1::class,
            $type->getService()
        );
    }
}