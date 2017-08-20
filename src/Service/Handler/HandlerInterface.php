<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 */
interface HandlerInterface
{
    /**
     * @param TypeInterface $type
     *
     * @return InstantiationMethod
     */
    public function handle(TypeInterface $type): InstantiationMethod;
}