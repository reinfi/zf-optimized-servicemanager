<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Psr\Container\ContainerInterface;
use Reinfi\OptimizedServiceManager\Service\Handler\HandlerInterface;
use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Service\Handler\AutowireHandler;
use Reinfi\OptimizedServiceManager\Service\Handler\DelegatorHandler;
use Reinfi\OptimizedServiceManager\Service\Handler\FactoryHandler;
use Reinfi\OptimizedServiceManager\Service\Handler\InvokableHandler;
use Reinfi\OptimizedServiceManager\Types\AutoWire;
use Reinfi\OptimizedServiceManager\Types\Delegator;
use Reinfi\OptimizedServiceManager\Types\Factory;
use Reinfi\OptimizedServiceManager\Types\Injection;
use Reinfi\OptimizedServiceManager\Types\Invokable;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class TypeHandlerService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    protected static $typeHandler = [
        Invokable::class => InvokableHandler::class,
        Delegator::class => DelegatorHandler::class,
        Factory::class   => FactoryHandler::class,
        Injection::class => FactoryHandler::class,
        AutoWire::class  => AutowireHandler::class,
    ];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param TypeInterface[] $typeMapping
     *
     * @return InstantiationMethod[]
     */
    public function resolveTypes(array $typeMapping): array
    {
        $instantiationMethods = [];

        foreach ($typeMapping as $className => $type) {
            if (!array_key_exists(get_class($type), static::$typeHandler)) {
                continue;
            }

            /** @var HandlerInterface $handler */
            $handler = $this->container->get(
                static::$typeHandler[get_class($type)]
            );

            $instantiationMethods[$className] = $handler->handle($type);
        }

        return $instantiationMethods;
    }

    /**
     * @param string $typeClass
     * @param string $handlerClass
     */
    public static function addHandler(string $typeClass, string $handlerClass)
    {
        static::$typeHandler[$typeClass] = $handlerClass;
    }
}