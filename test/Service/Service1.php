<?php

namespace Reinfi\OptimizedServiceManager\Service;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class Service1
{
    /**
     * @param Service2 $service2
     * @param Service3 $service3
     */
    public function __construct(
        Service2 $service2,
        Service3 $service3
    ) {
    }
}