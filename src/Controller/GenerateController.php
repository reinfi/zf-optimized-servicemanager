<?php

namespace Reinfi\OptimizedServiceManager\Controller;

use Nette\PhpGenerator\PhpNamespace;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Zend\Mvc\Controller\AbstractConsoleController;

/**
 * @package Reinfi\OptimizedServiceManager\Controller
 */
class GenerateController extends AbstractConsoleController
{
    /**
     * @var OptimizerServiceInterface
     */
    private $optimizerService;

    /**
     * @param OptimizerServiceInterface $optimizerService
     */
    public function __construct(OptimizerServiceInterface $optimizerService)
    {
        $this->optimizerService = $optimizerService;
    }

    /**
     *
     */
    public function indexAction()
    {
        $namespace = $this->optimizerService->generate();

        $classFile = $this->buildClassFile($namespace);
        $filePath = $this->getFilePath();
        file_put_contents(
            $filePath,
            $classFile
        );

        $this->console->writeLine('Finished generating optimized service manager');
    }

    private function buildClassFile(PhpNamespace $namespace): string
    {
        return sprintf(
            '<?php %s%s',
            PHP_EOL,
            (string) $namespace
        );
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        return realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . OptimizerServiceInterface::SERVICE_MANAGER_FILENAME;
    }
}