<?php

namespace Reinfi\OptimizedServiceManager\Service;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class ServiceTryAutowire
{
    /**
     * @var Service1
     */
    private $service1;
    /**
     * @var Service2
     */
    private $service2;
    /**
     * @var Service4
     */
    private $service4;

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
        $this->service1 = $service1;
        $this->service2 = $service2;
        $this->service4 = $service4;
    }
}