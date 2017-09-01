<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;
use Reinfi\OptimizedServiceManager\Manager\Initializer\EventManagerAwareInitializer;
use Reinfi\OptimizedServiceManager\Manager\Initializer\ServiceLocatorAwareInitializer;
use Reinfi\OptimizedServiceManager\Manager\Initializer\ServiceManagerAwareInitializer;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\Application;
use Zend\Mvc\Service\ServiceListenerFactory;
use Zend\Mvc\Service\ServiceManagerConfig;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class ServiceManagerConfigService
{
    /**
     * @var array map of characters to be replaced through strtr
     */
    const CANONICAL_NAMES_REPLACEMENTS = ['-' => '', '_' => '', ' ' => '', '\\' => '', '/' => ''];

    /**
     * @var array
     */
    const DEFAULT_ALIASES = [
        'Config'             => 'config',
        'Application'        => Application::class,
        'application'        => Application::class,
        'SharedEventManager' => SharedEventManager::class,
    ];

    /**
     * @var array
     */
    const DEFAULT_INITIALIZER = [
        EventManagerAwareInitializer::class,
        ServiceLocatorAwareInitializer::class,
        ServiceManagerAwareInitializer::class,
    ];

    /**
     * @var array
     */
    private $serviceManagerConfig;

    /**
     * @param array $serviceManagerConfig
     */
    public function __construct(array $serviceManagerConfig)
    {
        $this->serviceManagerConfig = $serviceManagerConfig;
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return ClassType
     */
    public function addServiceConfig(
        ClassType $class,
        Options $options
    ): ClassType {
        $this->addFactories($class, $options);
        $this->addInvokables($class, $options);
        $this->addAliases($class, $options);
        $this->addDelegators($class, $options);
        $this->addInitializers($class, $options);
        $this->addAbstractFactories($class, $options);

        return $class;
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addFactories(ClassType $class, Options $options): Property
    {
        $services = $this->getConfigServices('factories');
        $services = $this->removeClosures($services);
        $services = $this->prepareNames($options, $services);

        return $class
            ->addProperty('factories', $services)
            ->setVisibility('protected');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addInvokables(ClassType $class, Options $options): Property
    {
        $services = $this->getConfigServices('invokables');
        $services = $this->removeClosures($services);
        $services = $this->prepareNames($options, $services);

        return $class
            ->addProperty('invokables', $services)
            ->setVisibility('protected');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addAliases(ClassType $class, Options $options): Property
    {
        $aliases = $this->getConfigServices('aliases');
        $aliases = $this->prepareNames($options, $aliases);
        $aliases = array_merge(static::DEFAULT_ALIASES, $aliases);

        return $class
            ->addProperty('aliases', $aliases)
            ->setVisibility('protected');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addDelegators(ClassType $class, Options $options): Property
    {
        $delegators = $this->getConfigServices('delegators');
        $delegators = $this->prepareNames($options, $delegators);

        return $class
            ->addProperty('delegators', $delegators)
            ->setVisibility('protected');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addInitializers(ClassType $class, Options $options): Property
    {
        $initializers = $this->getConfigServices('initializers');
        $initializers = array_merge(static::DEFAULT_INITIALIZER, $initializers);

        return $class
            ->addProperty('registeredInitializers', $initializers)
            ->setVisibility('protected');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Property
     */
    private function addAbstractFactories(ClassType $class, Options $options): Property
    {
        $factories = $this->getConfigServices('abstract_factories');

        return $class
            ->addProperty('registeredAbstractFactories', $factories)
            ->setVisibility('protected');
    }

    /**
     * @param string $key
     *
     * @return array
     */
    private function getConfigServices(string $key): array
    {
        $reflClass = ClassType::from(ServiceListenerFactory::class);

        $defaultServiceListenerConfig = $reflClass
            ->getProperty('defaultServiceConfig')
            ->getValue();

        $reflClass = ClassType::from(ServiceManagerConfig::class);

        $defaultServiceManagerConfig = $reflClass
            ->getProperty('config')
            ->getValue();

        return array_merge(
            $defaultServiceListenerConfig[$key] ?? [],
            $defaultServiceManagerConfig[$key] ?? [],
            $this->serviceManagerConfig[$key] ?? []
        );
    }

    /**
     * @param Options $options
     * @param array   $services
     *
     * @return array
     */
    private function prepareNames(Options $options, array $services): array
    {
        if (!$options->isCanonicalizeNames()) {
            return $services;
        }

        foreach ($services as $service => $factory) {
            $canonicalizedName = strtolower(strtr($service, static::CANONICAL_NAMES_REPLACEMENTS));

            $services[$canonicalizedName] = $factory;
        }

        return $services;
    }

    /**
     * @param array $services
     *
     * @return array
     */
    private function removeClosures(array $services): array
    {
        foreach ($services as $key => $callable) {
            if (is_callable($callable)) {
                unset($services[$key]);
            }
        }

        return $services;
    }
}