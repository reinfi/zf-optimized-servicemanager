<?php

namespace Reinfi\OptimizedServiceManager\Controller;

use Nette\PhpGenerator\PhpNamespace;
use Reinfi\OptimizedServiceManager\Service\OptimizerServiceInterface;
use Reinfi\OptimizedServiceManager\Service\Options;
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
        $filePath = $this->getFilePath();
        // Remove old implementation, so we don't use it while generating.
        if (
            class_exists(OptimizerServiceInterface::SERVICE_MANAGER_FQCN, false)
            && file_exists($filePath)
        ) {
            unlink($filePath);
        }

        $options = $this->getOptions();
        $namespace = $this->optimizerService->generate($options);

        $classFile = $this->buildClassFile($namespace);
        file_put_contents(
            $filePath,
            $classFile
        );

        $this->console->writeLine('Finished generating optimized service manager');
    }

    /**
     * @return Options
     */
    private function getOptions(): Options
    {
        $params = $this->params()->fromRoute();

        return new Options([
            'withInitializers' => $params['with-initializers'] !== false,
        ]);
    }

    /**
     * @param PhpNamespace $namespace
     *
     * @return string
     */
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