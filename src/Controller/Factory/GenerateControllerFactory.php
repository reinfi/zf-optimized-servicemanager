<?php

namespace Reinfi\OptimizedServiceManager\Controller\Factory;

use Reinfi\OptimizedServiceManager\Controller\GenerateController;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\Mvc\Controller\ControllerManager;

/**
 * @package Reinfi\OptimizedServiceManager\Controller\Factory
 */
class GenerateControllerFactory
{
    /**
     * @param ControllerManager $controllerManager
     *
     * @return GenerateController
     */
    public function __invoke(ControllerManager $controllerManager): GenerateController
    {
        $container = $controllerManager->getServiceLocator();

        /** @var OptimizerServiceInterface $optimizerService */
        $optimizerService = $container->get(OptimizerServiceInterface::class);

        return new GenerateController($optimizerService);
    }
}