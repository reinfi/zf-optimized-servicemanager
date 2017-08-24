<?php

return [
    'service_manager' => [
        'aliases'    => [
            \Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface::class => \Reinfi\OptimizedServiceManager\Service\OptimizerService::class,
        ],
        'factories'  => [
            \Reinfi\OptimizedServiceManager\Service\OptimizerService::class         => \Reinfi\OptimizedServiceManager\Service\Factory\OptimizerServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\MappingService::class           => \Reinfi\OptimizedServiceManager\Service\Factory\MappingServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ClassBuilderService::class      => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\TypeHandlerService::class       => \Reinfi\OptimizedServiceManager\Service\Factory\TypeHandlerServiceFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler::class  => \Reinfi\OptimizedServiceManager\Service\Handler\Factory\AutowireHandlerFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\DelegatorHandler::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\FactoryHandler::class   => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Handler\InvokableHandler::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators' => [
            'Application' => [
                \Reinfi\OptimizedServiceManager\DelegatorFactory\ApplicationFactory::class,
            ],
        ],
    ],
    'console'         => [
        'router' => [
            'routes' => [
                'reinfi-di-cache-warmup' => [
                    'options' => [
                        'route'    => 'reinfi:optimize service-manager [--with-initializers]',
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