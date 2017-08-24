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
            ->addBody('if ($container->has(\'config\')):')
            ->addBody('    $config = $container->get(\'config\');')
            ->addBody('')
            ->addBody('    parent::__construct(new \Zend\Mvc\Service\ServiceManagerConfig($config[\'service_manager\'] ?? []));')
            ->addBody('endif;');
    }

    /**
     * @param ClassType $class
     *
     * @return Method
     */
    public function addGetMethod(ClassType $class): Method
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
    public function addHasMethod(ClassType $class): Method
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
}