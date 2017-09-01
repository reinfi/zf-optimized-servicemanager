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
     * @var ServiceManagerConfigService
     */
    private $serviceManagerConfigService;

    /**
     * @param MappingService              $mappingService
     * @param ClassBuilderService         $classBuilderService
     * @param TypeHandlerService          $typeHandlerService
     * @param ServiceManagerConfigService $serviceManagerConfigService
     */
    public function __construct(
        MappingService $mappingService,
        ClassBuilderService $classBuilderService,
        TypeHandlerService $typeHandlerService,
        ServiceManagerConfigService $serviceManagerConfigService
    ) {
        $this->mappingService = $mappingService;
        $this->classBuilderService = $classBuilderService;
        $this->typeHandlerService = $typeHandlerService;
        $this->serviceManagerConfigService = $serviceManagerConfigService;
    }

    /**
     * @inheritdoc
     */
    public function generate(Options $options = null): PhpNamespace
    {
        if ($options === null) {
            $options = new Options();
        }

        $namespace = $this->classBuilderService->buildNamespace(static::SERVICE_MANAGER_NAMESPACE);

        $class = $this->classBuilderService->buildClass($namespace, static::SERVICE_MANAGER_CLASSNAME);
        $class->setExtends(ServiceManager::class);

        $this->classBuilderService->addConstructor($class);
        $this->classBuilderService->addOverwriteMethods($class, $options);
        $this->addSharedProperty($class);

        $this->serviceManagerConfigService->addServiceConfig($class, $options);

        $injectionMapping = $this->mappingService->buildMappings($options);
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
     * @param Options               $options
     *
     * @return array
     */
    protected function addMethods(
        ClassType $class,
        array $instantiationMethods,
        Options $options
    ): array {
        $methodMapping = [];

        foreach ($instantiationMethods as $instantiationMethod) {
            if (!class_exists($instantiationMethod->getClassName())) {
                continue;
            }

            $method = $class->addMethod(
                sprintf(
                    'get%s',
                    $this->buildMethodName($instantiationMethod)
                )
            );

            $method
                ->setVisibility('protected')
                ->addComment('@return \\' . $instantiationMethod->getClassName() . '|object');

            foreach ($instantiationMethod->getMethodBody() as $bodyPart) {
                $method->addBody($bodyPart);
            }

            $methodMapping[$instantiationMethod->getClassName()] = $method->getName();
        }

        $aliases = $class->getProperty('aliases')->getValue();

        foreach ($aliases as $alias => $resolvedAlias) {
            if (
                isset($methodMapping[$resolvedAlias])
                && !isset($methodMapping[$alias])
            ) {
                $methodMapping[$alias] = $methodMapping[$resolvedAlias];
            }
        }

        if ($options->isCanonicalizeNames()) {
            foreach ($methodMapping as $service => $method) {
                $canonicalizedName = strtolower(strtr($service, ServiceManagerConfigService::CANONICAL_NAMES_REPLACEMENTS));

                if (!isset($methodMapping[$canonicalizedName])) {
                    $methodMapping[$canonicalizedName] = $method;
                }
            }
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