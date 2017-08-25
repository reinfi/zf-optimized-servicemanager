<?php

namespace Reinfi\OptimizedServiceManager;

use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Zend\Console\Console;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManagerInterface;

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
                'replaceServiceManager',
            ],
            PHP_INT_MAX
        );
    }

    /**
     * @param ModuleEvent $event
     */
    public function replaceServiceManager(ModuleEvent $event)
    {
        $container = $event->getParam('ServiceManager');

        if (!Console::isConsole() && class_exists(OptimizerService::SERVICE_MANAGER_FQCN)) {
            $managerClass = OptimizerService::SERVICE_MANAGER_FQCN;

            $event->setParam('ServiceManager', new $managerClass($container, $event->getConfigListener()));
        }
    }
}