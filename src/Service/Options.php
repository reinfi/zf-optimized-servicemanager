<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Zend\Stdlib\AbstractOptions;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class Options extends AbstractOptions
{
    /**
     * @var bool
     */
    private $withInitializers = false;

    /**
     * @return bool
     */
    public function isWithInitializers(): bool
    {
        return $this->withInitializers;
    }

    /**
     * @param bool $withInitializers
     *
     * @return Options
     */
    public function setWithInitializers(bool $withInitializers): Options
    {
        $this->withInitializers = $withInitializers;

        return $this;
    }
}