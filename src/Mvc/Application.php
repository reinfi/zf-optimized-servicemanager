<?php

namespace Reinfi\OptimizedServiceManager\Mvc;

use Reinfi\OptimizedServiceManager\Service\OptimizerService;
use Zend\Console\Console;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Mvc
 */
class Application extends \Zend\Mvc\Application
{
    /**
     * @inheritdoc
     */
    public static function init($configuration = [])
    {
        if (Console::isConsole() || !class_exists(OptimizerService::SERVICE_MANAGER_FQCN)) {
            return parent::init($configuration);
        }

        $managerClass = OptimizerService::SERVICE_MANAGER_FQCN;

        /** @var ServiceManager $serviceManager */
        $serviceManager = new $managerClass();
        $serviceManager->setService('ApplicationConfig', $configuration);

        // Load modules
        $serviceManager->get('ModuleManager')->loadModules();

        // Prepare list of listeners to bootstrap
        $listenersFromAppConfig     = isset($configuration['listeners']) ? $configuration['listeners'] : [];
        $config                     = $serviceManager->get('config');
        $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];

        $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));

        return $serviceManager->get('Application')->bootstrap($listeners);
    }
}