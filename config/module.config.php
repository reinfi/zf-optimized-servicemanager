<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface::class => \Reinfi\OptimizedServiceManager\Service\OptimizerService::class,
        ],
        'factories'  => [
            \Reinfi\OptimizedServiceManager\Service\OptimizerService::class                           => \Reinfi\OptimizedServiceManager\Service\Factory\OptimizerServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\MappingService::class                             => \Reinfi\OptimizedServiceManager\Service\Factory\MappingServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ClassBuilderService::class                        => \Reinfi\OptimizedServiceManager\Service\Factory\ClassBuilderServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ServiceManagerConfigService::class                => \Reinfi\OptimizedServiceManager\Service\Factory\ServiceManagerConfigServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\TypeHandlerService::class                         => \Reinfi\OptimizedServiceManager\Service\Factory\TypeHandlerServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\TryAutowiringService::class                       => \Reinfi\OptimizedServiceManager\Service\Factory\TryAutowiringServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler::class                    => \Reinfi\OptimizedServiceManager\Service\Handler\Factory\AutowireHandlerFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\DelegatorHandler::class                   => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\FactoryHandler::class                     => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\InvokableHandler::class                   => \Zend\ServiceManager\Factory\InvokableFactory::class,

            // Required because of AbstractFactories requesting config class at a none existing point.
            'ServiceListenerInterface' => \Reinfi\OptimizedServiceManager\Mvc\Service\Factory\ServiceListenerFactory::class,

            // Avoid need to explicitly add di module to application
            \Reinfi\DependencyInjection\Config\ModuleConfig::class                                    => \Reinfi\DependencyInjection\Config\Factory\ModuleConfigFactory::class,
            \Reinfi\DependencyInjection\Service\AutoWiring\ResolverService::class                     => \Reinfi\DependencyInjection\Service\AutoWiring\Factory\ResolverServiceFactory::class,
            \Reinfi\DependencyInjection\Service\AutoWiring\Resolver\ContainerResolver::class          => \Reinfi\DependencyInjection\Service\AutoWiring\Resolver\Factory\ContainerResolverFactory::class,
            \Reinfi\DependencyInjection\Service\AutoWiring\Resolver\ContainerInterfaceResolver::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\DependencyInjection\Service\AutoWiring\Resolver\PluginManagerResolver::class      => \Reinfi\DependencyInjection\Service\AutoWiring\Resolver\Factory\PluginManagerResolverFactory::class,
        ],
    ],
    'console'         => [
        'router' => [
            'routes' => [
                'reinfi-di-cache-warmup' => [
                    'options' => [
                        'route'    => 'reinfi:optimize service-manager [--with-initializers] [--canonicalize-names] [--try-autowire]',
                        'defaults' => [
                            'controller' => \Reinfi\OptimizedServiceManager\Controller\GenerateController::class,
                            'action'     => 'index',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'factories' => [
            \Reinfi\OptimizedServiceManager\Controller\GenerateController::class => \Reinfi\OptimizedServiceManager\Controller\Factory\GenerateControllerFactory::class,
        ],
    ],
];