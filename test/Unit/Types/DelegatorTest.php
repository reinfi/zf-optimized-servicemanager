<?php

namespace Reinfi\OptimizedServiceManager\Unit\Types;

use PHPUnit\Framework\TestCase;
use Reinfi\OptimizedServiceManager\Service\Service1;
use Reinfi\OptimizedServiceManager\Types\Delegator;

/**
 * @package Reinfi\OptimizedServiceManager\Unit\Types
 */
class DelegatorTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsServiceName()
    {
        $type = new Delegator(Service1::class);

        $this->assertEquals(
            Service1::class,
            $type->getService()
        );
    }
}