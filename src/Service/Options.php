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
     * @var bool
     */
    private $canonicalizeNames = false;

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

    /**
     * @return bool
     */
    public function isCanonicalizeNames(): bool
    {
        return $this->canonicalizeNames;
    }

    /**
     * @param bool $canonicalizeNames
     * @return Options
     */
    public function setCanonicalizeNames(bool $canonicalizeNames)
    {
        $this->canonicalizeNames = $canonicalizeNames;

        return $this;
    }
}