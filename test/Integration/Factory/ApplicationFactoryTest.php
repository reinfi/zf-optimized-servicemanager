<?php

namespace Reinfi\OptimizedServiceManager\Integration\Factory;

use Reinfi\OptimizedServiceManager\Controller\GenerateController;
use Reinfi\OptimizedServiceManager\DelegatorFactory\ApplicationFactory;
use Reinfi\OptimizedServiceManager\Integration\AbstractIntegrationTest;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

/**
 * Class ApplicationFactory
 *
 * @package Reinfi\OptimizedServiceManager\Integration\Factory
 * @author Martin Rintelen <martin.rintelen@check24.de>
 */
class ApplicationFactoryTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function itReplacesApplicationServiceManagerWithOptimizedManager()
    {
        $this->createOptimizedManager();

        $container = $this->prophesize(ServiceManager::class);
        $container->get('EventManager')
            ->willReturn($this->prophesize(EventManagerInterface::class)->reveal());
        $container->has('config')
            ->willReturn(false);
        $container->get('config')
            ->willReturn([]);
        $container->get('Request')
            ->willReturn($this->prophesize(RequestInterface::class)->reveal());
        $container->get('Response')
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal());

        $factory = new ApplicationFactory();

        $instance = $factory->createDelegatorWithName(
            $container->reveal(),
            Application::class,
            Application::class,
            [
                $this,
                'factoryCallback'
            ]
        );

        $this->assertInstanceOf(
            Application::class,
            $instance
        );

        $this->assertEquals(
            OptimizerServiceInterface::SERVICE_MANAGER_FQCN,
            get_class($instance->getServiceManager()),
            'service manager should be an instance of the optimized manager'
        );
    }

    /**
     * @test
     */
    public function itCreatesApplicationWithOptimizedManager()
    {
        $this->createOptimizedManager();

        $container = $this->prophesize(ServiceManager::class);
        $container->get('EventManager')
                  ->willReturn($this->prophesize(EventManagerInterface::class)->reveal());
        $container->has('config')
                  ->willReturn(true);
        $container->get('config')
                  ->willReturn(['service_manager' => []]);
        $container->get('Request')
                  ->willReturn($this->prophesize(RequestInterface::class)->reveal());
        $container->get('Response')
                  ->willReturn($this->prophesize(ResponseInterface::class)->reveal());

        $factory = new ApplicationFactory();

        $instance = $factory->createService(
            $container->reveal()
        );

        $this->assertInstanceOf(
            Application::class,
            $instance
        );

        $this->assertEquals(
            OptimizerServiceInterface::SERVICE_MANAGER_FQCN,
            get_class($instance->getServiceManager()),
            'service manager should be an instance of the optimized manager'
        );
    }

    /**
     * @test
     * @runInSeparateProcess this is required so that the autoloading does not find manager class.
     */
    public function itReturnsCallbackInstanceIfOptimizedManagerNotExists()
    {
        $factory = new ApplicationFactory();

        $container = $this->prophesize(ServiceLocatorInterface::class);

        $instance = $factory->createDelegatorWithName(
            $container->reveal(),
            Application::class,
            Application::class,
            [
                $this,
                'factoryCallback'
            ]
        );

        $this->assertInstanceOf(
            Application::class,
            $instance
        );
    }

    /**
     * @return void
     */
    protected function createOptimizedManager()
    {
        $serviceManager = $this->getServiceManager(
            require __DIR__ . '/../../resources/config.php'
        );

        $optimizerService = $serviceManager->get(OptimizerServiceInterface::class);

        $controller = new GenerateController($optimizerService);

        $console = $this->prophesize(AdapterInterface::class);
        $controller->setConsole($console->reveal());

        $controller->indexAction();
    }

    /**
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        $filePath = realpath(__DIR__ . '/../../../src/') . DIRECTORY_SEPARATOR  . OptimizerServiceInterface::SERVICE_MANAGER_FILENAME;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @return Application|object
     */
    public function factoryCallback(): Application
    {
        return $this->prophesize(Application::class)->reveal();
    }
}