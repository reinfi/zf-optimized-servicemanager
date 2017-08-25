<?php

namespace Reinfi\OptimizedServiceManager\Manager\Initializer;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class EventManagerAwareInitializer
 *
 * @package Reinfi\OptimizedServiceManager\Manager\Initializer
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class EventManagerAwareInitializer implements InitializerInterface
{
    /**
     * @inheritdoc
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (! $instance instanceof EventManagerAwareInterface) {
            return;
        }

        $eventManager = $instance->getEventManager();

        // If the instance has an EM WITH an SEM composed, do nothing.
        if ($eventManager instanceof EventManagerInterface
            && $eventManager->getSharedManager() instanceof SharedEventManagerInterface
        ) {
            return;
        }

        $instance->setEventManager($serviceLocator->get('EventManager'));
    }
}