<?php

namespace Reinfi\OptimizedServiceManager\Types;

/**
 * @package Reinfi\OptimizedServiceManager\Types
 */
class Delegator implements TypeInterface
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @param string $serviceName
     */
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @inheritDoc
     */
    public function getService(): string
    {
        return $this->serviceName;
    }
}