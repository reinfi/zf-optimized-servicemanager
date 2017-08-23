<?php

namespace Reinfi\OptimizedServiceManager\Service\Handler;

use Reinfi\OptimizedServiceManager\Types\TypeInterface;

/**
 * Class TestType
 *
 * @package Reinfi\OptimizedServiceManager\Service\Handler
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class TestType implements TypeInterface
{
    /**
     * @return string
     */
    public function getService(): string
    {
        return 'Test';
    }
}