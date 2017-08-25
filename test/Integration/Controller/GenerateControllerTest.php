<?php

namespace Reinfi\OptimizedServiceManager\Integration\Controller;

use Reinfi\OptimizedServiceManager\Controller\GenerateController;
use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

/**
 * @package Reinfi\OptimizedServiceManager\Integration\Controller
 */
class GenerateControllerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itGeneratesServiceManager()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../resources/config.php'
        );

        $optimizerService = $serviceManager->get(OptimizerServiceInterface::class);

        $controller = new GenerateController($optimizerService);

        $event = new MvcEvent();
        $event->setRouteMatch(new RouteMatch(['with-initializers' => false]));
        $controller->setEvent($event);

        $console = $this->prophesize(AdapterInterface::class);
        $controller->setConsole($console->reveal());

        $controller->indexAction();

        $this->assertFileExists(
            realpath(__DIR__ . '/../../../src/') . DIRECTORY_SEPARATOR  . OptimizerServiceInterface::SERVICE_MANAGER_FILENAME,
            'File should exist at source root directory'
        );
    }

    /**
     * @test
     */
    public function itGeneratesServiceManagerWithOptions()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../resources/config.php'
        );

        $optimizerService = $serviceManager->get(OptimizerServiceInterface::class);

        $controller = new GenerateController($optimizerService);

        $event = new MvcEvent();
        $event->setRouteMatch(new RouteMatch(['with-initializers' => true]));
        $controller->setEvent($event);

        $console = $this->prophesize(AdapterInterface::class);
        $controller->setConsole($console->reveal());

        $controller->indexAction();

        $this->assertFileExists(
            realpath(__DIR__ . '/../../../src/') . DIRECTORY_SEPARATOR  . OptimizerServiceInterface::SERVICE_MANAGER_FILENAME,
            'File should exist at source root directory'
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();

        $filePath = realpath(__DIR__ . '/../../../src/') . DIRECTORY_SEPARATOR  . OptimizerServiceInterface::SERVICE_MANAGER_FILENAME;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}