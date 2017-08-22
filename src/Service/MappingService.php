<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Reinfi\DependencyInjection\Factory\AutoWiringFactory;
use Reinfi\DependencyInjection\Factory\InjectionFactory;
use Reinfi\OptimizedServiceManager\Types\AutoWire;
use Reinfi\OptimizedServiceManager\Types\Delegator;
use Reinfi\OptimizedServiceManager\Types\Factory;
use Reinfi\OptimizedServiceManager\Types\Injection;
use Reinfi\OptimizedServiceManager\Types\Invokable;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class MappingService
{
    /**
     * @var array
     */
    private $serviceManagerConfig;

    /**
     * @param array $serviceManagerConfig
     */
    public function __construct(
        array $serviceManagerConfig
    ) {
        $this->serviceManagerConfig = $serviceManagerConfig;
    }

    /**
     * @return TypeInterface[]
     */
    public function buildMappings(): array
    {
        $mappings = [];

        $delegatorConfig = $this->serviceManagerConfig['delegators'] ?? [];

        $serviceConfig = array_merge(
            $this->serviceManagerConfig['factories'] ?? [],
            $this->serviceManagerConfig['invokables'] ?? []
        );

        foreach ($serviceConfig as $className => $factoryClass) {
            if (isset($delegatorConfig[$className])) {
                $mappings[$className] = new Delegator($className);

                continue;
            }

            if ($factoryClass === AutoWiringFactory::class) {
                $mappings[$className] = new AutoWire($className);

                continue;
            }

            if ($factoryClass === InjectionFactory::class) {
                $mappings[$className] = new Injection($className);

                continue;
            }

            if (
                $factoryClass === InvokableFactory::class
                || $factoryClass === $className
            ) {
                $mappings[$className] = new Invokable($className);

                continue;
            }

            $mappings[$className] = new Factory($className);
        }

        return $mappings;
    }

    /**
     * @return array
     */
    public function buildSharedMapping(): array
    {
        $sharedConfig = $this->serviceManagerConfig['shared'] ?? [];

        $serviceConfig = array_merge(
            $this->serviceManagerConfig['factories'] ?? [],
            $this->serviceManagerConfig['invokables'] ?? []
        );

        $sharedMapping = [];

        foreach ($serviceConfig as $className => $factoryClass) {
            $sharedMapping[$className] = $sharedConfig[$className] ?? true;
        }

        return $sharedMapping;
    }
}