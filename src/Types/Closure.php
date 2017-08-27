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
    private $className;

    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @inheritDoc
     */
    public function getService(): string
    {
        return $this->className;
    }

}