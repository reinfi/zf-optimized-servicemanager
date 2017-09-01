<?php

namespace Reinfi\OptimizedServiceManager;

use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\DependencyInjection
 */
class Module implements ConfigProviderInterface, InitProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param ModuleManagerInterface $manager
     */
    public function init(ModuleManagerInterface $manager)
    {
        $manager->getEventManager()->attach(
            ModuleEvent::EVENT_LOAD_MODULES_POST,
            [
                $this,
                'setConfig',
            ],
            PHP_INT_MAX
        );
    }

    /**
     * @param ModuleEvent $event
     */
    public function setConfig(ModuleEvent $event)
    {
        /** @var ServiceManager $container */
        $container = $event->getParam('ServiceManager');

        $managerClass = OptimizerServiceInterface::SERVICE_MANAGER_FQCN;
        if ($container instanceof $managerClass) {
            $container
                ->setAllowOverride(true)
                ->setService(
                    'config',
                    $event->getConfigListener()->getMergedConfig(false)
                );
        }
    }
}