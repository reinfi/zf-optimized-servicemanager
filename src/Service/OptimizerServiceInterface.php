<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Nette\PhpGenerator\PhpNamespace;

/**
 * Interface OptimizerServiceInterface
 *
 * @package Reinfi\OptimizedServiceManager\Service
 */
interface OptimizerServiceInterface
{
    /**
     * @var string
     */
    const SERVICE_MANAGER_CLASSNAME = 'Manager';

    /**
     * @var string
     */
    const SERVICE_MANAGER_NAMESPACE = 'Reinfi\OptimizedServiceManager';

    /**
     * @var string
     */
    const SERVICE_MANAGER_FQCN = 'Reinfi\OptimizedServiceManager\Manager';

    /**
     * @var string
     */
    const SERVICE_MANAGER_FILENAME = 'Manager.php';

    /**
     * @return PhpNamespace
     */
    public function generate(): PhpNamespace;
}