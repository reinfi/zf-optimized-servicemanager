<?php

namespace Reinfi\OptimizedServiceManager\Service;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class ServiceTryAutowire
{
    /**
     * @param Service1 $service1
     * @param Service2 $service2
     * @param Service4 $service4
     */
    public function __construct(
        Service1 $service1,
        Service2 $service2,
        Service4 $service4
    ) {
    }
}