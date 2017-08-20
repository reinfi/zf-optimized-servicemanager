<?php

namespace Reinfi\OptimizedServiceManager\Types;

/**
 * @package Reinfi\OptimizedServiceManager\Types
 */
interface TypeInterface
{
    /**
     * @return string
     */
    public function getService(): string;
}