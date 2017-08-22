<?php

namespace Reinfi\OptimizedServiceManager\Service\Factory;

use Reinfi\OptimizedServiceManager\Service\Service4;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Factory
 */
class AbstractService4Factory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreateServiceWithName(
        ServiceLocatorInterface $serviceLocator, $name, $requestedName
    ) {
        return $requestedName === Service4::class;
    }

    /**
     * @inheritDoc
     */
    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator, $name, $requestedName
    ) {
        return new Service4();
    }
}