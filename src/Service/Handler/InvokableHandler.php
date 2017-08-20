<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 */
class InvokableHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    protected function buildBody(
        InstantiationMethod $method,
        TypeInterface $type
    ) {
        $method->addBodyPart(
            sprintf('return new \\%s();', $type->getService())
        );
    }
}