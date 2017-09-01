<?php

namespace Reinfi\OptimizedServiceManager\Service;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use Zend\ModuleManager\Listener\ConfigListener;
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
            ->setVisibility('public');

        $constructor
            ->addBody('parent::__construct();')
            ->addBody('')
            ->addBody('foreach ($this->registeredInitializers as $initializer):')
            ->addBody('    $this->addInitializer($initializer);')
            ->addBody('endforeach;')
            ->addBody('')
            ->addBody('$this->setService(\'' . ServiceManager::class . '\', $this);');
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     */
    public function addOverwriteMethods(ClassType $class, Options $options)
    {
        $this->addGetMethod($class, $options);
        $this->addInteralGetMethod($class, $options);
        $this->addHasMethod($class);
        $this->overwriteCanonicalizeName($class, $options);
        $this->overwriteCanCreateFromAbstractFactory($class);
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
            ->addBody('return $this->internalGet($name, $usePeeringServiceManagers);');


        return $getMethod;
    }

    /**
     * @param ClassType $class
     * @param Options   $options
     *
     * @return Method
     */
    private function addInteralGetMethod(ClassType $class, Options $options): Method
    {
        $getMethod = $class->addMethod('internalGet')
            ->setVisibility('protected')
            ->addComment('@inheritdoc');

        $getMethod->addParameter('name');
        $getMethod->addParameter('usePeeringServiceManagers', true);

        $getMethod
            ->addBody('$instance = null;')
            ->addBody('')
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
     * @param Options   $options
     *
     * @return void
     */
    private function overwriteCanonicalizeName(ClassType $class, Options $options)
    {
        if ($options->isCanonicalizeNames()) {
            return;
        }

        $method = $class->addMethod('canonicalizeName')
            ->setVisibility('protected')
            ->addComment('@inheritdoc');

        $method->addParameter('name');

        $method->addBody('return $name;');
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
}