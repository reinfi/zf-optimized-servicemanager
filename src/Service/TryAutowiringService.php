<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Exception\AutoWiringNotPossibleException;
use Reinfi\DependencyInjection\Service\AutoWiring\ResolverService;
use Reinfi\DependencyInjection\Service\AutoWiringService;
use Reinfi\OptimizedServiceManager\Types\AutoWire;
use Reinfi\OptimizedServiceManager\Types\Invokable;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class TryAutowiringService
{
    /**
     * @var ContainerInterface|ServiceLocatorInterface
     */
    private $container;

    /**
     * @var ResolverService
     */
    private $resolverService;

    /**
     * @param ContainerInterface|ServiceLocatorInterface $container
     * @param ResolverService                            $resolverService
     */
    public function __construct($container, ResolverService $resolverService
    ) {
        $this->container = $container;
        $this->resolverService = $resolverService;
    }

    /**
     * @param string $className
     *
     * @return null|AutoWire|Invokable
     */
    public function tryAutowiring(string $className)
    {
        if ($this->validForAutoWiring($className) === false) {
            return null;
        }

        try {
            $injections = $this->resolverService->resolve(
                $className
            );

            if (count($injections) === 0) {
                return new Invokable($className);
            }

            foreach ($injections as $index => $injection) {
                $injections[$index] = $injection($this->container);
            }

            $this->checkInstanceEquality($className, $injections);

            return new AutoWire($className);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function validForAutoWiring(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $reflCass = new \ReflectionClass($className);

        if ($reflCass->isAbstract()) {
            return false;
        }

        $constructor = $reflCass->getConstructor();

        if ($constructor === null) {
            return true;
        }

        return (
            $constructor->isPublic()
            && !$constructor->isAbstract()
        );
    }
    /**
     * @param string $className
     * @param array  $injections
     *
     * @throws AutoWiringNotPossibleException
     */
    private function checkInstanceEquality(string $className, array $injections)
    {
        $autoWiringProperties = $this->buildPropertyValues(
            $this->buildInstance($className, $injections)
        );

        $containerProperties = $this->buildPropertyValues(
            $this->buildContainerInstance($className)
        );

        $equal = $containerProperties === $autoWiringProperties;

        if ($equal === false) {
            throw new AutoWiringNotPossibleException($className);
        }
    }

    /**
     * @param string $className
     * @param array  $injections
     *
     * @return object
     */
    private function buildInstance(string $className, array $injections)
    {
        $reflClass = new \ReflectionClass($className);

        $instance = $reflClass->newInstanceArgs($injections);

        return $instance;
    }

    /**
     * @param string $className
     *
     * @return object
     */
    private function buildContainerInstance(string $className)
    {
        return $this->container->get($className);
    }

    /**
     * @param object $instance
     *
     * @return array
     */
    private function buildPropertyValues($instance)
    {
        $reflClass = new \ReflectionClass($instance);

        $properties = $reflClass->getProperties(
            \ReflectionProperty::IS_PUBLIC
            | \ReflectionProperty::IS_PROTECTED
            | \ReflectionProperty::IS_PRIVATE
        );

        return array_map(
            function (\ReflectionProperty $property) use ($instance)
            {
                $property->setAccessible(true);

                return $property->getValue($instance);
            },
            $properties
        );
    }
}