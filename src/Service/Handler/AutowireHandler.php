<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Psr\Container\ContainerInterface;
use Reinfi\DependencyInjection\Injection\AutoWiring;
use Reinfi\DependencyInjection\Injection\AutoWiringPluginManager;
use Reinfi\DependencyInjection\Injection\InjectionInterface;
use Reinfi\DependencyInjection\Service\AutoWiring\ResolverService;
use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 */
class AutowireHandler extends AbstractHandler
{
    /**
     * @var ResolverService
     */
    protected $resolverService;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ResolverService $resolverService
     * @param ContainerInterface $container
     */
    public function __construct(
        ResolverService $resolverService,
        ContainerInterface $container
    ) {
        $this->resolverService = $resolverService;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    protected function buildBody(
        InstantiationMethod $method,
        TypeInterface $type
    ) {
        $injections = $this->resolverService->resolve($type->getService());

        $injectionCalls = [];
        foreach ($injections as $injection) {
            switch (true) {
                case $injection instanceof AutoWiring:
                    $injectionCalls[] = $this->handleAutoWiring($injection);
                    break;
                case $injection instanceof AutoWiringPluginManager:
                    $injectionCalls[] = $this->handleAutoWiringPluginManager($injection);
                    break;
                default:
                    $injectionCalls[] = $this->handleOther($injection);
            }
        }

        $method->addBodyPart(sprintf('return new \\%s(', $type->getService()));
        $lastInjectionCall = array_pop($injectionCalls);
        foreach ($injectionCalls as $injectionCall) {
            $method->addBodyPart($injectionCall . ',');
        }
        $method->addBodyPart($lastInjectionCall);
        $method->addBodyPart(');');
    }

    /**
     * @param AutoWiring $injection
     *
     * @return string
     */
    protected function handleAutoWiring(AutoWiring $injection): string
    {
        $serviceNameProperty = new \ReflectionProperty(
            get_class($injection),
            'serviceName'
        );

        $serviceNameProperty->setAccessible(true);
        $serviceName = $serviceNameProperty->getValue($injection);

        return sprintf(
            '$this->get(\'%s\')', $serviceName
        );
    }

    /**
     * @param AutoWiringPluginManager $injection
     *
     * @return string
     */
    protected function handleAutoWiringPluginManager(AutoWiringPluginManager $injection): string
    {
        $serviceNameProperty = new \ReflectionProperty(
            get_class($injection),
            'serviceName'
        );

        $serviceNameProperty->setAccessible(true);
        $serviceName = $serviceNameProperty->getValue($injection);

        $pluginManagerProperty = new \ReflectionProperty(
            get_class($injection),
            'pluginManager'
        );

        $pluginManagerProperty->setAccessible(true);
        $pluginManager = $pluginManagerProperty->getValue($injection);

        return sprintf(
            '$this->container->get(\'%s\')->get(\'%s\')', $pluginManager, $serviceName
        );
    }

    /**
     * @param InjectionInterface $injection
     *
     * @return string
     */
    protected function handleOther(InjectionInterface $injection): string
    {
        $instance = $injection($this->container);

        return sprintf(
            '$this->container->get(\'%s\')', get_class($instance)
        );
    }
}