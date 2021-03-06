<?php

return [
    'service_manager' => [
        'factories'          => [
            \Reinfi\OptimizedServiceManager\Service\Service1::class           => \Reinfi\DependencyInjection\Factory\AutoWiringFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Service2::class           => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Service3::class           => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\Service5::class           => \Reinfi\DependencyInjection\Factory\AutoWiringFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ServiceDelegator::class   => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \Reinfi\OptimizedServiceManager\Service\ServiceTryAutowire::class => function (
                \Psr\Container\ContainerInterface $container
            ) {
                return new \Reinfi\OptimizedServiceManager\Service\ServiceTryAutowire(
                    $container->get(
                        \Reinfi\OptimizedServiceManager\Service\Service1::class
                    ),
                    $container->get(
                        \Reinfi\OptimizedServiceManager\Service\Service2::class
                    ),
                    $container->get(
                        \Reinfi\OptimizedServiceManager\Service\Service4::class
                    )
                );
            },
            \Reinfi\OptimizedServiceManager\Service\ServiceClosure::class     => function (
                $container
            ) {
                return new \Reinfi\OptimizedServiceManager\Service\ServiceClosure(
                );
            },
            'ServiceClosureNative'                                            => function (
                \Psr\Container\ContainerInterface $container
            ) {
                return new \Reinfi\OptimizedServiceManager\Service\ServiceClosureNative(1);
            },
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