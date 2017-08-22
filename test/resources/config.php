<?php

return [
    'service_manager' => [
        'factories'          => [
            \Reinfi\OptimizedServiceManager\Service\Service1::class         => \Reinfi\DependencyInjection\Factory\AutoWiringFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Service2::class         => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Service3::class         => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ServiceDelegator::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'delegators'         => [
            \Reinfi\OptimizedServiceManager\Service\ServiceDelegator::class => [
                'MyDelegatorClass',
            ],
        ],
        'abstract_factories' => [
            \Reinfi\OptimizedServiceManager\Service\Factory\AbstractService4Factory::class,
        ],
    ],
    'test'            => [
        'value' => 1,
    ],
];