<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Reinfi\OptimizedServiceManager\Model\InstantiationMethod;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class OptimizerService implements OptimizerServiceInterface
{
    /**
     * @var array
     */
    const CANONICAL_NAME_REPLACEMENTS = ['-' => '', '_' => '', ' ' => '', '\\' => '', '/' => '', '.' => ''];

    /**
     * @var MappingService
     */
    private $mappingService;

    /**
     * @var ClassBuilderService
     */
    private $classBuilderService;

    /**
     * @var TypeHandlerService
     */
    private $typeHandlerService;

    /**
     * @param MappingService      $mappingService
     * @param ClassBuilderService $classBuilderService
     * @param TypeHandlerService  $typeHandlerService
     */
    public function __construct(
        MappingService $mappingService,
        ClassBuilderService $classBuilderService,
        TypeHandlerService $typeHandlerService
    ) {
        $this->mappingService = $mappingService;
        $this->classBuilderService = $classBuilderService;
        $this->typeHandlerService = $typeHandlerService;
    }

    /**
     * @inheritdoc
     */
    public function generate(): PhpNamespace
    {
        $namespace = $this->classBuilderService->buildNamespace(static::SERVICE_MANAGER_NAMESPACE);

        $class = $this->classBuilderService->buildClass($namespace, static::SERVICE_MANAGER_CLASSNAME);
        $class->setExtends(ServiceManager::class);

        $this->classBuilderService->addConstructor($class);
        $this->classBuilderService->addGetMethod($class);
        $this->classBuilderService->addHasMethod($class);
        $this->addSharedProperty($class);

        $injectionMapping = $this->mappingService->buildMappings();
        $instantiationMethods = $this->typeHandlerService->resolveTypes($injectionMapping);
        $methodMapping = $this->addMethods($class, $instantiationMethods);
        $this->addMappingProperty($class, $methodMapping);

        return $namespace;
    }

    /**
     * @param ClassType $class
     */
    protected function addSharedProperty(ClassType $class)
    {
        $sharedMapping = $this->mappingService->buildSharedMapping();

        $class
            ->addProperty('shared', $sharedMapping)
            ->setVisibility('protected')
            ->addComment('@var array');

    }

    /**
     * @param ClassType             $class
     * @param InstantiationMethod[] $instantiationMethods
     *
     * @return array
     */
    protected function addMethods(
        ClassType $class,
        array $instantiationMethods
    ): array {
        $methodMapping = [];

        foreach ($instantiationMethods as $instantiationMethod) {
            $method = $class->addMethod(
                sprintf(
                    'get%s',
                    $this->buildMethodName($instantiationMethod)
                )
            );

            $method
                ->setVisibility('protected')
                ->addComment('@return \\' . $instantiationMethod->getClassName());

            foreach ($instantiationMethod->getMethodBody() as $bodyPart) {
                $method->addBody($bodyPart);
            }

            $methodMapping[$instantiationMethod->getClassName()] = $method->getName();
        }

        return $methodMapping;
    }

    /**
     * @param InstantiationMethod $instantiationMethod
     *
     * @return string
     */
    protected function buildMethodName(InstantiationMethod $instantiationMethod): string
    {
        return ucfirst(
            strtr($instantiationMethod->getClassName(), static::CANONICAL_NAME_REPLACEMENTS)
        );
    }

    /**
     * @param ClassType $class
     * @param array     $methodMapping
     */
    protected function addMappingProperty(ClassType $class, array $methodMapping)
    {
        $class->addProperty('mappings', $methodMapping)
            ->setVisibility('protected')
            ->addComment('@var array');
    }
}