<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Zend\ServiceManager\ServiceManager;

/**
 * @package Reinfi\OptimizedServiceManager\Service
 */
class ClassBuilderService
{
    /**
     * @var array
     */
    private $serviceManagerConfig;

    /**
     * @param array $serviceManagerConfig
     */
    public function __construct(
        array $serviceManagerConfig
    ) {
        $this->serviceManagerConfig = $serviceManagerConfig;
    }

    /**
     * @param string $namespace
     *
     * @return PhpNamespace
     */
    public function buildNamespace(string $namespace): PhpNamespace
    {
        return new PhpNamespace($namespace);
    }

    /**
     * @param PhpNamespace $namespace
     * @param string       $className
     *
     * @return ClassType
     */
    public function buildClass(
        PhpNamespace $namespace,
        string $className
    ): ClassType {
        return $namespace->addClass($className);
    }

    /**
     * @param ClassType $class
     */
    public function addConstructor(ClassType $class)
    {
        $constructor = $class
            ->addMethod('__construct')
            ->setVisibility('public')
            ->addComment('@var \\' . ServiceManager::class . ' $container');

        $constructor->addParameter('container')
            ->setTypeHint(ServiceManager::class);

        $constructor
            ->addBody('parent::__construct();');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     */
    public function addOverwriteMethods(ClassType $class, Options $options)
    {
        $this->addGetMethod($class, $options);
        $this->addHasMethod($class);
        $this->overwriteCanonicalizeName($class);
        $this->overwriteCanCreateFromAbstractFactory($class);
        $this->addConfigValues($class);
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Method
     */
    private function addGetMethod(ClassType $class, Options $options): Method
    {
        $getMethod = $class->addMethod('get')
            ->setVisibility('public')
            ->addComment('@inheritdoc');

        $getMethod->addParameter('name');
        $getMethod->addParameter('usePeeringServiceManagers', true);

        $getMethod
            ->addBody('$instance = null;')
            ->addBody('if (isset($this->instances[$name])):')
            ->addBody('    return $this->instances[$name];')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('if (isset($this->mappings[$name])):')
            ->addBody('    $instance = call_user_func([$this, $this->mappings[$name]]);')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('if ($instance === null):')
            ->addBody('    $instance = parent::get($name, $usePeeringServiceManagers);')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('if ($instance === null):')
            ->addBody('    return $instance;')
            ->addBody('endif;');

        if ($options->isWithInitializers()) {
            $getMethod
                ->addBody('foreach ($this->initializers as $initializer):')
                ->addBody('    if ($initializer instanceof \Zend\ServiceManager\InitializerInterface):')
                ->addBody('        $initializer->initialize($instance, $this);')
                ->addBody('    else:')
                ->addBody('        call_user_func($initializer, $instance, $this);')
                ->addBody('    endif;')
                ->addBody('    endforeach;')
                ->addBody('')
            ;
        }

        $getMethod
            ->addBody('if (isset($this->shared[$name]) && $this->shared[$name]):')
            ->addBody('    $this->instances[$name] = $instance;')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('return $instance;');

        return $getMethod;
    }

    /**
     * @param ClassType $class
     *
     * @return Method
     */
    private function addHasMethod(ClassType $class): Method
    {
        $hasMethod = $class->addMethod('has')
            ->setVisibility('public')
            ->addComment('@inheritdoc');

        $hasMethod->addParameter('name');
        $hasMethod->addParameter('checkAbstractFactories', true);
        $hasMethod->addParameter('usePeeringServiceManagers', true);

        $hasMethod
            ->addBody('if (is_array($name)):')
            ->addBody('    list(, $name) = $name;')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('if (isset($this->mappings[$name])):')
            ->addBody('    return true;')
            ->addBody('endif;')
            ->addBody('')
            ->addBody(
            'return parent::has($name, $checkAbstractFactories, $usePeeringServiceManagers);'
            );

        return $hasMethod;
    }

    /**
     * @param ClassType $class
     *
     * @return Method
     */
    private function overwriteCanonicalizeName(ClassType $class): Method
    {
        $method = $class->addMethod('canonicalizeName')
            ->setVisibility('protected')
            ->addComment('@inheritdoc');

        $method->addParameter('name');

        $method->addBody('return $name;');

        return $method;
    }

    /**
     * @param ClassType $class
     *
     * @return Method
     */
    private function overwriteCanCreateFromAbstractFactory(ClassType $class): Method
    {
        $class->addProperty('initializedAbstractFactories', false);

        $method = $class->addMethod('canCreateFromAbstractFactory')
                        ->setVisibility('public')
                        ->addComment('@inheritdoc');

        $method->addParameter('cName');
        $method->addParameter('rName');

        $method
            ->addBody('if ($this->initializedAbstractFactories === false):')
            ->addBody('    foreach ($this->registeredAbstractFactories as $factory):')
            ->addBody('        $this->addAbstractFactory($factory);')
            ->addBody('    endforeach;')
            ->addBody('    $this->initializedAbstractFactories = true;')
            ->addBody('endif;')
            ->addBody('')
            ->addBody('return parent::canCreateFromAbstractFactory($cName, $rName);');

        return $method;
    }

    /**
     * @param ClassType $class
     */
    private function addConfigValues(ClassType $class)
    {
        $class
            ->addProperty(
            'invokableClasses',
            $this->serviceManagerConfig['invokables'] ?? []
            )->setVisibility('protected');
        $class
            ->addProperty(
            'factories',
            $this->serviceManagerConfig['factories'] ?? []
            )->setVisibility('protected');
        $class
            ->addProperty(
            'delegators',
            $this->serviceManagerConfig['delegators'] ?? []
            )->setVisibility('protected');
        $class
            ->addProperty(
                'aliases',
                $this->serviceManagerConfig['aliases'] ?? []
            )->setVisibility('protected');
        $class
            ->addProperty(
                'initializers',
                $this->serviceManagerConfig['initializers'] ?? []
            )->setVisibility('protected');
        $class
            ->addProperty(
                'registeredAbstractFactories',
                $this->serviceManagerConfig['abstract_factories'] ?? []
            )->setVisibility('protected');
    }
}