<?php

namespace Reinfi\OptimizedServiceManager\Types;

/**
 * @package Reinfi\OptimizedServiceManager\Types
 */
class Closure implements TypeInterface
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @param string   $className
     * @param callable $callable
     */
    public function __construct(string $className, callable $callable)
    {
        $this->serviceName = $className;
        $this->callable = $callable;
    }

    /**
     * @inheritDoc
     */
    public function getService(): string
    {
        return $this->serviceName;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}