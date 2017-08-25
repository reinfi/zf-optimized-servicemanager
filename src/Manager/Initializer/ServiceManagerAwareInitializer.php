<?php

namespace Reinfi\OptimizedServiceManager\Manager\Initializer;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * Class ServiceManagerAwareInitializer
 *
 * @package Reinfi\OptimizedServiceManager\Manager\Initializer
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class ServiceManagerAwareInitializer implements InitializerInterface
{
    /**
     * @inheritdoc
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ServiceManager && $instance instanceof ServiceManagerAwareInterface) {
            trigger_error(sprintf(
                'ServiceManagerAwareInterface is deprecated and will be removed in version 3.0, along '
                . 'with the ServiceManagerAwareInitializer. Please update your class %s to remove '
                . 'the implementation, and start injecting your dependencies via factory instead.',
                get_class($instance)
            ), E_USER_DEPRECATED);
            $instance->setServiceManager($serviceLocator);
        }
    }
}