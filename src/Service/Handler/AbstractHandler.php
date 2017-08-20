<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(TypeInterface $type): InstantiationMethod
    {
        $method = new InstantiationMethod($type->getService());

        $this->buildBody($method, $type);

        return $method;
    }

    /**
     * @param InstantiationMethod $method
     * @param TypeInterface       $type
     *
     * @return void
     */
    protected abstract function buildBody(
        InstantiationMethod $method,
        TypeInterface $type
    );
}