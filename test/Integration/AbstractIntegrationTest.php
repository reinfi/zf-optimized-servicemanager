<?php

namespace Reinfi\OptimizedServiceManager\Integration;

use PHPUnit\Framework\TestCase;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * @package Reinfi\OptimizedServiceManager\Integration
 */
abstract class AbstractIntegrationTest extends TestCase
{
    /**
     * @param array $config
     *
     * @return ServiceManager
     */
    protected function getServiceManager(array $config = []): ServiceManager
    {
        $moduleServices = require __DIR__ . '/../../config/module.config.php';
        $diService = require __DIR__ . '/../../vendor/reinfi/zf-dependency-injection/config/module.config.php';

        $moduleServices = ArrayUtils::merge(
            $moduleServices['service_manager'] ?? [],
            $diService['service_manager'] ?? []
        );

        $services = ArrayUtils::merge(
            $moduleServices ?? [],
            $config['service_manager'] ?? []
        );
        $smConfig = new ServiceManagerConfig($services);
        $container = new ServiceManager($smConfig);

        $container->setService('config', $config);

        return $container;
    }
}