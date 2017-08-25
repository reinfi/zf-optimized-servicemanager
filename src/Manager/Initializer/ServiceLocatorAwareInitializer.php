<?php

namespace Reinfi\OptimizedServiceManager\Manager\Initializer;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServiceLocatorAwareInitializer
 *
 * @package Reinfi\OptimizedServiceManager\Manager\Initializer
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class ServiceLocatorAwareInitializer implements InitializerInterface
{
    /**
     * @inheritDoc
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        // For service locator aware classes, inject the service
        // locator, but emit a deprecation notice. Skip plugin manager
        // implementations; they're dealt with later.
        if ($instance instanceof ServiceLocatorAwareInterface
            && ! $instance instanceof AbstractPluginManager
        ) {
            trigger_error(sprintf(
                'ServiceLocatorAwareInterface is deprecated and will be removed in version 3.0, along '
                . 'with the ServiceLocatorAwareInitializer. Please update your class %s to remove '
                . 'the implementation, and start injecting your dependencies via factory instead.',
                get_class($instance)
            ), E_USER_DEPRECATED);
            $instance->setServiceLocator($serviceLocator);
        }

        // For service locator aware plugin managers that do not have
        // the service locator already injected, inject it, but emit a
        // deprecation notice.
        if ($instance instanceof ServiceLocatorAwareInterface
            && $instance instanceof AbstractPluginManager
            && ! $instance->getServiceLocator()
        ) {
            trigger_error(sprintf(
                'ServiceLocatorAwareInterface is deprecated and will be removed in version 3.0, along '
                . 'with the ServiceLocatorAwareInitializer. Please update your %s plugin manager factory '
                . 'to inject the parent service locator via the constructor.',
                get_class($instance)
            ), E_USER_DEPRECATED);
            $instance->setServiceLocator($serviceLocator);
        }
    }
}