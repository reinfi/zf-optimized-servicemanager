<?php

namespace Reinfi\OptimizedServiceManager\Unit\Types;

use PHPUnit\Framework\TestCase;
use Reinfi\OptimizedServiceManager\Service\Service1;
use Reinfi\OptimizedServiceManager\Types\Invokable;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Types
 */
class InvokableTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsServiceName()
    {
        $type = new Invokable(Service1::class);

        $this->assertEquals(
            Service1::class,
            $type->getService()
        );
    }
}